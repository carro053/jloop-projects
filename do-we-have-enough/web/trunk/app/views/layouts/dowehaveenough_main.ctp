<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php if(isset($noindex)) { ?>
<meta name="robots" content="noindex, nofollow">
<?php }else{ ?>
<meta name="robots" content="index, follow">
<?php } ?>
<meta name="description" content="<?php if(isset($meta_description)) { echo $meta_description; }else{ echo 'Do We Have Enough? Need to find out if you have enough players for a ball game... Enough seats to fill a poker table... Enough participants to hold today\'s meeting?'; } ?>">
<meta name="keywords" content="Do we have enough, DWHE, Event Application, Event planner, invite friends to your event, Plan a poker game, coordinate a basketball game, event, planner">
<title>Do We Have Enough?<?php if($page_title != '') echo ' :: '.$page_title; ?></title>

<link rel="shortcut icon" href="/favicon.ico" type="image/x-icon" />
<link rel="stylesheet" href="/css/framework/screen.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/css/framework/print.css" type="text/css" media="print"> 
<link rel="stylesheet" href="/css/framework_override.css" type="text/css" media="screen, projection">
<link rel="stylesheet" href="/css/thickbox.css" type="text/css" media="screen" />
<!--[if IE]><link rel="stylesheet" href="/css/framework/ie.css" type="text/css" media="screen, projection"><![endif]-->
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.form.js"></script>
<script type="text/javascript" src="/js/jquery.jeditable.js"></script>
<script type="text/javascript" src="/js/jquery.easing.js"></script>
<script type="text/javascript" src="/js/thickbox.js"></script>
<script type="text/javascript" src="/js/datepicker.js"></script>
<script type="text/javascript" src="/js/dwhe_functions.js"></script>
<script type="text/javascript" src="/js/jquery.custom.js"></script>
<link href="/css/datepicker.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="/jquery.fancybox/jquery.fancybox.css" media="screen" />
<script type="text/javascript" src="/jquery.fancybox/jquery.fancybox-1.2.1.pack.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		$(".fancy").fancybox({
		
		'overlayOpacity': .75
		
		});
	});
</script>
</head>

<body<?php if($page == 'home') echo ' id="home"'; ?>>

<?php if($page == 'home') {
		echo '<div class="bubbles">';
        echo '<p><span>Need to find out if you have enough players for a ball game&hellip;</span></p>';
		echo '<p><span>Enough seats filled at the poker table&hellip;</span></p>';
		echo '<p><span>Enough participants to hold today\'s meeting?</span></p>';
        echo '</div><!-- end .bubbles -->';
} ?>

<div id="header">
    <h1><a href="/">Do We Have Enough?</a></h1>
</div><!-- end #header -->

<div id="content"<?php if($page == 'event') echo ' class="two-col"'; ?>>
    <?php echo $content_for_layout; ?>
</div><!-- end #content -->

<div id="footer">
	<div class="container">
    	<a class="button my-events" href="<?php if($uAuth->user_id > 0) { echo '/my_events'; }else{ echo '/current_events'; } ?>">my events</a>
        <a href="/help"class="button help">help</a>
        <a href="https://app.e2ma.net/app/view:Join/signupId:66678/acctId:2179" onclick="window.open('https://app.e2ma.net/app/view:Join/signupId:66678/acctId:2179', 'signup', 'menubar=no, location=no, toolbar=no, scrollbars=yes, width=620, height=350'); return false;"><img src="/img/btn_iphone_finalist.gif" width="332" height="85" style="margin: 0 auto 180px;"/></a>
    	<p>&copy;<?php echo date("Y"); ?> <a href="http://www.jloop.com" target="_blank">JLOOP</a></p>
    </div><!-- end .container -->
</div><!-- end #footer -->
<script type="text/javascript" charset="utf-8">
  var is_ssl = ("https:" == document.location.protocol);
  var asset_host = is_ssl ? "https://s3.amazonaws.com/getsatisfaction.com/" : "http://s3.amazonaws.com/getsatisfaction.com/";
  document.write(unescape("%3Cscript src='" + asset_host + "javascripts/feedback-v2.js' type='text/javascript'%3E%3C/script%3E"));
</script>

<script type="text/javascript" charset="utf-8">
  var feedback_widget_options = {};

  feedback_widget_options.display = "overlay";  
  feedback_widget_options.company = "dowehaveenough";
  feedback_widget_options.placement = "left";
  feedback_widget_options.color = "#222";
  feedback_widget_options.style = "question";
  
  
  
  
  
  

  var feedback_widget = new GSFN.feedback_widget(feedback_widget_options);
</script>
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try {
var pageTracker = _gat._getTracker("UA-75090-51");
pageTracker._trackPageview();
} catch(err) {}</script>
</body>
</html>