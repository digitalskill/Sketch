<?php
$skip = false;
if(isset($_REQUEST['a']) && adminCheck()){ ?>
	<label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>		
			<?php
			
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['a']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
			
		helper("shoppingcart");
		$r = getData("invoice","*","response_code<>'0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC",$limit);		
		while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
                 <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_REQUEST['a']) > 1 ? intval($_REQUEST['a']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&a=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'failedspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&a=<?php echo $curr+1; ?>" class="button ajaxlink output:'failedspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
<?php
	$skip = true;
}

if(isset($_REQUEST['b']) && adminCheck()){
	?>
    	<label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>
            <?php 
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['b']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response='INTERNET BANKING REQUEST'","invoice_date DESC",$limit);
		while($r->advance()){
		    $deliveryInfo 	= contentToArray($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
            
            <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_REQUEST['b']) > 1 ? intval($_REQUEST['b']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&b=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'internetspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&b=<?php echo $curr+1; ?>" class="button ajaxlink output:'internetspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
	
	<?php
	$skip = true;
}


if(isset($_REQUEST['c']) && adminCheck()){
	?>
	
    <label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>		
			<?php
			
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['c']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
			
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC",$limit);	
		while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
                 <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_REQUEST['c']) > 1 ? intval($_REQUEST['c']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&c=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'creditcspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&c=<?php echo $curr+1; ?>" class="button ajaxlink output:'creditcspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
	<?php
	$skip = true;	
}

if($skip==false){

if(isset($_REQUEST['n']) && adminCheck()){
	if(isset($_GET['pid'])){
		$r = getData("sketch_page","content","page_id=".intval($_GET['pid']));
		$r->advance();
		$data = $r->result;
		$c = contentToArray($r->content);
		if(isset($_GET['stock'])){
			$c['product_stock'] = intval($_GET['stock']);
		}
		if(isset($_GET['cost'])){
			$c['product_price'] = number_format(floatval($_GET['cost']),2,'.',',');
		}
		
		$data['content'] = serialize($c);
		setData("sketch_page",$data,"WHERE page_id=".$_GET['pid']);
		exit();	
	}
		$limit = "";
		$pagelimit = 10;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " limit 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['n']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " limit ".$startfrom.",".$pagelimit;	
		}
		$xtra = "";
		$name = "";
		if(isset($_GET['name']) && trim($_GET['name']) != ''){
			$name = trim(sketch("db")->quote(trim($_GET['name'])),"'");
			$xtra = " AND (menu_name like '%".$name."%' or content like '%".$name."%' )";
		}
		if(isset($_POST['name']) && trim($_POST['name']) != ''){
			$name = trim(sketch("db")->quote(trim($_POST['name'])),"'");
			$xtra = " AND (menu_name like '%".$name."%' or content like '%".$name."%' )";
		}
		?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr class="tblhead">
                        <td>Product name</td>
                        <td>Amount in Stock</td>
                        <td>Item Cost</td>
                        <td>Amount Sold</td>
                        </tr>
        	<?php 
				$prod = getData("sketch_page,sketch_menu","*","page_type='product' ".$xtra,"menu_under,sketch_menu_id ".$limit);
				while($prod->advance()){
					$c = contentToArray($prod->content);
					$inv = getData("invoice","*","response_code=0 AND invoice_details LIKE '%".$prod->page_id."%'");
					$amountsold = 0;
					while($inv->advance()){
						$amounts = contentToArray($inv->invoice_details);
						if(isset($amounts['itembreakdown'][$prod->page_id][0])){
							$amountsold += intval($amounts['itembreakdown'][$prod->page_id][0]);
						}
					}
					?><tr><td><?php echo $prod->menu_name; ?></td><td><input type="text" value="<?php echo $c['product_stock']; ?>" class="ajstock" rel="<?php echo $prod->page_id; ?>" /></td><td><input type="text" class="ajcost" value="<?php echo $c['product_price'];?>" rel="<?php echo $prod->page_id; ?>" /></td><td><?php echo $amountsold; ?></td></tr><?php				
				}
			?>
            </table>
            
            <?php
		$SQL = end(explode("FROM",$prod->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(sketch_menu_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_POST['n']) > 1 ? intval($_POST['n']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&n=<?php echo $j+1; ?>&name=<?php echo $name; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'memberlistform'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&n=<?php echo $curr+1; ?>&name=<?php echo $name; ?>" class="button ajaxlink output:'memberlistform'">&raquo;</a></li>
		<?php } ?>
        </ul>
        <?php
}else{
?>
<ul class="form" style="float:right;width:28%;">
    <li>
	<label>Instructions for setting up E-commerce</label>
	<div class="instruction-box">
	    <p><strong style="font-size:12px;">First you will need a DPS or PayPal account : </strong><br />You can get one <a href="http://www.paypal.com" target="_blank"> from paypal</a> or from <a href="http://www.paymentexpress.com/index.html" target="_blank">DPS services</a></p>
	    <p><strong style="font-size:12px;">Both types will require codes</strong><br/>Get your login and code numbers fro completing your account on your select payment site.<p>
	    <p><strong style="font-size:12px;">Enter these numbers into the form.</strong><br />Select the section of the form and complete the setup.</p>
	</div>
    </li>
</ul>
<ul class="form accordian" style="float:left;width:70%">
    <li><div class="content-column">
	    <div class="title">E-Commerce settings</div>
	</div></li>
    <li>
	    <a class="button accord-title"><span class="icons downarrow"></span>Check out settings</a>
	    <div class="accord-body">
	    <div class="accord-container">
	    <label>Checkout Page</label>
	    <select name="checkoutpage" class="bgClass:'select_bg'">
		<option value="">None (top Level page)</option>
	    <?php
	    $r        = getData( "sketch_menu,sketch_page", "menu_guid", "WHERE sketch_menu_id <> 25 AND menu_under <> 25 AND page_type='checkout' AND sketch_settings_id=" . sketch( "siteid" ), "ORDER BY menu_guid,menu_name" );
	    while ( $r->advance() ){
?>
		<option value="<?php echo $r->menu_guid;?>" <?php echo $r->menu_guid==$this->e("checkoutpage")? 'selected="selected"' : ""; ?>><?php echo $r->menu_guid; ?></option>
<?php
	    }
?>
            </select>
	    <label>Terms and conditions page</label>
	    <select name="terms" class="bgClass:'select_bg'">
		<option value="">None (Most gateways require a terms page)</option>
	    <?php 
		$r        = getData( "sketch_menu,sketch_page", "menu_guid", "WHERE sketch_menu_id <> 25 AND menu_under <> 25 AND sketch_settings_id=" . sketch( "siteid" ), "ORDER BY menu_guid,menu_name" );
		while ( $r->advance() ){ ?>
		    <option value="<?php echo $r->menu_guid;?>" <?php echo $r->menu_guid==$this->e("terms")? 'selected="selected"' : ""; ?>><?php echo $r->menu_guid; ?></option>
<?php	    } ?>
	    </select>
	    <label>Membership Page</label>
	    <select name="members" class="bgClass:'select_bg'">
		<option value="">None (Members will not be asked to login when placing orders)</option>
	    <?php $r->seek(0);
		while ( $r->advance() ){ ?>
		    <option value="<?php echo $r->menu_guid;?>" <?php echo $r->menu_guid==$this->e("members")? 'selected="selected"' : ""; ?>><?php echo $r->menu_guid; ?></option>
<?php	    } ?>
	    </select>
	    <label>Currency</label>
	    <input type="text" name="currency" value="<?php echo $this->e("currency"); ?>" />
	    <label>GST (If 0 sketch will assume NO GST will be added)</label>
	    <input type="text" name="gst" class="decimal" value="<?php echo $this->e("gst","0.15"); ?>" />
	    <label>Email orders to</label>
	    <input type="text" name="email_address" value="<?php echo $this->e("email_address"); ?>">
	    <label>Email orders from</label>
	    <input type="text" name="email_from" class="required email" value="<?php echo $this->e("email_from"); ?>">
	    </div>
	</div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Countries to show</a>
	    <div class="accord-body">
	    <div class="accord-container">
		<label>Countries,(separate each country by a comma and use a : to add freight prices<br /><em>New Zealand:9.95</em>)</label>
		<textarea name="countries" class="required"><?php echo $this->e("countries","New Zealand:9.95"); ?></textarea>
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Payment Methods</a>
	    <div class="accord-body">
	    <div class="accord-container">
		<label>Online Banking (Customers pay your bank - requires a follow up of each order) and email notice</label>
		<select name="onlinebanking" class="bgClass:'select_bg'">
		<option value="yes" <?php echo $this->e("onlinebanking")=="yes"  ? 'selected="selected"' : ""; ?>>Yes</option>
		<option value="no"  <?php echo $this->e("onlinebanking")=="no"  ? 'selected="selected"' : ""; ?>>No - Hide this option</option>
		<option value="email"  <?php echo $this->e("onlinebanking")=="email"  ? 'selected="selected"' : ""; ?>>No - Show option but Just send an email</option>
		</select>
		<label>Credit Card Processing</label>
		<select name="cctypes" class="bgClass:'select_bg'">
		<option value="">None</option>
		<option value="dps"	    <?php echo $this->e("cctypes")=="dps"  ? 'selected="selected"' : ""; ?>>DPS</option>
		<option value="paypal"	<?php echo $this->e("cctypes")=="paypal"  ? 'selected="selected"' : ""; ?>>PAYPAL</option>
		</select>
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>DPS Options</a>
	    <div class="accord-body">
	    <div class="accord-container">
		<label>Payment Url</label>
		<input type="text" value="<?php echo $this->e('vpcURL',"https://sec.paymentexpress.com/pxpay/pxpay.aspx"); ?>" name="vpcURL" />
		<label>Merchant Id</label>
		<input type="text" value="<?php echo $this->e('vpc_Merchant'); ?>" name="vpc_Merchant"/>
		<label>Secure Secret</label>
		<input type="text" value="<?php echo $this->e('secure_secret'); ?>" name="secure_secret" />
		<label>Order Title</label>
		<input type="text" value="<?php echo $this->e('ordertitle'); ?>" name="ordertitle" />
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>PAYPAL Options</a>
	    <div class="accord-body">
	    <div class="accord-container">
		<label>PayPal username</label>
		<input type="text" value="<?php echo $this->e('payPalUsername'); ?>" name="payPalUsername"/>
		<label>PayPal Password</label>
		<input type="text" value="<?php echo $this->e('payPalPassword'); ?>" name="payPalPassword" />
		<label>PayPal Currency code</label>
		<input type="text" value="<?php echo $this->e('payPalCurrencyCode',"NZD"); ?>" name="payPalCurrencyCode" />

		<label>Signature</label>
		<input type="text" value="<?php echo $this->e('payPalSignature'); ?>" name="payPalSignature" />
		<label>Make Payments Live (Orders cannot be placed until this is set to Yes)</label>
		<select name="payPalLive" class="bgClass:'select_bg'">
		<option value="yes" <?php echo $this->e("payPalLive")=="yes"  ? 'selected="selected"' : ""; ?>>Yes</option>
		<option value="no"  <?php echo $this->e("payPalLive")=="no"  ? 'selected="selected"' : ""; ?>>No</option>
		</select>
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Internet Banking / Email Orders</a>
	    <div class="accord-body">
	    <div class="accord-container" id="internetspot">
       		<label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>
            <?php 
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['b']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response='INTERNET BANKING REQUEST'","invoice_date DESC",$limit);
		while($r->advance()){
		    $deliveryInfo 	= contentToArray($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
            
            <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_POST['b']) > 1 ? intval($_POST['b']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&b=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'internetspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&b=<?php echo $curr+1; ?>" class="button ajaxlink output:'internetspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
            
            
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Credit Card and PayPal Orders</a>
	    <div class="accord-body">
	    <div class="accord-container" id="creditcspot">
          
       		<label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>
            <?php
			
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['c']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}	
			
		 
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC",$limit);
		while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
            
            <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_REQUEST['c']) > 1 ? intval($_REQUEST['c']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&c=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'creditcspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&c=<?php echo $curr+1; ?>" class="button ajaxlink output:'creditcspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
            
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Failed Orders (In case payment has not been captured)</a>
	    <div class="accord-body">
	    <div class="accord-container" id="failedspot">
       		<label>Invoice Details</label>
		<table width="100%" cellpadding="0" cellspacing="0" border="0">
		<tr class="tblhead">
		<td>Type</td>
		<td>Date</td>
		<td>Name</td>
		<td>Email</td>
		<td>Response</td>
		<td>Amount</td>
		<td></td>
		</tr>		
			<?php
			
		$limit = "";
		$pagelimit = 25;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['n']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
			
		helper("shoppingcart");
		$r = getData("invoice","*","response_code<>'0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC",$limit);		
		while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" class="button" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View</a></td>
                    </tr>
            <?php } ?>
            </table>
                 <?php
		$SQL = end(explode("FROM",$r->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(invoice_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_POST['a']) > 1 ? intval($_POST['a']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&a=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'failedspot'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&a=<?php echo $curr+1; ?>" class="button ajaxlink output:'failedspot'">&raquo;</a></li>
		<?php } ?>
        </ul>
	    </div>
	    </div>
    </li>
     <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Product inventory</a>
	    <div class="accord-body">
	    <div class="accord-container">
        <label>Search product records (enter text and press the Enter key)</label>
      <input type="text" name="membersearch" id="memsearch" />
      <script type="text/javascript">
	  	function setMemsearch(){
			$("memsearch").addEvent("keypress",function(event){
				if(event.key=="enter"){
					event.stop();
					$('memberlistform').set("load",{url:'','method':'post'});
					$('memberlistform').load('<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&noform=t&preview="); ?>&n=0&name='+this.value);	
				}
			});
		}
		setMemsearch.delay(500);
	  </script>
      <div id="memberlistform">
      	<?php 
		$limit = "";
		$pagelimit = 10;				// Make this the page output desired
		if($pagelimit > 0){
			$limit = " 0,".$pagelimit;
		}
		$startfrom = (intval(@$_REQUEST['n']) - 1) * $pagelimit;
		$startfrom = ($startfrom < 0)? 0 : $startfrom;
		if($startfrom){
			$limit = " ".$startfrom.",".$pagelimit;	
		}
		$xtra = "";
		?>
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr class="tblhead">
                        <td>Product name</td>
                        <td>Amount in Stock</td>
                        <td>Item Cost</td>
                        <td>Amount Sold</td>
                        </tr>
        	<?php 
				$prod = getData("sketch_page,sketch_menu","*","page_type='product'","menu_under,sketch_menu_id limit ".$limit);
				while($prod->advance()){
					$c = contentToArray($prod->content);
					$inv = getData("invoice","*","response_code=0 AND invoice_details LIKE '%".$prod->page_id.":%'");
					$amountsold = 0;
					while($inv->advance()){
						$amounts = contentToArray($inv->invoice_details);
						if(isset($amounts['itembreakdown'])){
							foreach($amounts['itembreakdown'] as $key => $value){
								list($id,$size,$color) = explode(":",$key);
								if($id==$prod->page_id){
									$amountsold += intval($value[0]);
								}
							}
						}
					}
					?><tr><td><?php echo $prod->menu_name; ?></td><td><input type="text" value="<?php echo $c['product_stock']; ?>" class="ajstock" rel="<?php echo $prod->page_id; ?>" /></td><td><input type="text" class="ajcost" value="<?php echo $c['product_price'];?>" rel="<?php echo $prod->page_id; ?>" /></td><td><?php echo $amountsold; ?></td></tr><?php
				}
			?>
            </table>
            
            <?php
		$SQL = end(explode("FROM",$prod->query));
		list($SQL,) = explode("limit",strtolower($SQL));
		$rowC = ACTIVERECORD::keeprecord("SELECT count(sketch_menu_id) as recordAmount FROM " .$SQL);
		$rowC->advance();
		?>
		<ul class="page-navi" style="clear:both;">
		<?php
		$curr = intval(@$_POST['n']) > 1 ? intval($_POST['n']): 1; 
		for($j=0;$j<($rowC->recordAmount/$pagelimit);$j++){ ?>
			 <li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&n=<?php echo $j+1; ?>" class="button <?php if($j+1==$curr){?>current<?php } ?> ajaxlink output:'memberlistform'" ><?php echo $j+1; ?></a></li><?php
		}
		if(intval($rowC->recordAmount) > ($startfrom + $pagelimit)){ ?>
			<li style="float:left;clear:none;"><a href="<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&preview="); ?>&n=<?php echo $curr+1; ?>" class="button ajaxlink output:'memberlistform'">&raquo;</a></li>
		<?php } ?>
        </ul>
            </div>
        </div>
        </div>
        </li>
</ul>
<?php }
} ?>
<script type="text/javascript">
	function setupInputs(){	
		$$(".ajstock").addEvent("keypress",function(event){
			if(event.key=="enter"){
				event.stop();
				$(this).spin();
				var updatevals = new Request.HTML({'url':'<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&noform=t&preview="); ?>&n=0t&pid=' + $(this).get("rel") + '&stock='+this.value,'method':'get',onComplete: function(){
					$$(".ajstock").each(function(item,index){
						$(item).unspin();
					});
				}});
				updatevals.send();	
			}
		});	
		$$(".ajcost").addEvent("keypress",function(event){
			if(event.key=="enter"){
				event.stop();
				$(this).spin();
				var updatevals = new Request({'url':'<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&noform=t&preview="); ?>&n=0&pid=' + $(this).get("rel") + '&cost='+this.value,'method':'get',onComplete: function(){
					$$(".ajcost").each(function(item,index){
						$(item).unspin();
					});
				}});
				updatevals.send();	
			}
		});	
		
		$$(".ajstock").addEvent("blur",function(event){
				$(this).spin();
				var updatevals = new Request.HTML({'url':'<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&noform=t&preview="); ?>&n=0t&pid=' + $(this).get("rel") + '&stock='+this.value,'method':'get',onComplete: function(){
					$$(".ajstock").each(function(item,index){
						$(item).unspin();
					});
				}});
				updatevals.send();	
		});	
		$$(".ajcost").addEvent("blur",function(event){
				$(this).spin();
				var updatevals = new Request({'url':'<?php echo urlPath("admin/ajax_plugin_shoppingcart?page_id=1&noform=t&preview="); ?>&n=0&pid=' + $(this).get("rel") + '&cost='+this.value,'method':'get',onComplete: function(){
					$$(".ajcost").each(function(item,index){
						$(item).unspin();
					});
				}});
				updatevals.send();	
		});	
		
		$$(".ajaxlink").each(function(item,index){
			new Ajaxlinks(item); 
		});	
	}
	setupInputs.delay(500);
    accordRefresh.delay(500);
</script>