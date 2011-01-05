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

	// Contains SQL functions for specials page
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
		
		
		// Hmm, maybe the switch between view all and view active should be a get variable and not passed through the same deleteproducts form
		if (isset($_REQUEST['headers']) && $_REQUEST['headers']=='all') {
			$tHeaders='all';
			$tHeaders_target='active';
		} else {
			$tHeaders='active';
			$tHeaders_target='all';
		}
		
		$html_top='
				<ul class="ul_breadcrumb">
					<li><a href="./?filter=1">Grocery Specials</a></li>
				</ul>';
		
		// Hmm, that's kinda ugly using $tA to define so many DOM elements
		$html_top.='
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
								<input onclick="this.form.headers.value=\''.$tHeaders_target.'\'; this.form.submit();" type="button" value="Show '.ucfirst($tHeaders_target).'"/>
								<input type="submit" value="Delete Selected"/>
							</td>
						</tr>
					</tfoot>
					<tbody>';
		
		$specialsList_result=get_specialsList($backoffice, $tHeaders);
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
				</table>
				<input type="hidden" name="a" value="deleteHeaders"/>
				<input type="hidden" name="filter" value="1"/>
				<input type="hidden" name="v" value="headers"/>
				<input type="hidden" name="headers" value="'.$tHeaders.'"/>
			</form>';
	} else if ($view=='products') {
		// TODO: $_REQUEST['id'] validation
		require_once($_SERVER["DOCUMENT_ROOT"].'/lib/table_specials_headers.php');
		$specials_headers_result=get_specials_headers($backoffice, $_REQUEST['id']);
		if ($specials_headers_result) {
			$row=mysql_fetch_array($specials_headers_result);	
		} else {
			// TODO: Send page back?
		}
		
		// TODO: Change filter to db
		$html_top='
				<ul class="ul_breadcrumb">
					<li><a href="./?filter=1">Grocery Specials</a></li>
					<li><a href="./?filter=1&v=products&id='.$_REQUEST['id'].'">'.$row['name'].'</a>: <em>'.strftime("%F", strtotime($row['start_date'])).' to '.strftime("%F", strtotime($row['end_date'])).'</em></li>
				</ul>';

		if (isset($_REQUEST['a']) && $_REQUEST['a']=='searchProducts') {
			$searchProducts_result=searchProducts($backoffice);
			if ($searchProducts_result) {
				$html_top.='<p>Searching for '.$_REQUEST['q'].'</p>';
				$html_top.='<p>Count result = '.count($searchProducts_result).'</p>';
				$html_top.='<pre>'.print_r($searchProducts_result,1).'</pre>';
			} else {
				$html_top.='<p>Error searching for '.$_REQUEST['q'].'</p>';
			}
		} else {
			$html_top.='
				<form action="./" method="post" name="searchProducts">
					<fieldset>
						<legend>Seach</legend>
						<label for="searchProducts_q">Item <span class="accesskey">S</span>KU, PLU, UPC or description: </label>
						<input accesskey="s" id="searchProducts_q" name="q" type="text" value=""/>
						<input name="a" type="hidden" value="searchProducts"/>
						<input name="filter" type="hidden" value="1"/>
						<input name="v" type="hidden" value="products"/>
						<input name="id" type="hidden" value="'.$_REQUEST['id'].'"/>
						<input type="submit" value="Search Products"/>
					</fieldset>
				</form>';
		}
		
		$html_bottom='			
				<form action="./" method="post" name="deleteProducts">
					<table>
						<thead>
							<tr>
								<th>Delete</th>
								<th>CAP</th>
								<th>Vendor</th>
								<th>Item #</th>
								<th>Description</th>
								<th>Brand</th>
								<th>Size</th>
								<th>Price</th>
								<th>Promo Price</th>
								<th>Signs</th>
							</tr>
						</thead>
						<tfoot>
							<tr>
								<td class="textAlignRight" colspan=10>
									<input disabled type="button" value="Print Products"/>
									<input type="submit" value="Delete Selected"/>
								</td>
							</tr>
						</tfoot>
						<tbody>';
		$specialProducts_result=get_specialProducts($backoffice, $_REQUEST['id']);
		// TODO No products found
		while ($row=mysql_fetch_array($specialProducts_result)) {
				// TODO Split on an underscore? Really?
			$tPK=$row['upc'].'_'.$row['specials_header_id'];
			$tCAP=($row['specials_sourceType_id']==1?'YES':'NO');
			$tVendor=$row['vendor_name'];
			$tItemNumber=''; // TODO
			$tDescription=$row['description'];
			$tBrand=$row['brand_name'];
			$tSize=$row['size'];
			$tPrice=$row['normal_price'];
			$tPromoPrice=$row['special_price'];
			$tSigns=$row['labels']; // TODO - Maybe have columns for each type and enumerate through db?

			$html_bottom.='
							<tr>
								<td class="textAlignCenter"><input name="deleteProducts_pk_'.$tPK.'" type="checkbox" value="'.$tPK.'"/></td>
								<td class="textAlignCenter">'.$tCAP.'</td>
								<td>'.$tVendor.'</td>
								<td class="textAlignRight">'.$tItemNumber.'</td>
								<td>'.$tDescription.'</td>
								<td>'.$tBrand.'</td>
								<td class="textAlignRight">'.$tSize.'</td>
								<td class="textAlignRight">'.$tPrice.'</td>
								<td class="textAlignRight">'.$tPromoPrice.'</td>
								<td class="textAlignRight">'.$tSigns.'</td>
							</tr>';
		}
		$html_bottom.='
						</tbody>
					</table>
					<input type="hidden" name="a" value="deleteProducts"/>
					<input type="hidden" name="filter" value="1"/>
					<input type="hidden" name="v" value="products"/>
					<input type="hidden" name="id" value="'.$_REQUEST['id'].'"/>
				</form>';
		
	} else {
		// TODO Redirect home with error?
		$html_top='<p>This should never happen</p>';
		$html_bottom='<p>Ever!</p>';
	}
?>