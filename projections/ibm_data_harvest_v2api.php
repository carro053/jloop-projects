<?php
  $url = "https://api.harvestapp.com/v2/users/me";
  $headers = array(
	"Authorization: Bearer " . getenv("5217.pt.vGw4NIAeouQI2rqzC7FG6q4ymr6ss3aZAYNAjNtxUwXcqsquNr8XG7zFZCItEV-dwWi0z4fp47wackA-XNmWkQ"),
	"Harvest-Account-ID: "   . getenv("127791")
  );

  $handle = curl_init();
  curl_setopt($handle, CURLOPT_URL, $url);
  curl_setopt($handle, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($handle, CURLOPT_HTTPHEADER, $headers);
  curl_setopt($handle, CURLOPT_USERAGENT, "JLOOP Projections");

  $response = curl_exec($handle);

  if (curl_errno($handle)) {
	print "Error: " . curl_error($handle);
  } else {
	print json_encode(json_decode($response), JSON_PRETTY_PRINT);
	curl_close($handle);
  }
?>