<?php
class VideosController extends AppController {
	var $name = 'Videos';
	var $helpers = array('Html', 'Session');
	var $uses = array('Video');
}

?>