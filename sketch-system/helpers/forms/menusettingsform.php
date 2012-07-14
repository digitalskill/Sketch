<div class="bgwhite" id='delmenu'>
	    <div class="content-column" style="padding-left:5px;">
			<div class="big-font" style="text-shadow: 0px 0px 2px lightSlateGray;color:lightSlateGray"><?php echo ucfirst(stripslashes($this->datalookup[$itemId]['menu_name'])); ?> settings</div>
        </div>
		<a class="accord-title button" style="clear:both;"><span class="icons downarrow"></span>Menu Settings</a>
		<div class="accord-body">
		    <div class="accord-container">
			<label>Menu Name</label>
			<input type="text" class="required" name="menu_name[]" value="<?php echo stripslashes($this->datalookup[$itemId]['menu_name']); ?>"/>
			<input type="hidden" name="sketch_menu_id[]" value="<?php echo intval($this->datalookup[$itemId]['sketch_menu_id']); ?>"/>
			<label>Menu Class</label>
			<input type="text" name="menu_class[]" value="<?php echo stripslashes($this->datalookup[$itemId]['menu_class']); ?>"/>
			<label>Menu url (this is the page guid)</label>
			<input type="text" class="" name="menu_guid[]" value="<?php echo str_replace("_##-","?",stripslashes(trim($this->datalookup[$itemId]['menu_guid']))); ?>"/>
			<label>On Mobile</label>
			<select name="menu_mobile[]" class="bgClass:'select_bg'">
			    <option value="0" <?php if($this->datalookup[$itemId]['menu_mobile']==0){?>selected="selected"<?php } ?>>No</option>
			    <option value="1" <?php if($this->datalookup[$itemId]['menu_mobile']==1){?>selected="selected"<?php } ?>>Yes</option>
			</select>
			<label>Menu under</label>
			<select name="menu_under[]" class="bgClass:'select_bg'">
			    <option value="0">None</option>
				<?php adminFilter("menu",array("select"=>true,"id"=>$this->datalookup[$itemId]['menu_under'])); ?>
		</select>
		<label>On menu</label>
		<select name="menu_show[]" class="bgClass:'select_bg'">
		    <option value="0" <?php if ($this->datalookup[$itemId]['menu_show'] == 0) { ?>selected="selected"<?php } ?>>No</option>
		    <option value="1" <?php if ($this->datalookup[$itemId]['menu_show'] == 1) { ?>selected="selected"<?php } ?>>Yes</option>
		</select>
		<label>Jump to Page</label>
		<?php echo str_replace(array("class", "<a"), array("rel", "<a class='button' style='width:95%;margin-top:2px;height:17px;padding-top:10px'"), $this->menuData['items'][$itemId]); ?>
	    	    </div>
	    	</div>
	    	<a class="accord-title button"><span class="icons downarrow"></span>Page Settings</a>
	    	<div class="accord-body">
	    	    <div class="accord-container">
	    		<label>Page Template</label>
	    		<select name="pagefile[]" class="bgClass:'select_bg'">
	    		    <option value="">None (standard)</option><?php
			    foreach (getDirectory(sketch("abspath") . sketch("themepath") . "views" . sketch("slash")) as $key => $value) {
				if (strpos($value, "template") !== false) {
		?>
				    <option value="<?php echo $value; ?>" <?php if ($record->pagefile == $value) { ?>selected="selected"<?php } ?>><?php echo $value; ?></option>
		    <?php }
			    } ?>
			</select>
			<label>Page Type</label>
			<select name="page_type[]" class="bgClass:'select_bg'">
			    <option value="general" 	<?php if ($record->page_type == "general") { 		?>selected="selected"<?php } ?>>General</option>
                <option value="landing"		<?php if ($record->page_type == "landing" ) { 		?>selected="selected"<?php } ?>>Landing page</option>
			    <option value="news" 		<?php if ($record->page_type == "news") { 			?>selected="selected"<?php } ?>>News</option>
                <option value="gallery" 	<?php if ($record->page_type == "gallery" ) { 		?>selected="selected"<?php } ?>>Gallery</option>
			    <option value="casestudies" <?php if ($record->page_type == "casestudies") { 	?>selected="selected"<?php } ?>>Case-studies</option>
			    <option value="product"		<?php if ($record->page_type == "product") { 		?>selected="selected"<?php } ?>>Product</option>
			    <option value="newsletter" 	<?php if ($record->page_type == "newsletter") { 	?>selected="selected"<?php } ?>>Newsletter</option>
			    <option value="blog"		<?php if ($record->page_type == "blog") { 			?>selected="selected"<?php } ?>>Blog</option>
		        <option value="member"		<?php if ($record->page_type == "member") {			?>selected="selected"<?php } ?>>Member</option>
                <option value="staff" 	    <?php if ($record->page_type == "staff") { 			?>selected="selected"<?php } ?>>Staff</option>
                <option value="listing" 	<?php if ($record->page_type == "listing") { 		?>selected="selected"<?php } ?>>Listing</option>
                <option value="any"        	<?php if ($record->page_type == "any" ) {    		?>selected="selected"<?php } ?>>Any</option>
          		<option value="article"     <?php if ($record->page_type == "article" ) {    	?>selected="selected"<?php } ?>>article listing</option>
          		<option value="newsl"       <?php if ($record->page_type == "newsl" ) {    		?>selected="selected"<?php } ?>>Newsletter Listing</option>
          		<option value="blogl"       <?php if ($record->page_type == "blogl" ) {    		?>selected="selected"<?php } ?>>Blog Listing</option>
          		<option value="productl"    <?php if ($record->page_type == "productl" ) {    	?>selected="selected"<?php } ?>>product Listing</option>
                <option value="checkout"	<?php if ($record->page_type == "checkout" ) {    	?>selected="selected"<?php } ?>>Checkout page</option>
                <option value="pagel"       <?php if ($record->page_type == "pagel" ) {    		?>selected="selected"<?php } ?>>Page Grouping</option>
                <option value="galleryl"    <?php if ($record->page_type == "galleryl" ) {    	?>selected="selected"<?php } ?>>Gallery Listing</option>
		</select>
		<label>Page Status</label>
		<select name="page_status[]" class="bgClass:'select_bg'">
		    <option value="published" 	<?php if ($record->page_status == "published") { ?>selected="selected"<?php } ?>>Published</option>
		    <option value="hidden"	<?php if ($record->page_status == "hidden") { ?>selected="selected"<?php } ?>>Hidden</option>
		    <option value="member"	<?php if ($record->page_status == "member") { ?>selected="selected"<?php } ?>>Members only</option>
		</select>
		<label>Page Title</label>
		<input type="text" name="page_title[]" class="required" value="<?php echo stripslashes($record->page_title); ?>" />
		<input type="hidden" name="page_mid[]" value="<?php echo intval($record->page_id); ?>" />
		<label>Delete page</label>
		<a href="<?php echo urlPath($record->menu_guid); ?>?deletepage=<?php echo intval($record->page_id); ?>" class="button negative ajaxlink confirm output:'delmenu'">Delete Page</a>
	    </div>
	</div>
</div>
<script type="text/javascript">
	function startlinks(){
		$$(".ajaxlink").each(function(item,index){
			new Ajaxlinks(item);
		});
	}
	startlinks.delay(500);
</script>