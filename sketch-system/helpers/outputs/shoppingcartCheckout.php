<?php if(isset($errorMessage)){ ?>
    <div class="alert">
	<h1>Payment Error</h1>
	<?php echo $errorMessage; ?>
      </div><?php
}
$amounts 	 = sessionGet("cart");
if($amounts){ ?>
    <form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="" id="productform"><?php
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
	   
	    ?>
        <?php foreach ($amounts as $key => $value){ 
				if(intval($value) > 0){
				list($id,$size,$color) = explode(":",$key);
				$r = getData("sketch_page,sketch_menu","*","sketch_page.page_id=".intval($id));
				$r->advance();
				$c = contentToArray($r->content);
		?>
        <div style="font-size:12px;font-weight:bold;clear:both">
			<?php 
				echo strip_tags($r->page_heading);
				?>
            </div>
        <div style="clear:both"></div>
	    <div class="productImage one-sixth">
        	
            <?php
			if(trim($c['page_image']) != ""){?>
			<img src="<?php echo urlPath($c['page_image']); ?>" style="height:auto; width:90%" border="0" alt="<?php echo strip_tags($r->page_heading);  ?>" title="<?php echo htmlentities(strip_tags($r->page_heading)); ?>"/>
			<?php } ?>&nbsp;
		    </div>
		    <div class="one-sixth">
			$<?php echo number_format(floatval($c['product_price']),2,'.',','); echo " ".$this->e("currency"); ?>
			<input type="hidden" value="<?php echo $id; ?>" name="product[]">
		    </div>
	    <div class="one-sixth">
		    <input type="text" size="3" style="min-width:12px;width:90%" name="quantity[]" class="" value="<?php echo $value; ?>">
	    </div>
         <div class="one-sixth">
         <input type="hidden" value="<?php echo $size; ?>" name="size[]">
		    <?php echo $size; ?>&nbsp;
	    </div>
         <div class="one-sixth">
         	<input type="hidden" value="<?php echo $color; ?>" name="color[]">
		    <?php echo $color; ?>&nbsp;
	    </div>
	    <div class="one-sixth last">$<?php echo number_format(floatval($c['product_price']) * intval($value),2,'.',','); ?>
		    <?php $total += floatval($c['product_price']) * intval($value); ?>&nbsp;
	    </div>
        <div class="top-border" style="clear:both"></div>
	 	<?php 
				}
		} ?>
	       </div>
    <div class="totalPrices info">
	<div style="text-align:right;"><label>SUB TOTAL :</label>$<?php echo number_format($total,2,'.',','); echo " ".$this->e("currency"); ?></div>
	<?php if($this->e("gst","0")!="0"){?>
    	<div style="text-align:right;"><label>GST :</label>$<?php echo number_format($total*floatval($this->e("gst","0")),2,'.',',');  echo " ".$this->e("currency"); ?></div><?php } ?>
	<div style="text-align:right;"><label>TOTAL :</label>$<?php echo number_format($total*(floatval($this->e("gst","0"))+1),2,'.',',');  echo " ".$this->e("currency"); 
	    $_SESSION['paymentAmount'] = number_format($total*(floatval($this->e("gst","0"))+1),2,'.',',');
		?>
	</div>

    </div>
    <div class="top-border" style="clear:both"></div>
    <div style="padding:10px">
    	<div style="float:right">
        
        <?php // Get Product Lister;
				$shop = getData("sketch_page,sketch_menu",'menu_guid',"page_type='productl'");
				if($shop->rowCount() > 0){
					$shop->advance();
		?>
        
	<button onclick="window.location = '<?php echo urlPath($shop->menu_guid); ?>'"	class="button pill bleft" type="button"><span class="icons leftarrow"></span>Back to Shopping</button>
    
    <?php }else{ ?>
    		<button onclick="history.back();"	class="button pill bleft" type="button"><span class="icons leftarrow"></span>Back to Shopping</button>
    <?php } ?>
	<button type="submit" name="update"	class="button pill middle"><span class="icons reload"></span>Update Prices</button>
    <?php if($this->e("cctypes") != "paypal" || $this->e("onlinebanking") != 'no'){?>
    <button type="submit" name="docheckout"	class="button pill <?php if($this->e("cctypes")!="paypal"){?>positive primary bright<?php }else{ ?>middle<?php } ?>"><span class="icons rightarrow"></span>Check out</button>
    <?php } ?>
    <?php if($this->e("cctypes")=="paypal"){?>
	<button type="submit" name="checkout" class="button positive primary pill bright" ><span class="icons rightarrow"></span>Pay with Paypal</button>
    <?php } ?>
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
      <?php // Get Product Lister;
				$shop = getData("sketch_page,sketch_menu",'menu_guid',"page_type='productl'");
				if($shop->rowCount() > 0){
					$shop->advance();
		?>
        <a class="button" href="<?php echo urlPath($shop->menu_guid); ?>"><span class="icons leftarrow"></span>Back to Shopping</a>
    
    <?php }else{ ?>
    		<a class="button" href="#" onclick="window.back();"><span class="icons leftarrow"></span>Back to Shopping</a>
    <?php } ?>
    
<?php }