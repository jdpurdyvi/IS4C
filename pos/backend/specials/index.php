<?php
	/*
	 * The page is a vertical split. Top half is search/entry, while the lower
	 * portion is a list.
	 * 
	 * Top is either search/entry for a specials_header or specials_products
	 * Lower is a list of specials_headers or specials_products
	 * 
	 * Designed to fit in a 865px box inside of a POS backend
	 */

	require_once($_SERVER["DOCUMENT_ROOT"]."/define.conf");

	$backoffice=array();
	$backoffice['status']=array();

	require_once($_SERVER["DOCUMENT_ROOT"].'/src/htmlparts.php');
	
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
		// TODO: Maybe this should be in some filters/main.php?
		$html_top='
		<ul>
			<li><a href="?filter=1">Grocery</a></li>
			<li><a href="?filter=2">HBC</a></li>
		</ul>';
	
		$html_bottom='';
	}
	
	$html=
'<!DOCTYPE HTML>
<html>
	<head>';
	
	$html.=head();
	$html.='
		<link href="screen.css" media="screen" rel="stylesheet" type="text/css"/>
		<!-- <script src="main.js" type="text/javascript"></script> -->
		<title>Shelf Audit - Specials</title>
	</head>
	<body>';
	
	$html.=body();
	
	$html.=$html_top;
	$html.=$html_bottom;
	
	foreach ($backoffice['status'] as $msg) {
		$html.='
				<p class="status">'.$msg.'</p>';
	}
	
	$html.='
	</body>
</html>';
		
	$html.=foot();
	
	$html.='
	</body>
</html>';

	print_r($html);
?>