<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	function get_specials_headers(&$backoffice) {
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			// TODO - Order by freshness?
			$query='SELECT `id`, `name`, `start_date`, `end_date`, `specials_posType_id` FROM `is4c_op`.`specials_headers` WHERE `specials_filterType_id`=1 AND `end_date`>NOW() ORDER BY `start_date`, `name`';
			$result=mysql_query($query, $link);
			if ($result) {
				if (mysql_num_rows($result)==0) {
					array_push($backoffice['status'], 'No specials_headers found...');
					return false;
				} else {
					return $result; 
				}
			} else {
				array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
			}
		} else {
			array_push($backoffice['status'], 'Error connecting to MySQL');
		}
	}
?>