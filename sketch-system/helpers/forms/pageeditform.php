<ul class="form accordian" style="float:left; width:70%">
		<li>
		<a class="accord-title button"><span class="icons downarrow"></span>Page Settings</a>
		<div class="accord-body">
		    <div class="accord-container">
		<div style="width:48%;float:left">
			<label>Page title</label>
			<input type="text" name="page_title" value="<?php echo sketch( "page_title" );?>">
                        <label>Page publish date</label>
                        <?php
    $d = "";
    if ( sketch( "page_date" ) != "" ) {
      list( $y, $m, $d ) = @explode( "-", sketch( "page_date" ) );
      if ( $y != "" ) {
        $d = $d . "-" . $m . "-" . $y;
      } //$y != ""
    } //sketch( "page_date" ) != ""
?>
      <input type="text" name="page_date" value="<?php echo $d;?>" class="calender" />
      <label>Page expiry </label>
                        <?php
    $d = "";
    if ( sketch( "page_expiry" ) != "" ) {
      list( $y, $m, $d ) = @explode( "-", sketch( "page_expiry" ) );
      if ( $y != "" ) {
        $d = $d . "-" . $m . "-" . $y;
      } //$y != ""
    } //sketch( "page_date" ) != ""
?>
      <input type="text" name="page_expiry" value="<?php echo $d;?>" class="calender" />
        <label>Cache page</label>
	<select name="page_cache" class="bgClass:'select_bg'">
          <option value="0" <?php if ( sketch( "page_cache" ) == 0 ) {?>selected="selected"<?php } ?>>No</option>
          <option value="1" <?php if ( sketch( "page_cache" ) == 1 ) {?>selected="selected"<?php } ?>>Yes</option>
	</select>
	<label>Page Status</label>
	<select name="page_status" class="bgClass:'select_bg'">
          <option value="published" <?php if ( sketch( "page_status" ) == 'published' ) { ?>selected="selected"<?php } ?>>Published</option>
          <option value="hidden" <?php if ( sketch( "page_status" ) == 'hidden' ) { ?>selected="selected"<?php } ?>>Hidden</option>
	  <option value="member" <?php if ( sketch( "page_status" ) == 'member' ) { ?>selected="selected"<?php } ?>>Members Only</option>
	</select>
		</div>
		<div style="width:50%;float:left">
		<label>Page Type</label>
		<select name="page_type" class="bgClass:'select_bg'">
          <option value="general" 		<?php if ( sketch( "page_type" ) == "general" ) { ?>selected="selected"<?php } ?>>General</option>
          <option value="landing"		<?php if ( sketch( "page_type" ) == "landing" ) { ?>selected="selected"<?php } ?>>Landing page</option>
		  <option value="news"    		<?php if ( sketch( "page_type" ) == "news" ) { ?>selected="selected"<?php } ?>>News</option>
		  <option value="gallery" 		<?php if ( sketch( "page_type" ) == "gallery" ) { ?>selected="selected"<?php } ?>>Gallery</option>
		  <option value="casestudies" 	<?php if ( sketch( "page_type" ) == "casestudies" ) { ?>selected="selected"<?php } ?>>Case-studies</option>
		  <option value="product" 		<?php if ( sketch( "page_type" ) == "product" ) {   ?>selected="selected"<?php } ?>>Product</option>
		  <option value="newsletter" 	<?php if ( sketch( "page_type" ) == "newsletter" ) {?>selected="selected"<?php } ?>>Newsletter</option>
		  <option value="blog" 			<?php if ( sketch( "page_type" ) == "blog" ) {      ?>selected="selected"<?php } ?>>Blog</option>
          <option value="staff" 		<?php if ( sketch( "page_type" ) == "staff" ) {      ?>selected="selected"<?php } ?>>Staff</option>
          <option value="listing" 		<?php if ( sketch( "page_type" ) == "listing" ) {      ?>selected="selected"<?php } ?>>Listing</option>
		  <option value="member"        <?php if ( sketch( "page_type" ) == "member" ) {    ?>selected="selected"<?php } ?>>Member</option>
          <option value="any"        	<?php if ( sketch( "page_type" ) == "any" ) {    ?>selected="selected"<?php } ?>>Any</option>
          <option value="article"       <?php if ( sketch( "page_type" ) == "article" ) {    ?>selected="selected"<?php } ?>>article listing</option>
          <option value="newsl"         <?php if ( sketch( "page_type" ) == "newsl" ) {    ?>selected="selected"<?php } ?>>Newsletter Listing</option>
          <option value="blogl"         <?php if ( sketch( "page_type" ) == "blogl" ) {    ?>selected="selected"<?php } ?>>Blog Listing</option>
          <option value="productl"      <?php if ( sketch( "page_type" ) == "productl" ) {    ?>selected="selected"<?php } ?>>Product Listing</option>
          <option value="checkout"      <?php if ( sketch( "page_type" ) == "checkout" ) {    ?>selected="selected"<?php } ?>>Checkout page</option>
         <option value="galleryl"   	<?php if ( sketch( "page_type" ) == "galleryl" ) {    ?>selected="selected"<?php } ?>>Gallery Listing</option>
         <option value="pagel"      	<?php if ( sketch( "page_type" ) == "pagel" ) {    ?>selected="selected"<?php } ?>>Page Grouping</option>
		</select>
		<label>Template</label>
		    <select name="pagefile" class="bgClass:'select_bg'">
		    <option value="">None (standard)</option>
<?php
    foreach ( getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value ) {
      if ( strpos( $value, "template" ) !== false ) {
?>
		    <option value="<?php echo $value;?>" <?php if ( sketch( "pagefile" ) == $value ) { ?>selected="selected"<?php } ?>><?php echo $value; ?></option>
<?php
      } //strpos( $value, "template" ) !== false
    } //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='page'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( sketch( "pagefile" ) == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "TEMPLATE: ". $r->template_name; ?></option><?php
		}
	}
?>
		</select>
		<label>Page Form</label>
		<select name="pageform" class="bgClass:'select_bg'">
		<option value="">None (standard)</option>
<?php
    foreach ( getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) ) as $key => $value ) {
      if ( strpos( $value, "page" ) !== false ) {
?>
		<option value="<?php echo $value; ?>" <?php if ( sketch( "pageform" ) == $value ) { ?>selected="selected"<?php } ?>><?php echo $value; ?></option>
<?php
      } //strpos( $value, "page" ) !== false
    } //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) . "forms" . sketch( "slash" ) ) as $key => $value
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='form'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( sketch( "pageform" ) == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "FORM: ". $r->template_name; ?></option><?php
		}
	}
?>
		 </select>
		 <label>Page Output</label>
		 <select name="pageoutput" class="bgClass:'select_bg'">
                  <option value="">None (standard)</option>
<?php
    foreach ( getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value ) {
      if ( strpos( $value, "page" ) !== false ) {
?>
		  <option value="<?php echo $value; ?>" <?php if ( sketch( "pageoutput" ) == $value ) { ?>selected="selected"<?php } ?>><?php echo $value; ?></option>
<?php
      } //strpos( $value, "page" ) !== false
    } //getDirectory( sketch( "abspath" ) . sketch( "themepath" ) . "views" . sketch( "slash" ) ) as $key => $value
	if(getSettings("version") > 2){
		$r = getData("template","*","template_type='output'");
		while($r->advance()){
			?><option value="<?php echo $r->template_name; ?>" <?php if ( sketch( "pageoutput" ) == $r->template_name ) { ?>selected="selected"<?php } ?>><?php echo "OUTPUT: ". $r->template_name; ?></option><?php
		}
	}
?>
		</select>
        
        <?php if( sketch( "page_status" ) == "member" ){?>
        <label>Member Group (select existing groups from this list)</label>
		<select name="menu_sel" class="bgClass:'select_bg'" onchange="$('theClass').value = $('theClass').value +','+this.value">
		    <option value="">All Members</option>
            <?php
			$found = false;
			$memdata = getData("sketch_page,sketch_menu","menu_class","page_status='member' GROUP BY menu_class");
			while($memdata->advance()){
				if($memdata->menu_class != ''){
					$found=true
				?><option value="<?php echo $memdata->menu_class; ?>" <?php if(sketch("menu_class")==$memdata->menu_class){?>selected="selected"<?php } ?>><?php echo $memdata->menu_class; ?></option><?php	
				}
			}
			?>
		</select>
        <label>
		<?php if(!$found){ ?>
        	<div class="alert" style="width:95%;">No Member Group Found</div>
        <?php }else{ ?>
        	Enter a new group name below or select one from above
        <?php } ?>
        </label>
        <input name="menu_class" type="text" id="theClass" value="<?php echo sketch("menu_class"); ?>"/>
        
        <?php }else{ ?>
         <input name="menu_class" type="hidden" id="theClass" value="<?php echo sketch("menu_class"); ?>"/>
        <?php } ?>
		</div>
		</div>
		</div>
        
        <a class="accord-title button"><span class="icons downarrow"></span>Page Listing Content</a>
		<div class="accord-body">
		<div class="accord-container">
        <label>Page Image</label>
        <div class="clb" style="margin-top:5px;margin-bottom:5px;padding-bottom:5px; border-bottom:1px solid #ccc;float:left;width:95%">
        <input type="hidden" name="page_image" class="imageload" id="i0" value="<?php echo sketch( "page_image" )==''? '' : sketch( "page_image" ); ?>" />
        </div>
        <label>Page Intro</label>
        <textarea class="doTiny:true tinySettings:1" name="page_intro" id="page_intro"><?php echo sketch( "page_intro" ); ?></textarea>
		</div>
		</div>
</li>
</ul>