<h2>Purchase history</h2>
<ul class="forms">
<?php
	$chkPage = getData("sketch_page,sketch_menu","menu_guid","page_type='checkout'"); 
	$chkPage->advance();
	if(memberid()){
		$purchase = getData("invoice","*","page_id=".intval(memberid()));
		if($purchase->rowCount() > 0){
			$counter = 0;
			while($purchase->advance()){
				?>
                <li>
                <div style="float:left;width:20%">
                    <?php if($counter==0){?><label>Date</label><?php } ?>
                    <?php echo date("j M Y",strtotime($purchase->invoice_date)); ?>
                </div>
                <div style="float:left;width:40%">
                    <?php if($counter==0){?><label>Result</label><?php } ?>
                    <?php echo $purchase->invoice_response; ?>
                </div>
                <?php if($chkPage->rowCount() > 0){?>
                 <div style="float:left;width:20%">
                    <?php if($counter==0){?><label>View Invoice</label><?php } ?>
                   <a href="<?php echo urlPath($chkPage->menu_guid)."?status=".intval(memberid())."-".$purchase->invoice_id."&chk=".md5(intval(memberid())."-".$iid); ?>" class="button">View Invoice</a>
                </div>
                <?php } ?>
                 <div style="float:right;width:20%">
                    <?php if($counter==0){?><label>Amount</label><?php } ?>
                    <?php echo number_format($purchase->amount,2,'.',','); ?>
                </div>
                </li>        
<?php	
			}
		}else{
			?><p>No purchases have been made</p><?php 	
		}
	}
?>
</ul>