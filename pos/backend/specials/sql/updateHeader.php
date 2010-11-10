<?php
	function updateHeader(&$backoffice) {
		/*
		 * TODO Sanitize input
		 * TODO Change target database from define.conf
		 * TODO Expand to handle filterType_id as a parameter
		 * TODO Show warnings and partial failure messages
		 */

		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			// Need a link for mysql_real_escape_string
			$tName=mysql_real_escape_string($_REQUEST['updateHeader_name'], $link);
			$tStart_date=strftime("%F", strtotime($_REQUEST['updateHeader_start_date']));
			$tEnd_date=strftime("%F", strtotime($_REQUEST['updateHeader_end_date']));
			$tSpecials_posType_id=($_REQUEST['updateHeader_specials_posType_id']?'2':'1');
			$tSpecials_filterType_id=1;
			$tWhomodified='Specials transition: '.$_SERVER['REMOTE_ADDR'];
			$tModified='NOW()';
			
			$tId=$_REQUEST['id'];

			$query='UPDATE `is4c_op`.`specials_headers` SET 
	        	`specials_headers`.`name`=\''.$tName.'\',
	        	`specials_headers`.`start_date`=\''.$tStart_date.'\',
	        	`specials_headers`.`end_date`=\''.$tEnd_date.'\',
	        	`specials_headers`.`specials_posType_id`='.$tSpecials_posType_id.',
	        	`specials_headers`.`specials_filterType_id`='.$tSpecials_filterType_id.',
	        	`specials_headers`.`whomodified`=\''.$tWhomodified.'\',
	        	`specials_headers`.`modified`='.$tModified.'
	        WHERE `specials_headers`.`id`='.$tId.' LIMIT 1';
	
	        $result=mysql_query($query, $link);
			if ($result) {
				array_push($backoffice['status'], 'Updated specials header');
			} else {
				array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
				array_push($backoffice['status'], $query);
			}	
		} else {
			array_push($backoffice['status'], 'Error connecting to MySQL');
		} 
	}
?>