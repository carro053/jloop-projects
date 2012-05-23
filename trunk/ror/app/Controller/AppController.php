<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {

	function removeInvis($input)
	{
		$replaceArray = array(array(), array()); // this is a replace array for illegal SGML characters;
		for ($i=0; $i<32; $i++)                  // produces a correct XML output
		{
		    $replaceArray[0][] = chr($i);
		    $replaceArray[1][] = "";
		}
		for ($i=127; $i<160; $i++)
		{
		    $replaceArray[0][] = chr($i);
		    $replaceArray[1][] = "";
		}
		$input = str_replace($replaceArray[0], $replaceArray[1], $input); // get rid of illegal SGML chars
		return $input;
	}
	
	function generateAnswerImage($src_name, $dst_name)
	{
		$src_data = list($src_width, $src_height) = getimagesize($src_name);
		
		$work = imagecreatetruecolor(156, 97);
		$fill = imagecreatefrompng(WWW_ROOT.'img'.DS.'templates'.DS.'fill.png');
		$frame = imagecreatefrompng(WWW_ROOT.'img'.DS.'templates'.DS.'frame.png');
		switch($src_data['mime'])
		{
			case 'image/gif':
				$src = imagecreatefromgif($src_name);
				break;
			case 'image/jpeg':
				$src = imagecreatefromjpeg($src_name);
				break;
			case 'image/png':
				$src = imagecreatefrompng($src_name);
				break;
			default:
				die('Error with uploaded image type');
				break;
		}
		
		imagesavealpha($work, true);
		$trans_colour = imagecolorallocatealpha($work, 0, 0, 0, 127);
		imagefill($work, 0, 0, $trans_colour);
		
		imagecopyresized(
			$work, //resource dst_image
			$fill, //resource src_image
			8, //int dst_x
			8, //int dst_y
			0, //int src_x
			0, //int src_y
			139, //int dst_w
			79, //int dst_h
			139, //int src_w
			79 //int src_h
		);
		
		$ratio = 139 / 79;
		$src_ratio = $src_width / $src_height;
		if($src_ratio > $ratio) //too wide
		{
			$src_w = round($src_width * 79 / $src_height);
			$src_h = $src_height;
			$src_x = round(($src_width - $src_w) / 2);
			$src_y = 0;
		}else{ //too tall
			$src_w = $src_width;
			$src_h = round($src_height * $src_width / 139);
			$src_x = 0;
			$src_y = round(($src_height - $src_h) / 2);
		}
		
		echo $ratio."/".$src_ratio."/".($src_h/$src_w);die;
		
		imagecopyresized(
			$work, //resource dst_image
			$src, //resource src_image
			8, //int dst_x
			8, //int dst_y
			$src_x, //int src_x
			$src_y, //int src_y
			139, //int dst_w
			79, //int dst_h
			$src_w, //int src_w
			$src_h //int src_h
		);
		
		imagecopyresized(
			$work, //resource dst_image
			$frame, //resource src_image
			0, //int dst_x
			0, //int dst_y
			0, //int src_x
			0, //int src_y
			156, //int dst_w
			97, //int dst_h
			156, //int src_w
			97 //int src_h
		);
		
		imagepng($work, $dst_name);
		
		imagedestroy($work);
		imagedestroy($fill);
		imagedestroy($frame);
		imagedestroy($src);
	}
}
