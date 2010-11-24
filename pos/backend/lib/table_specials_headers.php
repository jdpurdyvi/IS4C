<?php
	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	function get_specials_headers(&$backoffice, $id=null) {
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			if (isset($id)) {
				$tWhere='WHERE `id`='.$id;
			} else {
				$tWhere='';
			}
			$query='SELECT `id`, `name`, `start_date`, `end_date`, `specials_posType_id`, `specials_filterType_id`, `whomodified`, `modified` FROM `is4c_op`.`specials_headers` '.$tWhere.' ORDER BY `id` DESC';
			$result=mysql_query($query, $link);
			if ($result) {
				if (mysql_num_rows($result)==0) {
					array_push($backoffice['status'], 'No specials found...');
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