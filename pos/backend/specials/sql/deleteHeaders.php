<?php
	function deleteHeaders(&$backoffice) {
		/*
		 * TODO Sanitize input
		 * TODO Change target database from define.conf
		 * TODO Expand to handle filterType_id as a parameter
		 * TODO Show warnings and partial failure messages
		 */
		
		// Loop through the $_REQUEST, add any headers requested from the checkboxes into an array
		$tIds=array();

		foreach ($_REQUEST as $key=>$val) {
			if (substr($key, 0, 17)=='deleteHeaders_id_') {
				array_push($tIds, substr($key, 17));
			}
		}
		
		if (count($tIds)>0) {
			$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
			if ($link) {
	        	$query='DELETE FROM `is4c_op`.`specials_headers` WHERE `specials_headers`.`id` IN ('.implode(', ',$tIds).')';
	        	$result=mysql_query($query, $link);
				if ($result) {
					$tRows=mysql_affected_rows($link);
					array_push($backoffice['status'], 'Deleted '.$tRows.' specials header'.($tRows>1?'s':''));
				} else {
					array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
					array_push($backoffice['status'], $query);
				}	
			} else {
				array_push($backoffice['status'], 'Error connecting to MySQL');
			} 
		} else {
			array_push($backoffice['status'], 'No specials headers to delete');
		}
	}
?>