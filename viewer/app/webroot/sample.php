<?php
/*
//get topics
$ch = curl_init('https://basecamp.com/1801107/api/v1/topics.json');
$header = array('User-Agent: JLOOP File Viewer (chris@jloop.com)');
curl_setopt($ch, CURLOPT_USERPWD, 'chris@jloop.com:Chris#3');
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$json = json_decode($response);

echo '<pre>';
print_r($json);
*/

//https://basecamp.com/1801107/projects/3001382-viewer-test-project/messages/12092985-viewer-files-thread //add /api/v1 to beginning and .json to the end
$ch = curl_init('https://basecamp.com/1801107/api/v1/projects/3001382-viewer-test-project/messages/12092985-viewer-files-thread.json');
$header = array('User-Agent: JLOOP File Viewer (chris@jloop.com)');
curl_setopt($ch, CURLOPT_USERPWD, 'chris@jloop.com:Chris#3');
curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
curl_setopt($ch, CURLOPT_HEADER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
curl_setopt($ch, CURLOPT_TIMEOUT, 30);
$response = curl_exec($ch);
$json = json_decode($response);

echo '<pre>';
//print_r($response);
//print_r($json->comments);

//print_r($json);
//echo '------------------------------------------------------------------------------------<br />';


$the_files = array();
foreach($json->comments as $comment)
{
	//print_r($comment->attachments);
	foreach($comment->attachments as $attachment)
	{
		print_r($attachment);
		echo '<img src="'.$attachment->url.'" />';
	}
}