<?php
	/*
	 * TODO Should specialsList be a class that each filter overloads?
	 */

	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	// Returns the list of specials_headers to display
	function get_specialsList(&$backoffice, $status='active') {
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			if ($status=='active') {
				$tWhere='AND `end_date`>NOW()';
			} else {
				$tWhere==''; 
			}
			
			$query='SELECT 
			`specials_headers`.`id`, 
			`specials_headers`.`name`, 
			`specials_headers`.`start_date`, 
			`specials_headers`.`end_date`, 
			`specials_headers`.`specials_posType_id`,
			COUNT(`specials_products`.`upc`) AS \'productCount\' 
			FROM `is4c_op`.`specials_headers`
			LEFT JOIN `is4c_op`.`specials_products` ON `specials_headers`.`id`=`specials_products`.`specials_header_id` 
			WHERE 
				`specials_filterType_id`=1 
				'.$tWhere.'
			GROUP BY
				`specials_headers`.`id`,
				`specials_headers`.`name`,
				`specials_headers`.`start_date`,
				`specials_headers`.`end_date`,
				`specials_headers`.`specials_posType_id`
			ORDER BY `specials_headers`.`end_date` DESC, `specials_headers`.`name`';
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

	// Returns special header information for given id
	function get_specialInfo(&$backoffice, $id) {
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			$query='SELECT 
			`specials_headers`.`id`, 
			`specials_headers`.`name`, 
			`specials_headers`.`start_date`, 
			`specials_headers`.`end_date`, 
			`specials_headers`.`specials_posType_id`
			FROM `is4c_op`.`specials_headers`
			WHERE
				`specials_headers`.`id`='.$id;
			$result=mysql_query($query, $link);
			if ($result) {
				if (mysql_num_rows($result)==0) {
					array_push($backoffice['status'], 'No special found');
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
	
	function get_specialProducts(&$backoffice, $id) {
		/*
		 * For now, just group_concat the list of label names to return to the user.
		 * In the future, they could be broken into different fields and handled better in the filter pages
		 */
		
		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			$query='SELECT
	`specials_products`.`upc`,
	`specials_products`.`specials_header_id`,
	`specials_products`.`specials_sourceType_id`,
	`specials_products`.`vendor_id`,
	`vendors`.`name` AS \'vendor_name\',
	`specials_products`.`labelflag`,
	`specials_products`.`special_price`,
	`products`.`normal_price`,
	`brands`.`name` AS \'brand_name\',
	`products`.`size`,
	`products`.`description`,
	GROUP_CONCAT(CASE WHEN `specials_products`.`labelflag` IS NOT NULL AND `specials_products`.`labelflag`&`tLabelsTypes`.`id`!=0 THEN `tLabelsTypes`.`abbreviation` END ORDER BY `tLabelsTypes`.`abbreviation`) AS \'labels\'
	FROM `is4c_op`.`specials_products`
	LEFT JOIN `is4c_op`.`vendors` ON `specials_products`.`vendor_id`=`vendors`.`id`
	INNER JOIN `is4c_op`.`products` ON `specials_products`.`upc`=`products`.`upc`
	LEFT JOIN `is4c_op`.`brands` ON `products`.`brand_id`=`brands`.`id`
	JOIN ( SELECT `specials_labelTypes`.`id`, `specials_labelTypes`.`abbreviation` FROM `is4c_op`.`specials_labelTypes`) `tLabelsTypes`
	WHERE `specials_products`.`specials_header_id`='.$id.'
	GROUP BY
		`specials_products`.`upc`,
		`specials_products`.`specials_header_id`,
		`specials_products`.`specials_sourceType_id`,
		`specials_products`.`vendor_id`,
		`vendors`.`name`,
		`specials_products`.`labelflag`,
		`specials_products`.`special_price`,
		`products`.`normal_price`,
		`brands`.`name`,
		`products`.`size`,
		`products`.`description`
	ORDER BY `upc`';
			
			$result=mysql_query($query, $link);
			if ($result) {
				if (mysql_num_rows($result)==0) {
					array_push($backoffice['status'], 'No products found...');
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