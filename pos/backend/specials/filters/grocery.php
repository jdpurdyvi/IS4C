<?php
	/*
	 * Used when no filter is given to the application
	 * 
	 * TODO: Can we use the crystalsvg viewmag.png image?
	 * TODO: Disable removing specials_headers with products
	 * TODO: Move table_specials_headers.php & refactor
	 * TODO: Show all | active
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

	require_once($_SERVER["DOCUMENT_ROOT"].'/lib/materialized_specials.php');
		
	if ($view=='headers') {
		if (isset($_REQUEST['a']) && $_REQUEST['a']=='editHeader') {
			if (isset($_REQUEST['id']) && is_numeric($_REQUEST['id'])) {
				$tId=$_REQUEST['id'];
				$specialInfo_result=get_specialInfo($backoffice, $tId);
				$row=mysql_fetch_array($specialInfo_result);

				// temporary action variable.. $tA? hmm... could do better...
				$tA='update';
				
				$tId=$row['id'];
				$tName=$row['name'];
				$tMember_special=($row['specials_posType_id']==1?false:true);
				$tStart_date=strftime("%F", strtotime($row['start_date']));
				$tEnd_date=strftime("%F", strtotime($row['end_date']));
			} else {
				array_push($backoffice['status'], 'Invalid special header');
				
				$tA='add';
				
				$tName='New Special';
				$tMember_special=false;
				$tStart_date='';
				$tEnd_date='';
			}
		} else {
			$tA='add';
				
			$tName='New Special';
			$tMember_special=false;
			$tStart_date='';
			$tEnd_date='';
		}
		
				// Hmm, that's kinda ugly using $tA to define so many DOM elements
		$html_top='
				<form action="./" method="post" name="'.$tA.'Header">
					<fieldset>
						<legend>'.ucfirst($tA).' Special</legend>
						<fieldset>
							<div class="form_row">
								<label for="'.$tA.'Header_name"><span class="accesskey">N</span>ame</label>
								<input accesskey="n" id="'.$tA.'Header_description" name="'.$tA.'Header_name" required type="text" value="'.$tName.'"/>
							</div>
							<div class="form_row">
								<label for="'.$tA.'Header_specials_posType_id"><span class="accesskey">M</span>ember Special?</label>
								<input accesskey="m" '.($tMember_special?'checked ':'').'id="'.$tA.'Header_specials_posType_id" name="'.$tA.'Header_specials_posType_id" type="checkbox"/>
							</div>
						</fieldset>
						<fieldset>
							<div class="form_row">
								<label for="'.$tA.'Header_start_date"><span class="accesskey">S</span>tart Date</label>
								<input accesskey="s" id="'.$tA.'Header_start_date" name="'.$tA.'Header_start_date" type="date" value="'.$tStart_date.'"/>
							</div>
							<div class="form_row">
								<!-- TODO: Calendar -->
							</div>
						</fieldset>
						<fieldset>
							<div class="form_row">
								<label for="'.$tA.'Header_end_date"><span class="accesskey">E</span>nd Date</label>
								<input accesskey="e" id="'.$tA.'Header_end_date" name="'.$tA.'Header_end_date" type="date" value="'.$tEnd_date.'"/>
							</div>
							<div class="form_row">
								<!-- TODO: Calendar -->
							</div>
						</fieldset>
						<fieldset>
							<input name="a" type="hidden" value="'.$tA.'Header"/>
							<input name="filter" type="hidden" value="1"/>'.($tA=='update'?'
							<input name="id" type="hidden" value="'.$tId.'"/>
							':'').'<input name="v" type="hidden" value="headers"/>
							<input type="submit" value="'.ucfirst($tA).' Special"/>
						</fieldset>
					</fieldset>
				</form>';
		
		$html_bottom='
			<form action="./" method="post" name="deleteHeaders">
				<table>
					<thead>
						<tr>
							<th>Delete</th>
							<th>Name</th>
							<th>Start Date</th>
							<th>End Date</th>
							<th>Member Special</th>
						</tr>
					</thead>
					<tfoot>
						<tr>
							<td class="textAlignRight" colspan=5>
								<input disabled type="button" value="Show All"/>
								<input disabled type="button" value="Print Specials"/>
								<input type="submit" value="Delete Selected"/>
							</td>
						</tr>
					</tfoot>
					<tbody>';
		
		$specialsList_result=get_specialsList($backoffice);
		while ($row=mysql_fetch_array($specialsList_result)) {
			$tId=$row['id'];
			$tName=$row['name'];
			$tStart_date=strftime("%F", strtotime($row['start_date']));
			$tEnd_date=strftime("%F", strtotime($row['end_date']));
			$tMember_special=($row['specials_posType_id']==1?'No':'Yes');
					
			$html_bottom.='
						<tr>
							<td class="textAlignCenter"><input name="deleteHeaders_id_'.$tId.'" type="checkbox" value="'.$tId.'"/></td>
							<td>
								<a href="./?a=editHeader&filter=1&id='.$tId.'&v=headers"><img border=0 src="../../src/images/viewmag.png"/></a>
								<a href="./?v=products&filter=1&id='.$tId.'">'.$tName.'</a>
							</td>
							<td class="textAlignCenter">'.$tStart_date.'</td>
							<td class="textAlignCenter">'.$tEnd_date.'</td>
							<td class="textAlignCenter">'.$tMember_special.'</td>
						</tr>';
		}
		
		$html_bottom.='
					</tbody>
					<input type="hidden" name="a" value="deleteHeaders"/>
					<input type="hidden" name="filter" value="1"/>
					<input type="hidden" name="v" value="headers"/>
				</form>
			</table>';
	} else if ($view=='products') {
		if (isset($_REQUEST['a']) && $_REQUEST['a']=='huh') {
			$html_top='<p>Still working on this...</p>';
		} else {
			$html_top='
				<form action="./" method="post" name="searchProducts">
					<fieldset>
						<legend>Seach</legend>
						<label for="searchProducts_q">Item <span class="accesskey">S</span>KU, PLU, UPC or description: </label>
						<input accesskey="s" id="searchProducts_q" name="q" type="text" value=""/>
						<input name="a" type="hidden" value="searchProducts"/>
						<input name="filter" type="hidden" value="1"/>
						<input name="v" type="hidden" value="products"/>
						<input type="submit" value="Search Products"/>
					</fieldset>
				</form>';
		}
		
		$html_bottom='';
		$specialProducts_result=get_specialProducts($backoffice, $_REQUEST['id']);
		while ($row=mysql_fetch_array($specialProducts_result)) {
			$html_bottom.=print_r($row,1);
		}
		$html_bottom.='<p>And this too</p>';
	} else {
		$html_top='<p>This should never happen</p>';
		$html_bottom='<p>Ever!</p>';
	}
?>