<?php
		$mo = date('m');
        $credentials = "jay@jloop.com:a4d6s5";
        // just a sample below naturally you need to replace this with the right project and taks ids, as you cannot access these.
        //$xml_data = "<request> <notes>qwer</notes> <hours>0.25</hours> <project_id>75406</project_id> <task_id>93182</task_id> <spent_at>Fri, 08 Feb 2008</spent_at> </request>";
        $url = "https://jloop.harvestapp.com/projects?updated_since=2014-".$mo."-01+18%3A30";
        //$url = "https://jloop.harvestapp.com/projects/4380968/entries?from=20140609&to=20140615";

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
            // Show me the result
            //var_dump($data);
            //print_r_($data);
            
            $project_xml = new SimpleXMLElement($data);
            echo "there are: ".count($project_xml->project)." projects";
            foreach ($project_xml->project as $project) {
	            echo $project->name."<br />";
	            echo substr($project>name, 0,2)."<br />";
            }
            //print_r($project_xml);
            curl_close($ch);
        }

?>