<?php
/**
 * Decoda - Lightweight Markup Language
 *
 * A CakePHP Helper that processes and translates custom markup (BB code style), functionality for word censoring, GeSHi code highlighting.
 *
 * @author 		Miles Johnson - www.milesj.me
 * @copyright	Copyright 2006-2009, Miles Johnson, Inc.
 * @license 	http://www.opensource.org/licenses/mit-license.php - Licensed under The MIT License
 * @link		www.milesj.me/resources/script/decoda
 */

App::import('Vendor', 'Forum.geshi/geshi');

class DecodaHelper extends AppHelper {

	/**
	 * Current version: www.milesj.me/resources/logs/decoda
	 *
	 * @access public
	 * @var string
	 */
	public $version = '2.6';

	/**
	 * Helpers.
	 *
	 * @access public
	 * @var array
	 */
	public $helpers = array('Html', 'Time', 'Cupcake');

	/**
	 * Should we parse codeblocks with geshi?
	 *
	 * @access public
	 * @property boolean
	 */
	public $geshi = true;

	/**
	 * Should non [url] [email] links be clickable?
	 *
	 * @access public
	 * @var boolean
	 */
	public $makeClickable = true;

	/**
	 * Uses shorthand email and url's?
	 *
	 * @access public
	 * @var boolean
	 */
	public $useShorthand = false;

	/**
	 * List of tags allowed to parse.
	 *
	 * @access private
	 * @var array
	 */
	private $__allowed = array();

	/**
	 * Array of words to censor.
	 *
	 * @access private
	 * @var array
	 */
	private $__censored = array('fuck');

	/**
	 * List of options to apply to geshi output.
	 *
	 * @access private
	 * @see setGeshi()
	 * @property array
	 */
	private $__geshiOptions = array(
		'container' 	=> 'pre',
		'line_numbers'	=> false,
		'start_number' 	=> 1,
		'use_css'		=> false,
		'auto_casing'	=> false,
		'tab_width'		=> false,
		'strict_mode'	=> false
	);

	/**
	 * Default markup code.
	 *
	 * @access private
	 * @var array
	 */
	private $__markupCode = array(
		'b'		=> '/\[b\](.*?)\[\/b\]/is',
		'i'		=> '/\[i\](.*?)\[\/i\]/is',
		'u'		=> '/\[u\](.*?)\[\/u\]/is',
		'align'	=> '/\[align=(left|center|right)\](.*?)\[\/align\]/is',
		'float'	=> '/\[float=(left|right)\](.*?)\[\/float\]/is',
		'color'	=> '/\[color=(#[0-9a-fA-F]{6}|[a-z]+)\](.*?)\[\/color\]/is',
		'font'	=> '/\[font=\"(.*?)\"\](.*?)\[\/font\]/is',
		'h16'	=> '/\[h([1-6]{1})\](.*?)\[\/h([1-6]{1})\]/is',
		'size'	=> '/\[size=((?:[1-2]{1})?[0-9]{1})\](.*?)\[\/size\]/is',
		'sub'	=> '/\[sub\](.*?)\[\/sub\]/is',
		'sup'	=> '/\[sup\](.*?)\[\/sup\]/is',
		'hide'	=> '/\[hide\](.*?)\[\/hide\]/is'
	);

	/**
	 * Default markup result.
	 *
	 * @access private
	 * @var array
	 */
	private $__markupResult = array(
		'b'		=> '<b>$1</b>',
		'i'		=> '<i>$1</i>',
		'u'		=> '<u>$1</u>',
		'align'	=> '<div style="text-align: $1">$2</div>',
		'float'	=> '<div class="decoda_float_$1">$2</div>',
		'color'	=> '<span style="color: $1">$2</span>',
		'font'	=> '<span style="font-family: \'$1\', sans-serif;">$2</span>',
		'h16'	=> '<h$1>$2</h$3>',
		'size'	=> '<span style="font-size: $1px">$2</span>',
		'sub'	=> '<sub>$1</sub>',
		'sup'	=> '<sup>$1</sup>',
		'hide'	=> '<span style="display: none">$1</span>'
	);

	/**
	 * Fix missing sub helpers if called with App::import().
	 *
	 * @access public
	 * @uses HtmlHelper, TimeHelper, GeSHi
	 * @param array $settings
	 * @return void
	 */
	public function __construct($settings = array()) {
		parent::__construct();

		if (!empty($settings['geshi']) && is_array($settings['geshi'])) {
			$this->__geshiOptions = array_merge($this->__geshiOptions, $settings['geshi']);
		}

		if (!empty($settings['censored']) && is_array($settings['censored'])) {
			$this->__censored = array_merge($this->__censored, $settings['censored']);
		}

		if (!isset($this->Html)) {
			App::import('Helper', 'Html');
			$this->Html = new HtmlHelper();
		}

		if (!isset($this->Time)) {
			App::import('Helper', 'Time');
			$this->Time = new TimeHelper();
		}

		if (!class_exists('GeSHi')) {
			$this->geshi = false;
		}
	}

	/**
	 * Checks to see if the tag is allowed in the current parse.
	 *
	 * @access public
	 * @param string $tag
	 * @return boolean
	 */
	public function allowed($tag) {
		$allowed = array();
		if (!empty($this->__allowed) && is_array($this->__allowed)) {
			$allowed = $this->__allowed;
		}

		if (empty($allowed)) {
			return true;
		} else if (!in_array($tag, $allowed)) {
			return false;
		}

		return true;
	}

	/**
	 * Processes the string and translate all the markup code.
	 *
	 * @access public
	 * @param string $string
	 * @param boolean $return
	 * @param array $allowed
	 * @return string
	 */
	public function parse($string, $return = false, $allowed = array()) {
		if ($this->geshi === false) {
			$textToParse = htmlentities($string, ENT_NOQUOTES, 'UTF-8');
		} else {
			$textToParse = $string;
		}

		if (!empty($allowed) && is_array($allowed)) {
			$this->__allowed = array_merge($this->__allowed, array_unique($allowed));
		} else {
			$this->__allowed = array();
		}

		if ((strpos($string, '[') === false) && (strpos($string, ']') === false)) {
			$string = nl2br($textToParse);

		} else {
			// Replace standard markup
			$string = ' '. $textToParse;
			$string = nl2br($string);

			// Convert code
			if ($this->allowed('code')) {
				$string = preg_replace_callback('/\[code(?:\slang=\"([-_\sa-zA-Z0-9]+)\")?(?:\shl=\"([0-9,]+)\")?\](.*?)\[\/code\]/is', array($this, '__callbackCode'), $string);
			}

			// Determine which tags can be parsed from the defaults
			$string = $this->__parseDefaults($string);

			// Replace images and apply width/height attributes
			if ($this->allowed('img')) {
				$string = preg_replace_callback('/\[img(?:\swidth=([0-9%]{1,4}+))?(?:\sheight=([0-9%]{1,4}+))?\]((?:ftp|http)s?:\/\/.*?)\[\/img\]/is', array($this, '__processImgs'), $string);
			}

			// Replace divs and apply ids/classes
			if ($this->allowed('div')) {
				$string = preg_replace_callback('/\[div(?:\sid=\"([a-zA-Z0-9]+)\")?(?:\sclass=\"([a-zA-Z0-9\s]+)\")?\](.*?)\[\/div\]/is', array($this, '__processDivs'), $string);
			}

			// Replace and encode urls; allows http(s), ftp(s), irc
			if ($this->allowed('url')) {
				$string = preg_replace_callback('/\[url\]((?:http|ftp|irc)s?:\/\/.*?)\[\/url\]/is', array($this, '__processUrls'), $string);
				$string = preg_replace_callback('/\[url=((?:http|ftp|irc)s?:\/\/.*?)\](.*?)\[\/url\]/is', array($this, '__processUrls'), $string);
			}

			// Replace and obfuscate emails
			if ($this->allowed('email')) {
				$string = preg_replace_callback('/\[e?mail\](.*?)\[\/e?mail\]/is', array($this, '__processEmails'), $string);
				$string = preg_replace_callback('/\[e?mail=(.*?)\](.*?)\[\/e?mail\]/is', array($this, '__processEmails'), $string);
			}

			// Make urls/emails clickable
			if ($this->makeClickable === true) {
				$string = $this->__clickable($string);
			}

			// Build quotes and lists
			if ($this->allowed('quote')) {
				$string = preg_replace_callback('/\[quote(?:=\"(.*?)\")?(?:\sdate=\"(.*?)\")?\](.*?)\[\/quote\]/is', array($this, '__processQuotes'), $string);
			}

			if ($this->allowed('list')) {
				$string = $this->__processLists($string);
			}

			// Clean linebreaks and fix codeblocks
			if ($this->allowed('code')) {
				$string = preg_replace_callback('/\[newcode(?:\slang=\"([-_\sa-zA-Z0-9]+)\")?(?:\shl=\"([0-9,]+)\")?\](.*?)\[\/newcode\]/is', array($this, '__processCode'), $string);
			}

			// Clean linebreaks
			$string = $this->__cleanLineBreaks($string);

			// Censor
			if (!empty($this->__censored)) {
				$string = $this->__parseCensored($string);
			}
		}

		if ($return === false) {
			echo $string;
		} else {
			return $string;
		}
	}

	/**
	 * Removes all decoda markup from a string.
	 *
	 * @access public
	 * @param string $string
	 * @param string $tag
	 * @return string
	 */
	public function removeCode($string, $tag = 'b') {
		if (empty($tag)) {
			return false;
		}

	    return preg_replace_callback('/\['. $tag .'\](.*?)\[\/'. $tag .'\]/is', create_function(
			'$matches', 'return $matches[1];'
		), $string);
	}

	/**
	 * Censors a blacklisted word and replaces with *.
	 *
	 * @param array $matches
	 * @return string
	 */
	private function __callbackCensored($matches) {
		$length = mb_strlen($matches[0]);
		$censored = '';

		for ($i = 1; $i <= $length; ++$i) {
			$censored .= '*';
		}

		return $censored;
	}

	/**
	 * Preformat code so that it doesn't get converted.
	 *
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	private function __callbackCode($matches) {
		$attributes = '';

		if (!empty($matches[1])) {
			$attributes .= ' lang="'. $matches[1] .'"';
		} else {
			$matches[3] = preg_replace('/(&lt;br \/?&gt;)/is', '', htmlentities($matches[3], ENT_NOQUOTES, 'UTF-8'));
		}

		if (!empty($matches[2])) {
			$attributes .= ' hl="'. $matches[2] .'"';
		}

		$return = '[newcode'. $attributes .']'. base64_encode($matches[3]) .'[/newcode]';
		return $return;
	}

	/**
	 * Callback for email processing.
	 *
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	private function __callbackEmails($matches) {
		return $this->__processEmails($matches, true);
	}

	/**
	 * Callback for url processing.
	 *
	 * @access private
	 * @param array $matches
	 * @return void
	 */
	private function __callbackUrls($matches) {
		return $this->__processUrls($matches, true);
	}

	/**
	 * Remove <br />s within [code] and [list].
	 *
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function __cleanLinebreaks($string) {
		/*$string = preg_replace('/<pre>(.*?)<\/pre>/ise', "'<pre class=\"decoda_code\">'. strip_tags(preg_replace('/(<br \/?>)/is', '', '\\1')) . '</pre>'", $string);*/
		$string = str_replace('</li><br />', '</li>', $string);
		$string = str_replace('<ul><br />', '<ul class="decoda_list">', $string);

		return $string;
	}

	/**
	 * Makes links and emails clickable that dont have the [url] or [mail] tags.
	 *
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function __clickable($string) {
		$string = preg_replace('#(script|about|applet|activex|chrome):#is', "\\1&#058;", $string);

		// Matches a link that begins with http(s)://, ftp(s)://, irc://, www.
		if ($this->allowed('url')) {
			$string = preg_replace_callback("#(^|[\n ])(?:http|ftp|irc)s?:\/\/(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,4}(?:[-a-zA-Z0-9._\/&=+%?;\#]+)#is", array($this, '__callbackUrls'), $string);
			$string = preg_replace_callback("#(^|[\n ])www\.(?:[-a-zA-Z0-9._\/&=+;%?\#]+)#is", array($this, '__callbackUrls'), $string);
		}

		// Matches an email@domain.tld
		if ($this->allowed('email')) {
			$string = preg_replace_callback("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", array($this, '__callbackEmails'), $string);
		}

		return $string;
	}

	/**
	 * Parses the text and censors words.
	 *
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function __parseCensored($string) {
		foreach ($this->__censored as $word) {
			$string = preg_replace_callback('/'. $word .'/i', array($this, '__callbackCensored'), $string);
		}

		return $string;
	}

	/**
	 * Parse the default markup depending on the allowed.
	 *
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function __parseDefaults($string) {
		if (empty($this->__allowed)) {
			$code = $this->__markupCode;
			$result = $this->__markupResult;
		} else {
			$code = array();
			$result = array();
			foreach ($this->__markupCode as $tag => $regex) {
				if (in_array($tag, $this->__allowed)) {
					$code[$tag] = $this->__markupCode[$tag];
					$result[$tag] = $this->__markupResult[$tag];
				}
			}
		}

		$string = preg_replace($code, $result, $string);
		return $string;
	}

	/**
	 * Processes and replaces codeblocks / applies geshi if enabled.
	 *
	 * @access private
	 * @uses GeSHi
	 * @param array $matches
	 * @return $string
	 */
	private function __processCode($matches) {
		$language 	= (!empty($matches[1])) ? mb_strtolower($matches[1]) : '';
		$highlight 	= (!empty($matches[2])) ? explode(',', $matches[2]) : '';
		$code = preg_replace('/(<br \/?>)/is', '', base64_decode($matches[3]));

		if (empty($language) || $this->geshi === false) {
			$codeBlock = '<pre class="decoda_code">'. $code .'</pre>';
		} else {
			$this->Geshi = new GeSHi($code, $language);
			$this->__processGeshi($highlight);
			$codeBlock = $this->Geshi->parse_code();

			if ($error = $this->Geshi->error()) {
				trigger_error('Decoda::__processCode(): '. $error, E_USER_WARNING);
			}
		}

		return $codeBlock;
	}

	/**
	 * Processes div tags and allows optional attributes for id/class.
	 *
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	private function __processDivs($matches) {
		$textBlock 	= trim($matches[3]);
		$divId	  	= trim($matches[1]);
		$divClass 	= trim($matches[2]);
		$attributes = array();

		// Add id if available
		if (!empty($divId)) {
			$attributes['id'] = $divId;
		}

		// Add classes if available
		$class = (!empty($divClass)) ? $divClass : NULL;

		return $this->Html->div($class, $textBlock, $attributes);
	}

	/**
	 * Processes, obfuscates and replaces email tags.
	 *
	 * @access private
	 * @param array $matches
	 * @param bool $isCallback
	 * @return string
	 */
	private function __processEmails($matches, $isCallback = false) {
		if ($isCallback === true) {
			$email = trim($matches[2]) .'@'. trim($matches[3]);
			$padding = $matches[1];
		} else {
			$email = trim($matches[1]);
			$emailText = (isset($matches[2])) ? $matches[2] : '';
			$padding = '';
		}

		// Obfuscates the email using ASCII alternatives
		$encrypted = '';
		for ($i = 0; $i < mb_strlen($email); ++$i) {
			$letter = mb_substr($email, $i, 1);
			$encrypted .= '&#'. ord($letter) .';';
		}

		if ($this->useShorthand === true) {
			$emailStr = $padding .'['. $this->Html->link(__('mail', true), 'mailto:'. $email, array('title' => 'Email me!', 'escape' => false)) .']';
		} else {
			$emailStr = $padding . $this->Html->link((!empty($emailText) ? trim($emailText) : $encrypted), 'mailto:'. $email, array('title' => 'Email me!', 'escape' => false));
		}

		return $emailStr;
	}

	/**
	 * Apply the custom settings to the geshi output.
	 *
	 * @access private
	 * @uses GeSHi
	 * @param array $highlight
	 * @return void
	 */
	private function __processGeshi($highlight) {
		$options = $this->__geshiOptions;

		if (isset($this->Geshi)) {
			$this->Geshi->set_overall_style(null, false);
			$this->Geshi->set_encoding('UTF-8');
			$this->Geshi->set_overall_class('decoda_code');

			// Container
			switch ($options['container']) {
				case 'pre':		$container = GESHI_HEADER_PRE; break;
				case 'div':		$container = GESHI_HEADER_DIV; break;
				case 'table':	$container = GESHI_HEADER_PRE_TABLE; break;
				default:		$container = GESHI_HEADER_NONE; break;
			}

			$this->Geshi->set_header_type($container);

			// Line numbers
			if ($options['line_numbers'] == 'fancy') {
				$lineNumbers = GESHI_FANCY_LINE_NUMBERS;
			} else if ($options['line_numbers'] === true) {
				$lineNumbers = GESHI_NORMAL_LINE_NUMBERS;
			} else {
				$lineNumbers = GESHI_NO_LINE_NUMBERS;
			}

			$this->Geshi->enable_line_numbers($lineNumbers);

			if (is_numeric($options['start_number']) && $options['start_number'] >= 0) {
				$this->Geshi->start_line_numbers_at($options['start_number']);
			}

			// CSS / Spans
			if (is_bool($options['use_css'])) {
				$this->Geshi->enable_classes($options['use_css']);
			}

			// Auto-Casing
			switch ($options['auto_casing']) {
				case 'upper': $casing = GESHI_CAPS_UPPER; break;
				case 'lower': $casing = GESHI_CAPS_LOWER; break;
				default: 	  $casing = GESHI_CAPS_NO_CHANGE; break;
			}

			$this->Geshi->set_case_keywords($casing);

			// Tab width
			if ($options['container'] == GESHI_HEADER_DIV) {
				if (is_numeric($options['tab_width']) && $options['tab_width'] >= 0) {
					$this->Geshi->set_tab_width($options['tab_width']);
				}
			}

			// Strict mode
			if (is_bool($options['strict_mode'])) {
				$this->Geshi->enable_strict_mode($options['strict_mode']);
			}

			// Highlight lines
			if (is_array($highlight)) {
				$this->Geshi->highlight_lines_extra($highlight);
			}
		}
	}

	/**
	 * Processes img tags and allows optional attributes for width/height.
	 *
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	private function __processImgs($matches) {
		$imgPath = trim($matches[3]);
		$width	 = trim($matches[1]);
		$height  = trim($matches[2]);
		$imgExt  = strtolower(str_replace('.', '', mb_strrchr($imgPath, '.')));

		// If the image extension is allowed
		if (in_array($imgExt, array('gif', 'jpg', 'jpeg', 'png', 'bmp'))) {
			$attributes = array('alt' => '', 'escape' => true);

			// Add width, is it a percent?
			if (mb_substr($width, -1) == '%') {
				$width = trim($width, '%');
				$widthPercent = '%';
			} else {
				$widthPercent = '';
			}

			if (is_numeric($width) && $width > 0) {
				$attributes['width'] = $width . $widthPercent;
			}

			// Add height, is it a percent?
			if (mb_substr($height, -1) == '%') {
				$height = str_replace('%', '', $height);
				$heightPercent = '%';
			} else {
				$heightPercent = '';
			}

			if (is_numeric($height) && $height > 0) {
				$attributes['height'] = $height . $heightPercent;
			}

			$imgStr = $this->Html->image($imgPath, $attributes);
		} else {
			$imgStr = $imgPath;
		}

		return $imgStr;
	}

	/**
	 * Processes unordered lists.
	 *
	 * @access private
	 * @param string $string
	 * @return string
	 */
	private function __processLists($string) {
		preg_match_all('/\[list\]/i', $string, $matches);
		$openTags = count($matches[0]);

		preg_match_all('/\[\/list\]/i', $string, $matches);
		$closeTags = count($matches[0]);

		$unclosed = $openTags - $closeTags;
		if ($unclosed > 0) {
			for ($i = 0; $i < $unclosed; $i++) {
				$string .= '[/list]';
			}
		}

		$string = str_replace('[list]', '<ul>', $string);
		$string = str_replace('[/list]', '</ul>', $string);
		$string = preg_replace('/\[li\](.*?)\[\/li\]/is', '<li>$1</li>', $string);

		return $string;
	}

	/**
	 * Processes and replaces nested quote tags.
	 *
	 * @access private
	 * @param array $matches
	 * @return string
	 */
	private function __processQuotes($matches) {
		$quote = '<blockquote class="decoda_quote">';

		if (!empty($matches[2])) {
			$date = sprintf('<span class="decoda_quoteDate">%s</span>', $this->Time->niceShort($matches[2], $this->Cupcake->timezone()));
		} else {
			$date = '';
		}

		$quote .= sprintf('<div class="decoda_quoteAuthor">%sQuote by %s</div>', $date, $matches[1]);
		$quote .= sprintf('<div class="decoda_quoteBody">%s</div>', $matches[3]);
		$quote .= '</blockquote>';

		return $quote;

		/*preg_match_all('/\[quote(?:=\".*?\")?\s?(?:date=\".*?\")?\]/i', $string, $matches);
		$openTags = count($matches[0]);

		preg_match_all('/\[\/quote\]/i', $string, $matches);
		$closeTags = count($matches[0]);

		$unclosed = $openTags - $closeTags;
		if ($unclosed > 0) {
			for ($i = 0; $i < $unclosed; $i++) {
				$string .= '[/quote]';
			}
		}*/
	}

	/**
	 * Processes and replaces URLs.
	 *
	 * @access private
	 * @param array $matches
	 * @param bool $isCallback
	 * @return $string
	 */
	private function __processUrls($matches, $isCallback = false) {
		if ($isCallback === true) {
			$url = trim($matches[0]);
			$padding = $matches[1];
		} else {
			$url = trim($matches[1]);
			$urlText = (isset($matches[2])) ? $matches[2] : '';
			$padding = '';
		}

		$urlText = (!empty($urlText)) ? $urlText : $url;
		$urlFull = (mb_substr($url, 0, 3) == 'www') ? 'http://'. $url : $url;

		if ($this->useShorthand === true) {
			$urlStr = $padding .'['. $this->Html->link(__('link', true), $urlFull, array('title' => '', 'escape' => false)) .']';
		} else {
			$urlStr = $padding . $this->Html->link($urlText, $urlFull, array('title' => '', 'escape' => false));
		}

		return $urlStr;
	}

}
