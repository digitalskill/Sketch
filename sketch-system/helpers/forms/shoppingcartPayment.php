<?php if($internetBanking){
	?><h1>Shopping Order</h1><?php
	if($this->e("onlinebanking")=="email"){ ?>
	    <div class="success">
	    <h2><?php echo $_SERVER['HTTP_HOST']; ?> has send you an email copy</h2>
	    <p>Thank you for your order. We will review it and get back to you.</p>
	    </div>
<?php	}else{?>
	    <div class="notice">
	    <h2><?php echo $_SERVER['HTTP_HOST']; ?> needs to contact you</h2>
	    <p><?php echo $_SERVER['HTTP_HOST']; ?> will email you their bank account details to arrange payment.</p>
	    </div>
<?php	}
     }else{
	if($showReceipt && $errorMessage==""){ ?>
	<h1>Shopping Order</h1>
	<div class="success">
	<h4>Thank you for your order</h4>
	<p>A copy of the order has been sent to your email address</p>
       </div>
<?php }
}

if($errorMessage != ''){ ?>
       <div class="alert">
	    <h1>Payment Error</h1>
	<?php echo $errorMessage; ?>
      </div>
<?php }
$amounts	    = ($showReceipt)? $products : sessionGet("cart");
if($amounts){ ?>
<form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="" id="productform"><?php
    $total	    	= 0;
    $GST	    	= 0;
    $deliveryInfo   = (array)getDeliveryInfo($deliveryInfo);
    $freight	    = floatval(end(explode(":",$deliveryInfo["country"])));
    $ItemBreakdown  = array();
    $costref	    = 0; ?>
    <h2 class="title">Cart Details</h2>
     <div class="top-border" style="clear:both"></div>
	 <div class="productRow">
	    <div class="large" style="float:left;width:100%">
	    	<div class="one-sixth">&nbsp;</div>
	    	<div class="one-sixth">Cost each</div>
	    	<div class="one-sixth">Quantity</div>
        	<div class="one-sixth">Size</div>
        	<div class="one-sixth">Color</div>
	    	<div class="one-sixth last">Line Total</div>
	    </div>
	<div class="top-border" style="clear:both"></div><?php
	foreach($amounts as $key => $qty){
			list($id,$size,$color) = explode(":",$key);
			$r = getData("sketch_page,sketch_menu","*","sketch_page.page_id='".intval($id)."'");
			$r->advance();
			$c 		= contentToArray($r->content);
			$qty 	= 	 ($showReceipt)? floatval($products[$key][0]) : $qty;
			$p 	   	=    ($showReceipt)? floatval($products[$key][1]) : floatval($c['product_price']);
			if(intval($qty) > 0){
	?>
        <div style="font-size:12px;font-weight:bold;clear:both">
			<?php 
				echo strip_tags($r->page_heading);
				?>
            </div>
	    <div class="productImage one-sixth" >
		<?php if(trim($c['page_image']) != ""){?>
		<img src="<?php echo urlPath($c['page_image']); ?>" style="height:auto; width:100%" border="0" alt="<?php echo strip_tags($r->page_heading);  ?>"/>
		<?php }else{
		    echo strip_tags($r->page_heading);
		 } ?>&nbsp;
	    </div>
	    <div class="one-sixth">
		$<?php echo number_format($p,2,'.',','); echo " ".$this->e("currency"); ?>&nbsp;
	    </div>
	    <div class="one-sixth" >
		<?php echo $qty; ?>&nbsp;
	    </div>
        
        <div class="one-sixth">
		    <?php echo $size; ?>&nbsp;
	    </div>
         <div class="one-sixth">
		    <?php echo $color; ?>&nbsp;
	    </div>
        
        
	    <div class="one-sixth last">
		$<?php echo number_format($p * intval($qty),2,'.',',');
			$total += $p * intval($qty); ?>&nbsp;
	    </div>
		<div class="top-border" style="clear:both"></div>
<?php	    $ItemBreakdown[$key] = array($qty,$p);
		} 
	}?>
</div>
<div class="productRow">
<h4>Delivery Details</h4>
<?php if($showReceipt==false && !isset($_GET['token'])){?>
    <button type="submit" class="button" name="checkout" style="float:right;">Update Details</button>
    <?php } ?>
<ul class="forms" style="float:left;width:70%">
<?php foreach($deliveryInfo as $key => $value){
    if(trim($value!="")){
	$value = explode(":",$value);
	?>
	<li><?php echo ucfirst(str_replace("name"," name",trim(stripslashes($key))));?>: <span><?php echo trim(stripslashes($value[0]));?></span></li>
<?php }
}?>
    </ul>
</div>
<div class="top-border" style="clear:both"></div>
<div class="totalPrices info" >
	<div style="text-align:right;">SUB TOTAL: $<?php echo number_format($total,2,'.',','). " ".$this->e("currency"); ?></div>
	<?php if(floatval($freight) > 0){?>
	<div style="text-align:right;">FREIGHT COSTS: $<?php echo number_format($freight,2,'.',','). " ".$this->e("currency"); ?></div>
	<?php } ?>
	<div style="text-align:right;">GST: $<?php echo number_format(((($total+$freight)*floatval($this->e("gst","0")))),2,'.',','). " ".$this->e("currency"); ?></div>
	<div style="text-align:right;">TOTAL: $<?php echo number_format(($total+$freight)*(floatval($this->e("gst","0"))+1),2,'.',',')." ".$this->e("currency"); ?></div>
</div>
<?php if($showReceipt==false){
	    $ItemBreakdown['amount']   = ($total+$freight)*(floatval($this->e("gst","0"))+1);
	    sessionAdd("itembreakdown",$ItemBreakdown);
?>
<div class="top-border" style="clear:both"></div>
    <div style="padding:10px">
    	<div style="float:right">
        
        <button onclick="window.location = '<?php echo urlPath($this->e("checkoutpage")); ?>'"	class="button pill bleft" type="button"><span class="icons leftarrow"></span>Change Order</button>
        
    <?php if(($this->e("onlinebanking")=="yes" || $this->e("onlinebanking")=="email") && !isset($_GET['token'])){?>
	<button type="submit" name="banktransfer" class="button pill <?php if($this->e("cctypes","")==""){?>positive primary bright<?php }else{ ?>middle<?php } ?>"><span class='icons mail'></span><?php echo $this->e("onlinebanking")=="email"? "Email Order" : "Pay by Bank Transfer"; ?></button>
    <?php } ?>
    <?php if($this->e("cctypes","")!= ""){?>
		<button type="submit" name="payonline" class="button pill positive primary bright"><span class='icons check'></span><?php echo $this->e("cctypes")=="paypal"? "Confirm payment" : "Pay by Credit Card"; ?></button>
    <?php } ?>
</div>
</div>
<div class="top-border" style="clear:both"></div>
<div>
<p style="text-align:right;">Progress: You are at step 3 of 3</p>
</div>
<?php } ?>
	    </form>
	    <?php
    }else{
	    if(!isset($_REQUEST['status'])){
    ?>
<h2>Your Shopping Cart is empty</h2>
<a href="<?php echo urlPath($this->e("checkoutpage")); ?>"><span class="icons arrowleft"></span>Back to Shopping</a>
<?php } ?>
<?php }