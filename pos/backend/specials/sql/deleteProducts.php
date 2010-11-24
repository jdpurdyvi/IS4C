<?php
	function deleteProducts(&$backoffice) {
		/*
		 * TODO Sanitize input
		 * TODO Change target database from define.conf
		 * TODO Show warnings and partial failure messages
		 */
		
		// Loop through the $_REQUEST, add any headers requested from the checkboxes into an array
		$tUpcs=array();
		$tSpecials_header_ids=array();

		foreach ($_REQUEST as $key=>$val) {
			if (substr($key, 0, 18)=='deleteProducts_pk_') {
				$tPK=explode('_', substr($key, 18));
				array_push($tUpcs, $tPK[0]);
				array_push($tSpecials_header_ids, $tPK[1]);
			}
		}
		
		if (count($tUpcs)>0 && count($tSpecials_header_ids)>0) {
			$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
			if ($link) {
	        	$query='DELETE FROM `is4c_op`.`specials_products` WHERE 1=2';
	        	foreach ($tUpcs as $key=>$val) {
	        		$query.='
	OR (`specials_products`.`upc`=\''.$val.'\' AND `specials_products`.`specials_header_id`='.$tSpecials_header_ids[$key].')';
	        	}
	        	
	        	$result=mysql_query($query, $link);
				if ($result) {
					$tRows=mysql_affected_rows($link);
					array_push($backoffice['status'], 'Deleted '.$tRows.' product'.($tRows>1?'s':''));
				} else {
					array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
					array_push($backoffice['status'], $query);
				}	
			} else {
				array_push($backoffice['status'], 'Error connecting to MySQL');
			} 
		} else {
			array_push($backoffice['status'], 'No specials products to delete');
		}
	}
?>