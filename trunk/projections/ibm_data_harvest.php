<?php
if(isset($_GET['year'])) {
	$myyear = $_GET['year'];
} else {
	$myyear = "2017";
}

if(isset($_GET['end']) && ($_GET['end'] != "Today") && ($_GET['end'] != "")) {
	$enddate = date($_GET['end'].'1231');
} else {
	$enddate = date("Ymd");
}
	
ini_set('display_errors',1); 
error_reporting(E_ALL);

$credentials = "jay@jloop.com:AEGV2uJHrhhj3";
// just a sample below naturally you need to replace this with the right project and taks ids, as you cannot access these.
//$xml_data = "<request> <notes>qwer</notes> <hours>0.25</hours> <project_id>75406</project_id> <task_id>93182</task_id> <spent_at>Fri, 08 Feb 2008</spent_at> </request>";
//$url = "https://jloop.harvestapp.com/projects?updated_since=2014-".$mo."-01+18%3A30";
//$url = "https://jloop.harvestapp.com/projects/4380968/entries?from=20140609&to=20140615";
$url = "https://jloop.harvestapp.com/projects?client=3381986&updated_since=".$myyear."-01-01";

$headers = array(
	"Content-type: application/xml",
	"Accept: application/xml",
	"Authorization: Basic " . base64_encode($credentials)
);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_USERAGENT, "JLOOP Projections");


curl_setopt($ch, CURLOPT_POST, 0);
//curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_data);

$data = curl_exec($ch);

if (curl_errno($ch)) {
	print "Error: " . curl_error($ch);
} else {
	$clients = simplexml_load_string ( $data );
	//print_r($clients);
	
	
	foreach ($clients->project as $project) {
		$projectHours = 0;
		
		$new_url = "https://jloop.harvestapp.com/projects/".$project->id."/entries?from=".$myyear."0101&to=".$enddate;
		curl_setopt($ch, CURLOPT_URL, $new_url);
		$data2 = curl_exec($ch);

		if (curl_errno($ch)) {
			print "Error: " . curl_error($ch);
		} else {
			$time = simplexml_load_string($data2);
			//print_r($time);
			foreach($time->{'day-entry'} as $dayentry) {
				$projectHours += floatval($dayentry->hours);
			}
			
		}
		if ($projectHours == 0 && floatval($project->budget) == 0) {
			// do nothing
		} else if ($projectHours == 0 && $project->active == "false") {
			// also do nothing
		} else {
			echo $project->name.",";
			echo $project->budget.",";
			echo $projectHours.",";
			echo $project->active;
			echo "\n";
		}
		
		//echo "<br>";
		//echo "******************************<br><br><br>";
	}
}
?>