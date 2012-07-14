<ul class="form accordian" style="float:left;width:70%">
    <li style="width:100%; float:left;margin-bottom:5px;"><label>List Amounts (Enter in the total amount of list feeds desired)</label>
	<input type="text" name="listamounts" class="required integer" value="<?php echo $this->e("listamounts", "0"); ?>" />
    </li>
    <?php
    for ($i = 1; $i < (intval($this->e("listamounts")) + 1); $i++) { 
		 $ia = $i."-".sketch("siteid");
	?>
        <li style="float:left; width:100%; clear:left;">
    	<a class="accord-title button"><span class="icons downarrow"></span>Lister <?php $ia; ?> : <?php echo $this->e("heading" . $ia, "empty"); ?></a>
    	<div class="accord-body">
	    <div class="accord-container">
    	    <div style="float:left; width:32%;margin-bottom:5px;margin-right:5px;">
    		<label>Heading</label>
    		<input type="text" name="heading<?php echo $ia; ?>" value="<?php echo $this->e("heading" . $ia); ?>"/>
            <input type="hidden" name="site_id<?php echo $ia; ?>" value="<?php echo sketch("siteid"); ?>" />
		<label>Page Limit (0=None)</label>
    		<input type="text" class="integer" name="limitto<?php echo $ia; ?>" value="<?php echo $this->e("limitto" . $ia); ?>"/>
    		<label>List Type</label>
    		<select name="listtype<?php echo $ia; ?>" class="bgClass:'select_bg'">
    		    <option value="news" <?php if ($this->e("listtype" . $ia) == "news") { ?>selected="selected"<?php } ?>>News</option>
    		    <option value="gallery" <?php if ($this->e("listtype" . $ia) == "gallery") { ?>selected="selected"<?php } ?>>Gallery</option>
		   	    <option value="casestudies" <?php if ($this->e("listtype" . $ia) == "casestudies") { ?>selected="selected"<?php } ?>>Case-studies</option>
    		    <option value="product" <?php if ($this->e("listtype" . $ia) == "product") { ?>selected="selected"<?php } ?>>Product</option>
    		    <option value="newsletter" <?php if ($this->e("listtype" . $ia) == "newsletter") { ?>selected="selected"<?php } ?>>newsletter</option>
                <option value="staff" <?php if ($this->e("listtype" . $ia) == "staff") { ?>selected="selected"<?php } ?>>Staff</option>
                <option value="listing" <?php if ($this->e("listtype" . $ia) == "listing") { ?>selected="selected"<?php } ?>>Listing</option>
                <option value="any" <?php if ($this->e("listtype" . $ia) == "any") { ?>selected="selected"<?php } ?>>Any Page</option>
    		</select>
    		<label>Sort By</label>
    		<select name="sortby<?php echo $ia; ?>" class="bgClass:'select_bg'">
    		    <option value="page_date DESC" <?php if ($this->e("sortby" . $ia) == "page_date DESC") { ?>selected="selected"<?php } ?>>Newest First</option>
    		    <option value="page_date ASC" <?php if ($this->e("sortby" . $ia) == "page_date ASC") { ?>selected="selected"<?php } ?>>Oldest First</option>
    		    <option value="menu_order" <?php if ($this->e("sortby" . $ia) == "menu_order") { ?>selected="selected"<?php } ?>>Menu Order</option>
    		    <option value="page_heading" <?php if ($this->e("sortby" . $ia) == "page_heading") { ?>selected="selected"<?php } ?>>Page Heading</option>
    		    <option value="page_price" <?php if ($this->e("sortby" . $ia) == "page_price") { ?>selected="selected"<?php } ?>>Price</option>
    		</select>
    		
    		<div><label>Use Template</label>
    		    <select name="getview<?php echo $ia; ?>" class="bgClass:'select_bg'">
    			<option value="" <?php if ($this->e('getview' . $ia) == "") {?>selected="selected"<?php } ?>>Standard</option>
<?php
			foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash")) as $key => $value) {
			    if (strpos($value, "lister_") !== false) {
 ?>
				<option value="<?php echo $value; ?>" <?php if ($this->e('getview' . $ia) == $value) { ?>selected="selected"<?php } ?>><?php echo str_replace(array("lister_", ".php"), "", $value); ?></option>
<?php }
			} ?>
		    </select>
		</div>
	    </div>
	    <div style="float:left; width:35%;margin-bottom:5px;">
		<label>Display on pages</label>
		<div class="clonecontainer">
<?php if (count((array) $this->e("onPages" . $ia)) == 0) { ?>
			    <div>
				<select name="onPages<?php echo $ia; ?>[]" class="bgClass:'select_bg'">
			    <option value="">None</option>
			    <?php adminFilter("menu",array("select"=>true,"id"=>$value)); ?>
				</select>
			    </div>
<?php } else {
			    foreach ((array) $this->e("onPages" . $ia) as $key => $value) { ?>
	    		    <div>
	    			<select name="onPages<?php echo $ia; ?>[]" class="bgClass:'select_bg'">
    			    <option value="">None</option>
			    	<?php adminFilter("menu",array("select"=>true,"id"=>$value)); ?>
	    			</select>
	    		    </div>
<?php }
			} ?>
    		    <a class="button positive addNewPage"><span class="icons plus"></span>Add New Page</a>
    		</div>
    	    </div>
    	    <div style="float:left; width:31%;margin-left:5px;margin-bottom:5px;overflow:hidden">
    		<label>List pages under: <?php echo $this->e("listtype" . $ia); ?> </label>
    		<div class="clonecontainer">
<?php
			if (count((array) $this->e("getfrom" . $ia)) == 0) {
?>
		    <div>
			<select name="getfrom<?php echo $ia; ?>[]" class="bgClass:'select_bg'">
			    <option value="">None</option>
			    <option value="all" <?php if (in_array("all", $this->e("getfrom" . $ia))) {?>selected="selected"<?php } ?>>All</option>
				<?php adminFilter("menu",array("select"=>true,"id"=>$value)); ?>
				</select>
			    </div>
			    <?php } else {
			    foreach ((array) $this->e("getfrom" . $ia) as $key => $value) {
 ?>
    		    <div>
    			<select name="getfrom<?php echo $ia; ?>[]" class="bgClass:'select_bg'">
    			    <option value="">None</option>
    			    <option value="all" <?php if (in_array("all", $this->e("getfrom" . $ia))) { ?>selected="selected"<?php } ?>>All</option>
					<?php adminFilter("menu",array("select"=>true,"id"=>$value)); ?>
			    	</select>
			    		    </div>
<?php }
			} ?>
		    <a class="button positive addNewPage"><span class="icons plus"></span>Add New Page</a>
		    		</div>
		    	    </div>
		            </div>
	</div>
		        </li>
<?php
} // End Loop ?>
</ul>
<script type="text/javascript">
    function setupAccords(){
	$$('.accordian').each(function(item,index){
	    new accord(item);
	});
	setupClones();
    }
    function setupClones(){
		$$(".addNewPage").each(function(it,ind){
			$(it).addEvent("click",function(){
				var newrow = $(this).getParent('.clonecontainer').getElement("div").clone();
				$(newrow).inject($(this),'before');
				$(newrow).getElements("input").each(function(item,index){
				$(item).set("value","");
				});
				$(newrow).getElements("select").each(function(item,index){
				$(item).addEvent("change",function(){
					$(this).getParent("div").getElement(".span").set("html",$(this).getSelected().get("html"));
				});
				$(item).set("value","");
				});
			});
		});
    }
    setupAccords.delay(500);
</script>