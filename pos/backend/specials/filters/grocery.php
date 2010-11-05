<?php
	/*
	 * Used when no filter is given to the application
	 */
	
	if (isset($_REQUEST['v'])) {
		// Change to switch?
		if ($_REQUEST['v']=='headers') {
			$view='headers';
		} else if ($_REQUEST['v']=='products') {
			$view='products';
		} else {
			$view='headers';
		}
	} else {
		$view='headers';
	}

	if ($view='headers') {
		require_once($_SERVER["DOCUMENT_ROOT"].'/lib/table_specials_headers.php');
		$specials_headers_result=get_specials_headers($backoffice);
	}
	
	$html_top='
		<form>
			<label for="addHeader_name"><span class="accesskey">N</span>ame</label>
			<input accesskey="n" id="addHeader_description" name="addHeader_name" type="text" value="New Special"/>
			<span class="accesskey">M</span>ember Special?
			<label for="addHeader_specials_posType_id_1">Yes</label>
			<input accesskey="m" id="addHeader_specials_posType_id_1" name="addHeader_specials_posType_id" type="radio" value="1"/>
			<label for="addHeader_specials_posType_id_2">No</label>
			<input accesskey="m" id="addHeader_specials_posType_id_2" name="addHeader_specials_posType_id" type="radio" value="2"/>
		</form>';
	

	
	$html_bottom='
		<ul>';
	
	while ($row=mysql_fetch_array($specials_headers_result)) {
		$html_bottom.='
			<li>'.$row['name'].', '.$row['start_date'].', '.$row['end_date'].', '.$row['specials_posType_id'].'</li>';
	}
	
	$html_bottom.='
		</ul>';
?>