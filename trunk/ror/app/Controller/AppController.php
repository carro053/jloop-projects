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

}
