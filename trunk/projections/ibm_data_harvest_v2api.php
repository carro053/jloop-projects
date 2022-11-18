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
	$clients = json_decode($response);
	print_r($clients);
	
	/*
	foreach ($clients->project as $project) {
		$projectHours = 0;
		
		$new_url = "https://api.harvestapp.com/v2/reports/time/projects/".$project->id."?from=".$myyear."0101&to=".$enddate;
		curl_setopt($handle, CURLOPT_URL, $new_url);
		$data2 = curl_exec($handle);
	
		if (curl_errno($ch)) {
			print "Error: " . curl_error($ch);
		} else {
			$time = simplexml_load_string($data2);
			print_r($time);
			//foreach($time->{'day-entry'} as $dayentry) {
			//	$projectHours += floatval($dayentry->hours);
			//}
			
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
	*/
}
?>