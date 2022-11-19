<?php
//setup
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

/// make the calls

  $url = "https://api.harvestapp.com/v2/projects?client_id=3381986&updated_since=".$myyear."-01-01";
  $headers = array(
	"Authorization: Bearer 5217.pt.oGfeU_rAjoaG90v3LRthn4IDkynOPZy_dc3PCB190Srf4fqMrWfIAY9g3Vl57ivNp6VjXbKtky5lqKm6Y1FKkA",
	"Harvest-Account-ID: 127791"
  );

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($handle, CURLOPT_USERAGENT, "jloop1");
  /*
  curl -i \
	-H 'Harvest-Account-ID: 127791'\
	-H 'Authorization: Bearer 5217.pt.vGw4NIAeouQI2rqzC7FG6q4ymr6ss3aZAYNAjNtxUwXcqsquNr8XG7zFZCItEV-dwWi0z4fp47wackA-XNmWkQ'\
	-H 'User-Agent: Harvest API Example' \
	"https://api.harvestapp.com/api/v2/users/me.json"
*/
  $response = curl_exec($handle);

  if (curl_errno($handle)) {
	print "Error: " . curl_error($handle);
  } else {
	//print json_encode(json_decode($response), JSON_PRETTY_PRINT);
	//curl_close($handle);
	$clients = json_decode($response, true);
	//print_r($clients);
	
	//////now get all the time spent for this client for the time period
	
	$new_url = "https://api.harvestapp.com/v2/reports/time/projects?from=".$myyear."0101&to=".$enddate;
	curl_setopt($handle, CURLOPT_URL, $new_url);
	$data2 = curl_exec($handle);
	
	if (curl_errno($handle)) {
		print "Error: " . curl_error($handle);
	} else {
		$project_time = json_decode($data2_true);
		print_r($project_time);
		//foreach($time->{'day-entry'} as $dayentry) {
		//	$projectHours += floatval($dayentry->hours);
		//}
		
	}
	
	///// now loop through all the projects and output
	foreach ($clients['projects'] as $project) {
		//print_r($project);
		//print "------------";
		$projectHours = 0;
		
		if ($projectHours == 0 && floatval($project['budget']) == 0) {
			// do nothing
		} else if ($projectHours == 0 && $project['is_active'] == "false") {
			// also do nothing
		} else {
			echo $project['name'].",";
			echo $project['budget'].",";
			echo $projectHours.",";
			echo $project['is_active'];
			echo "\n";
		}
		
		//echo "<br>";
		//echo "******************************<br><br><br>";
	}
	
}
?>