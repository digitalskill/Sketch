<?php	
helper("shoppingcart");
helper("member");
if(isset($_POST['token']) && $_POST['token']==sessionGet("token") && isset($_POST['addpostcoment']) && $_POST['addpostcoment']=="addpostcoment" && memberid()){ 
	sessionRemove('token');
	$data 					= $_POST;
	$data['menu_under'] 	= intval(sketch("sketch_menu_id"));
	$data['sketch_settings_id'] = sketch("siteid");
	$data['page_updated']   = date("Y-m-d H:i:s");
	$data['page_date']	    = date("Y-m-d H:i:s");
	$data['page_type']	    = "blog";
	$data['page_status']	= "hidden";
	$memDetails		    	= memberGet();
	$_POST['member_id']	    = intval(memberid());
	$data['updated_by']	    = (isset($memDetails['nickname']) && $memDetails['nickname']!= "")? $memDetails['nickname'] : $_SERVER['HTTP_HOST'];
	$serial			    	= $_POST;
	foreach($serial as $key => $value){
	    $serial[$key] 			= htmlentities(strip_tags(trim(stripslashes($value))));
	}
	$data['content']	 		= serialize($serial);
	$data['edit']	    		= $data['content'];
	
	$Raw = stripslashes(trim($data['name'].date("ymdhis")));
	$RemoveChars = array("([\40])", "([^a-zA-Z0-9-])", "(-{2,})");
	$ReplaceWith = array("-", "", "-");
	$guid = preg_replace($RemoveChars, $ReplaceWith, $Raw);
	
	$mrow = explode("?",sketch("menu_guid"));
	$guid = trim($mrow[0],"/"). "/" . $guid;
	$data['menu_guid']	= $guid;
	$data['menu_show']	= 0;
	$data['menu_name']	= htmlentities(trim(strip_tags($_POST['name'])));
	$add 	= addData("sketch_page",$data);
	$data['page_id']		= lastInsertId();
	$data['menu_class']		= intval($_POST['rating']);
	$addp 	= addData("sketch_menu",$data);
	?>
    <div class="post alert">
  		<h2 class="title">Thank you - your comment will appear once moderated</h2>
    </div>
    <?php
}
$pstatus =  "page_status='published' AND ";
if(adminCheck()){
	$pstatus ="";
}

// GET Comment Count
$comments = getData("sketch_menu,sketch_page","count(sketch_menu_id) as commentcount",$pstatus." menu_under=".intval(sketch("sketch_menu_id")));
$comments->advance();
?>
<div class="post">
  <h2 class="title"><?php echo sketch('page_heading'); ?></h2>
  <div class="meta">
    	<div class="top-border"></div>
    	<?php if(sketch("page_image")!=""){?>
			<img src="<?php echo urlPath(sketch("page_image")); ?>" alt=""/>
		<?php } ?>
  </div>
  
  <div class="one-half">
  <?php echo sketch("content"); ?>
  <h5>Price: <?php echo number_format(floatval(sketch("product_price")),2,'.',','); ?> <span><?php filter("shoppingcart",array("currency"=>true)); ?></span></h5>
  <?php if(intval(sketch("product_weight")) > 0 ){?>
  			<div class="product_row">
  				<div class="row_title">Weight:</div>
  				<div class="product_weight"><?php echo sketch("product_weight"); ?> (kgs)</div>
			</div>
<?php } ?>
	</div>
    	<div class="comment-form one-half last">
        
<?php if(sketch("product_stock") != 0){ ?>
<form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="required" id="productform">
<ul class="forms">
<li>
  <input type="hidden" value="<?php echo sketch("page_id"); ?>" name="product" />
  <label>Amount</label> <input type="text" size="3" name="quantity" id="quantity" title="Please update the stock amount" class="required integer minValue:1 <?php if(sketch("product_stock") != '-1'){?>maxValue:<?php echo intval(sketch("product_stock"));  }?>" value="" />
</li>
<li>  
  <?php if(sketch("product_size") != ""){ ?>
  <label>Size</label>
                	<select name="size" class="bgClass:'select_bg'">
                    	<?php $allSizes = explode(",",sketch("product_size")); 
								foreach($allSizes as $key => $value){ ?>
                    			<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                    </select>
                <?php } ?>
  <?php if(sketch("product_color") != ""){ ?>
  </li>
  <li>
  <label>Colour</label>
                	<select name="color" class="bgClass:'select_bg'">
                    	<?php $allSizes = explode(",",sketch("product_color")); 
								foreach($allSizes as $key => $value){ ?>
                    			<option value="<?php echo $value; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                    </select>
                <?php } ?>
                </li>
                <li>
  <button class="button" type="submit" onclick="if($('quantity').get('value')==0){ $('quantity').set('value',1);}"><span class="icons plus"></span>Buy Now</button>
</li>
</ul>
</form>
<?php }else{?>
	<h3>Product is out of stock</h3>
<?php } ?>
</div>
	
  <div class="top-border" style="clear:both";></div>
  <div class="tags">
  <?php
  			$r = getData("tag","*","page_id=".sketch("page_id"));
			if($r->rowCount() > 0){?>
  Tags:
  <?php
			$counter = 0;
			while($r->advance()){
          		echo $counter > 0? ", " : ""; ?>
  					<a href="<?php echo urlPath(sketch("menu_guid"));?>?tag=<?php echo urlencode($r->tag_name); ?>" title=""><?php echo $r->tag_name; ?></a>
  <?php 
				$counter++;
				}
			}
			$r->free();?>
</div>
</div>
<div id="comment-wrapper">
<?php 
	$com = getData("sketch_page,sketch_menu","*",$pstatus." menu_under=".intval(sketch("sketch_menu_id")),"ORDER BY page_date DESC, sketch_menu_id DESC"); 
	if($com->rowCount() > 0) { ?>
	<h3><a id="comments"></a><?php echo intval($comments->commentcount); ?> Comments for  "<?php echo sketch("page_title"); ?>"</h3>
    <div id="comments">
      <ol id="singlecomments" class="commentlist">
      	<?php while($com->advance()){ 
				$c = contentToArray($com->content);
				$mem = getData("sketch_page","*","page_type='member' AND page_id=".intval($c['member_id']));
				$mem->advance();
				$memc = contentToArray($mem->content);	
		?>
        <li class="clearfix" id='comment<?php echo $com->page_id; ?>'>
          <div class="user"><img alt="" src="sketch-images/art/member1.jpg" height="60" width="60" class="avatar" /></div>
          <div class="message">
            <div class="infor">
              <h3><a href="#"><?php echo $memc['nickname']==""? $memc['firstname']." ".$memc['lastname']: $memc['nickname']; ?> rated this product as <?php echo intval($c['rating']);?> out of 5</a></h3>
              <span class="date"><?php echo date("d F, Y",strtotime($com->page_date)); ?></span> </div>
           	  <p><?php echo htmlentities(strip_tags(trim($c['textarea']))); ?></p>
              <?php if(adminCheck()){?>
              	<div class="info round">
                	<?php if($com->page_status!='published'){?>
                	<a id='approve<?php echo $com->page_id; ?>' class="button ajaxlink output:'approve<?php echo $com->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?approvepage=<?php echo $com->page_id; ?>"><span class="icons cross"></span>Approve</a>
                	<?php } ?>
                    <a class="button negative ajaxlink output:'comment<?php echo $com->page_id; ?>'" href="<?php echo urlPath(sketch("menu_guid")); ?>?deletepage=<?php echo $com->page_id; ?>"><span class="icons cross"></span>Delete</a>
                </div>
              <?php } ?>
              
          </div>
          <div class="clear"></div>
        </li>
        <?php } ?>
      </ol>
    </div>
<?php } 
	if(memberid()){ ?>
		<div id="comment-form" class="comment-form">
        	<a id="cform"></a> 
          <h3 id='replyingto'>Rate this product</h3>
           <form action="<?php echo urlPath(sketch("menu_guid")); ?>" method="post" class="required">
            <input type="hidden" name="addpostcoment" value="addpostcoment">
            <input type="hidden" name="token" value="<?php $tok = md5(rand()); sessionSet("token",$tok,false); echo sessionGet("token"); ?>" class="required"/>
            <div class="comment-input">
              <p>
                <label for="rating" id="ratinglbl">Rating (1-5)</label>
               
                <input type="text" min="0" max="5" name="rating" value="" id="class" class="required integer label:'ratinglbl' minValue:0 maxValue:5">
              </p>
            </div>
            <div class="comment-textarea">
              <textarea name="textarea" id="textarea"></textarea>
               <button type="submit" name="submit"><span class="icons heart"></span>Rate product</button>
            </div>
           
          </form>
          <div class="clear"></div>
        </div>
        <?php } ?>
</div>