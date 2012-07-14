<?php if(isset($errorMessage)){ ?>
    <div class="alert">
	<h1>Payment Error</h1>
	<?php echo $errorMessage; ?>
      </div><?php
}
$r = getProducts($products);
if($r){ ?>
    <form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="" id="productform"><?php
	$amounts 	 = sessionGet("cart");
	$sizes		 = sessionGet("sizes");
	$colours	 = sessionGet("color");
	$total 		 = 0;
	$GST		 = 0; ?>
    <h2 class="title">Shopping Cart Items</h2>
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
	<div class="top-border" style="clear:both"></div>
	<?php
	while($r->advance()){
	   $c = unserialize($r->result['content']);
	   foreach ($c as $k => $v) {
	    $c[$k] = str_replace(array("_##-",";#;"),array("?",'"'), $v);
	   }
	    ?>
        <div style="clear:both"></div>
	    <div class="productImage one-sixth">
			<?php if(trim($c['page_image']) != ""){?>
			<img src="<?php echo urlPath($c['page_image']); ?>" style="height:auto; width:90%" border="0" alt="<?php echo strip_tags($r->page_heading);  ?>"/>
			<?php }else{
			    echo strip_tags($r->page_heading);
			 } ?>&nbsp;
		    </div>
		    <div class="one-sixth">
			$<?php echo number_format(floatval($c['product_price']),2,'.',','); echo " ".$this->e("currency"); ?>
			<input type="hidden" value="<?php echo $r->page_id; ?>" name="product[]">
		    </div>
	    <div class="one-sixth">
		    <input type="text" size="3" style="min-width:12px;width:90%" name="quantity[]" class="" value="<?php echo $amounts[$r->page_id]; ?>">
	    </div>
         <div class="one-sixth">
		    <?php echo $sizes[$r->page_id]; ?>&nbsp;
	    </div>
         <div class="one-sixth">
		    <?php echo $colours[$r->page_id]; ?>&nbsp;
	    </div>
	    <div class="one-sixth last">$<?php echo number_format(floatval($c['product_price']) * intval($amounts[$r->page_id]),2,'.',','); ?>
		    <?php $total += floatval($c['product_price']) * intval($amounts[$r->page_id]); ?>&nbsp;
	    </div>
	  
<?php	} // end WHILE $R->ADVANCE ?>
	       </div>
    <div class="top-border" style="clear:both"></div>
    <div class="totalPrices info">
	<div style="text-align:right;"><label>SUB TOTAL :</label>$<?php echo number_format($total,2,'.',','); echo " ".$this->e("currency"); ?></div>
	<?php if($this->e("gst","0")!="0"){?>
    	<div style="text-align:right;"><label>GST :</label>$<?php echo number_format($total*floatval($this->e("gst","0")),2,'.',',');  echo " ".$this->e("currency"); ?></div><?php } ?>
	<div style="text-align:right;"><label>TOTAL :</label>$<?php echo number_format($total*(floatval($this->e("gst","0"))+1),2,'.',',');  echo " ".$this->e("currency"); 
	    $_SESSION['paymentAmount'] = number_format($total*(floatval($this->e("gst","0"))+1),2,'.',','); ?>
	</div>

    </div>
    <div class="top-border" style="clear:both"></div>
    <div style="padding:10px">
    	<div style="float:right">
	<button onclick="history.back();"	class="button pill bleft" type="button"><span class="icons leftarrow"></span>Back to Shopping</button>
	<button type="submit" name="update"	class="button pill middle"><span class="icons reload"></span>Update Prices</button>
	<button type="submit" name="checkout"	class="button positive primary pill bright" ><span class="icons rightarrow"></span> Check out</button>
		</div>
	<div style="clear:both"></div>
    </div>
    <div>
    <p style="text-align:right">Progress: You are at step 1 of 3</p>
    </div>
    </form>
<?php
}else{ ?>
    <h2>Your Shopping Cart is empty</h2>
    <a class="button" href="#" onclick="window.back();">Back to Shopping</a>
<?php }