<?php
	function addHeader(&$backoffice) {
		/*
		 * TODO - Sanitize input
		 * TODO - Change target database from define.conf
		 * TODO - Expand to handle filterType_id as a parameter
		 */
		
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			// Need a link for mysql_real_escape_string
			$tName=mysql_real_escape_string($_REQUEST['addHeader_name'], $link);
			$tStart_date=strftime("%F", strtotime($_REQUEST['addHeader_start_date']));
			$tEnd_date=strftime("%F", strtotime($_REQUEST['addHeader_end_date']));
			$tSpecials_posType_id=($_REQUEST['addHeader_specials_posType_id']?'2':'1');
			$tSpecials_filterType_id=1;
			$tWhomodified='Specials transition: '.$_SERVER['REMOTE_ADDR'];
			$tModified='NOW()';
			
			$query='INSERT INTO `is4c_op`.`specials_headers` (`name`, `start_date`, `end_date`, `specials_posType_id`, `specials_filterType_id`, `whomodified`, `modified`) VALUES (
        	\''.$tName.'\',
        	\''.$tStart_date.'\',
        	\''.$tEnd_date.'\',
        	'.$tSpecials_posType_id.',
        	'.$tSpecials_filterType_id.',
        	\''.$tWhomodified.'\',
        	'.$tModified.')';
			
			$result=mysql_query($query, $link);
			if ($result) {
				$specialsHeader_id=mysql_insert_id($link);
				array_push($backoffice['status'], 'Added special <a href="#">'.$tName.'</a>');
			} else {
				array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
				array_push($backoffice['status'], $query);
			}
		} else {
			array_push($backoffice['status'], 'Error connecting to MySQL');
		}
	}
?>