<?php
	function searchProducts(&$backoffice) {
		// $_REQUEST['q']

		$link=mysql_connect($_SESSION["mServer"], $_SESSION["mUser"], $_SESSION["mPass"]);
		if ($link) {
			// Search products table for query, include information about existing entries in other specials
			$query=
'SELECT
	`brands`.`name` AS \'brand_name\',
	`products`.`brand_id`,
	`products`.`description`,
	`products`.`normal_price`,
	`products`.`upc`,
	`products`.`vendor_id`,
	`products_vendors`.`name` AS \'products_vendor_name\',
	`specials_headers`.`id` AS \'specials_headers_id\',
	`specials_headers`.`name` AS \'specials_headers_name\',
	`specials_products`.`labelflag`,
	`specials_products`.`special_price`,
	`specials_products`.`specials_header_id`,
	`specials_products`.`specials_sourceType_id`,
	`specials_products`.`vendor_id`,
	`specials_products_vendors`.`name` AS \'specials_products_vendor_name\'
FROM `is4c_op`.`products`
LEFT JOIN `is4c_op`.`specials_products` ON `products`.`upc`=`specials_products`.`upc`
LEFT JOIN `is4c_op`.`specials_headers` ON `specials_products`.`specials_header_id`=`specials_headers`.`id`
LEFT JOIN `is4c_op`.`brands` ON `products`.`brand_id`=`brands`.`id`
LEFT JOIN `is4c_op`.`vendors` `products_vendors` ON `products`.`vendor_id`=`products_vendors`.`id`
LEFT JOIN `is4c_op`.`vendors` `specials_products_vendors` ON `specials_products`.`vendor_id`=`specials_products_vendors`.`id`\
WHERE `products`.`upc` LIKE \'%'.$_REQUEST['q'].'%\' OR `products`.`description` LIKE \'%'.$_REQUEST['q'].'%\'
ORDER BY `products`.`upc` DESC LIMIT 25';
			$result=mysql_query($query, $link);
			if ($result) {
				$num_rows=mysql_affected_rows($link);
				if ($num_rows==0) {
					// Some type of result
				} else if ($num_rows==1) {
					// Some type of result
				} else {
					// Some type of result
				}
			} else {
				array_push($backoffice['status'], 'Error with MySQL query: '.mysql_error($link));
				array_push($backoffice['status'], $query);
			}
		} else {
			array_push($backoffice['status'], 'Error connecting to MySQL');
		}
		
	}
?>