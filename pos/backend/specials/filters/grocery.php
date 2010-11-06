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
			<form action="./" method="post" name="addHeader">
				<fieldset>
					<legend>Add Special</legend>
					<fieldset>
						<div class="form_row">
							<label for="addHeader_name"><span class="accesskey">N</span>ame</label>
							<input accesskey="n" id="addHeader_description" name="addHeader_name" required type="text" value="New Special"/>
						</div>
						<div class="form_row">
							<label for="addHeader_specials_posType_id"><span class="accesskey">M</span>ember Special?</label>
							<input accesskey="m" id="addHeader_specials_posType_id" name="addHeader_specials_posType_id" type="checkbox"/>
						</div>
					</fieldset>
					<fieldset>
						<div class="form_row">
							<label for="addHeader_start_date"><span class="accesskey">S</span>tart Date</label>
							<input accesskey="s" id="addHeader_start_date" name="addHeader_start_date" type="date"/>
						</div>
						<div class="form_row">
							<!-- TODO: Calendar -->
						</div>
					</fieldset>
					<fieldset>
						<div class="form_row">
							<label for="addHeader_end_date"><span class="accesskey">E</span>nd Date</label>
							<input accesskey="e" id="addHeader_end_date" name="addHeader_end_date" type="date"/>
						</div>
						<div class="form_row">
							<!-- TODO: Calendar -->
						</div>
					</fieldset>
					<fieldset>
						<input type="hidden" name="a" value="addHeader"/>
						<input type="hidden" name="filter" value="1"/>
						<input type="submit" value="Add Special"/>
					</fieldset>
				</fieldset>
			</form>';
	
	$html_bottom='
		<table border=1>';
	
	while ($row=mysql_fetch_array($specials_headers_result)) {
		$html_bottom.='
			<tr>
				<td>'.$row['name'].'</td>
				<td>'.$row['start_date'].'</td>
				<td>'.$row['end_date'].'</td>
				<td>'.$row['specials_posType_id'].'</td>
			</tr>';
	}
	
	$html_bottom.='
		</table>';
?>