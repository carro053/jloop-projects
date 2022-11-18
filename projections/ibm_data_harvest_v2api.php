<?php
  $url = "https://api.harvestapp.com/v2/users/me";
  $headers = array(
	"Authorization: Bearer " . getenv("-ik8BxikAX9sL6fQCCRI8pdNrukzWOnHM5I1G5N9Yallc_0M8lgAEblQWmfsCuIysC1rsKoyOXGXTm9JET364Q"),
	"Harvest-Account-ID: "   . getenv("phZ4rI8r6Djaw0z2mH52sZe2")
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