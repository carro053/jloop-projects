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
		$work = imagecreatetruecolor(156, 97);
		$blank = imagecreatefrompng(WWW_ROOT.'img'.DS.'blank.png');
		$src = imagecreatefrompng($src_name);
		
		$clear = imagecolorallocatealpha(
			$work, //resource image
			255, //int red
			255, //int green
			255, //int blue
			127 //int alpha
		);
		
		imagefilledrectangle(
			$work, //resource image
			0, //int x1
			0, //int y1
			156, //int x2
			97, //int y2
			$clear //int color
		);
		
		imagecopyresized(
			$work, //resource dst_image
			$src, //resource src_image
			8, //int dst_x
			8, //int dst_y
			0, //int src_x
			0, //int src_y
			139, //int dst_w
			79, //int dst_h
			139, //int src_w
			79 //int src_h
		);
		
		imagecopyresized(
			$work, //resource dst_image
			$blank, //resource src_image
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
		imagedestroy($blank);
		imagedestroy($src);
	}
}
