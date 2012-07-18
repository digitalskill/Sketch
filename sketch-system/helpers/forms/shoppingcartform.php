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
	    <?php $r->seek(0);
		while ( $r->advance() ){ ?>
		    <option value="<?php echo $r->menu_guid;?>" <?php echo $r->menu_guid==$this->e("terms")? 'selected="selected"' : ""; ?>><?php echo $r->menu_guid; ?></option>
<?php	    } ?>
	    </select>
	    <label>Membership Page</label>
	    <select name="members" class="bgClass:'select_bg'">
		<option value="">None (Members will not be asked to login when placing orders)</option>
	    <?php $r->seek(0);
		while ( $r->advance() ){ ?>
		    <option value="<?php echo $r->menu_guid;?>" <?php echo $r->menu_guid==$this->e("terms")? 'selected="selected"' : ""; ?>><?php echo $r->menu_guid; ?></option>
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
		<option value="paypal"	    <?php echo $this->e("cctypes")=="paypal"  ? 'selected="selected"' : ""; ?>>PAYPAL</option>
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
		<label>Payment Url</label>
		<input type="text" value="<?php echo $this->e('paypalURL',"https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token="); ?>" name="paypalURL" />
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
	    <div class="accord-container">
            <?php
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response='INTERNET BANKING REQUEST'","invoice_date DESC","50");
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
		<td>View Order</td>
		</tr>
            <?php while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View Order</a></td>
                    </tr>
            <?php } ?>
            </table>
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Credit Card and PayPal Orders</a>
	    <div class="accord-body">
	    <div class="accord-container">
            <?php
		helper("shoppingcart");
		$r = getData("invoice","*","response_code='0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC","50");
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
		<td>View Order</td>
		</tr>
            <?php while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View Order</a></td>
                    </tr>
            <?php } ?>
            </table>
	    </div>
	    </div>
    </li>
    <li>
	<a class="button accord-title"><span class="icons downarrow"></span>Failed Orders (In case payment has not been captured)</a>
	    <div class="accord-body">
	    <div class="accord-container">
            <?php
		helper("shoppingcart");
		$r = getData("invoice","*","response_code<>'0' AND invoice_response <> 'INTERNET BANKING REQUEST'","invoice_date DESC","50");
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
		<td>View Order</td>
		</tr>
            <?php while($r->advance()){
		    $deliveryInfo 	= unserialize($r->invoice_details);
		    $deliveryInfo = (array)getDeliveryInfo($deliveryInfo);	?>
		    <tr <?php if($r->invoice_filled==1){?>class='filled'<?php }?>>
                    <td><?php if(stripos($r->invoice_response,"banking request")!==false){?>Internet Banking<?php }else{?>Credit Card<?php } ?></td>
                    <td><?php echo $r->invoice_date; ?></td>
                    <td><?php echo $deliveryInfo['firstname']. " ". $deliveryInfo['lastname']; ?></td>
                    <td><a href="mailto:<?php echo $deliveryInfo['email']; ?>" class="button"><span class="icons mail"></span><?php echo $deliveryInfo['email']; ?></a></td>
                    <td><?php echo $r->invoice_response;  ?></td>
                    <td>$<?php echo number_format($r->amount,2,".",",");  ?></td>
                    <td><a target="_blank" onclick="javascript:window.open('<?php echo urlPath($this->e("checkoutpage")); ?>?status=view-<?php echo $r->page_id ."-".$r->invoice_id; ?>&chk=<?php echo md5(intval($r->page_id)."-".$r->invoice_id); ?>&adview','orderview','top=0,left=0,fullscreen=no,scrollbars=yes,width=830,height=700,toolbar=no',true); return false;">View Order</a></td>
                    </tr>
            <?php } ?>
            </table>
	    </div>
	    </div>
    </li>
</ul>
<script type="text/javascript">
    accordRefresh.delay(500);
</script>