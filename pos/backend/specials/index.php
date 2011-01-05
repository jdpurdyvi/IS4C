<?php
	/*
	 * The page is a vertical split. 
	 * 
	 * Top is either search/entry for a specials_header or specials_products
	 * Lower is a list of specials_headers or specials_products
	 * 
	 * The different filters are setup to accommodate different users
	 * 
	 * Designed to fit in a 865px box inside of a POS backend
	 */

	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	$backoffice=array();
	$backoffice['status']=array();

	require_once($_SERVER["DOCUMENT_ROOT"].'/src/htmlparts.php');
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='addHeader') {
		require_once('sql/addHeader.php');
			addHeader($backoffice);
	}
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='deleteHeaders') {
		require_once('sql/deleteHeaders.php');
			deleteHeaders($backoffice);
	}
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='updateHeader') {
		require_once('sql/updateHeader.php');
			updateHeader($backoffice);
	}
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='deleteProducts') {
		require_once('sql/deleteProducts.php');
			deleteProducts($backoffice);
	}
	
	if (isset($_REQUEST['a']) && $_REQUEST['a']=='searchProducts') {
		require_once('sql/searchProducts.php');
			// Don't search here, wait until filter page
			// searchProducts($backoffice);
	}
	
	if (isset($_REQUEST['filter'])) {
		// TODO: Sanitize better, turn into db call
		switch ($_REQUEST['filter']) {
			case 1:
				$specials_filter='grocery';
			break;
			case 2:
				$specials_filter='hbc';
			break;
			default:
				$specials_filter='default';
			break;
		}
		
		require_once('./filters/'.$specials_filter.'.php');
	} else {
		require_once('./filters/default.php');
	}
	
	$html=
'<!DOCTYPE HTML>
<html>
	<head>';
	
	$html.=head();
	$html.='
		<link href="specials.css" media="screen" rel="stylesheet" type="text/css"/>
		<script src="specials.js" type="text/javascript"></script>
		<title>Shelf Audit - Specials</title>
	</head>
	<body>';
	
	$html.=body();
	
	$html.='
		<div id="page_panel">';
	
	$html.=$html_top;
	$html.=$html_bottom;
	
	foreach ($backoffice['status'] as $msg) {
		$html.='
				<p class="status">'.$msg.'</p>';
	}
	
	$html.='
		</div>';
		
	$html.=foot();
	
	$html.='
	</body>
</html>';

	print_r($html);
?>