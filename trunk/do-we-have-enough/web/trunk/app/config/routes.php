<?php
/* SVN FILE: $Id: routes.php 7945 2008-12-19 02:16:01Z gwoo $ */
/**
 * Short description for file.
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different urls to chosen controllers and their actions (functions).
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework (http://www.cakephp.org)
 * Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright     Copyright 2005-2008, Cake Software Foundation, Inc. (http://www.cakefoundation.org)
 * @link          http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.app.config
 * @since         CakePHP(tm) v 0.2.9
 * @version       $Revision: 7945 $
 * @modifiedby    $LastChangedBy: gwoo $
 * @lastmodified  $Date: 2008-12-18 18:16:01 -0800 (Thu, 18 Dec 2008) $
 * @license       http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/views/pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'users', 'action' =>'home'));
/**
 * ...and connect the rest of 'Pages' controller's urls.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));
	
	Router::connect('/create/*', array('controller' => 'users', 'action' => 'create'));
	Router::connect('/save_event/*', array('controller' => 'users', 'action' => 'save_event'));
	Router::connect('/event/*', array('controller' => 'users', 'action' => 'event'));
	Router::connect('/activate_event/*', array('controller' => 'users', 'action' => 'validate_event'));
	Router::connect('/validate_device/*', array('controller' => 'devices', 'action' => 'validate_device'));
	Router::connect('/go_to_event/*', array('controller' => 'users', 'action' => 'validate_user'));
	Router::connect('/event_status/*', array('controller' => 'users', 'action' => 'validate_user'));
	Router::connect('/change_status/*', array('controller' => 'users', 'action' => 'change_status'));
	Router::connect('/change_guests/*', array('controller' => 'users', 'action' => 'change_guests'));
	Router::connect('/current_events', array('controller' => 'users', 'action' => 'current_events'));
	Router::connect('/my_events', array('controller' => 'users', 'action' => 'my_events'));
	Router::connect('/update_name/*', array('controller' => 'users', 'action' => 'update_name'));
	Router::connect('/update_settings/*', array('controller' => 'users', 'action' => 'update_settings'));
	Router::connect('/invite/*', array('controller' => 'users', 'action' => 'invite'));
	Router::connect('/new_invite/*', array('controller' => 'users', 'action' => 'new_invite'));
	Router::connect('/send_sms', array('controller' => 'users', 'action' => 'send_sms'));
	Router::connect('/iamin', array('controller' => 'users', 'action' => 'iamin'));
	Router::connect('/iam50', array('controller' => 'users', 'action' => 'iam50'));
	Router::connect('/plus1', array('controller' => 'users', 'action' => 'plus1'));
	Router::connect('/minus1', array('controller' => 'users', 'action' => 'minus1'));
	Router::connect('/enough', array('controller' => 'users', 'action' => 'enough'));
	Router::connect('/iamout', array('controller' => 'users', 'action' => 'iamout'));
	Router::connect('/receive_sms', array('controller' => 'users', 'action' => 'receive_sms'));
	Router::connect('/subscribe_sms', array('controller' => 'users', 'action' => 'subscribe_sms'));
	Router::connect('/unsubscribe/*', array('controller' => 'users', 'action' => 'unsubscribe'));
	Router::connect('/unsubscribe_from_dwhe/*', array('controller' => 'users', 'action' => 'unsubscribe_from_dwhe'));
	Router::connect('/unsubscribe_from_event/*', array('controller' => 'users', 'action' => 'unsubscribe_from_event'));
	Router::connect('/unsubscribe_from_group/*', array('controller' => 'users', 'action' => 'unsubscribe_from_group'));
	Router::connect('/help', array('controller' => 'users', 'action' => 'help'));
	Router::connect('/event_list', array('controller' => 'users', 'action' => 'event_list'));
	Router::parseExtensions('rss', 'xml');
?>