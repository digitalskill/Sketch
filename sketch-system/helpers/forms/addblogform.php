<ul style="float:right;width:38%;">
    <li>
	<label>Instructions for creating a new blog page</label>
	<div class="instruction-box">
	    <p><strong style="font-size:12px;">Enter in the blog title : </strong>This name must be unique to the site.</p>
	    <p><strong style="font-size:12px;">Select where the page resides in the menu : </strong><br />
		Select the parent of this page. E.g If this page is a product - then it may go under a products page.</p>
	    <p><strong style="font-size:12px;">Select the page type : </strong><br />
		Choose if this is to be a general web page for your site, or a special page like a product or case study page</p>
	    <p><strong style="font-size:12px;">Select if the page is public : </strong><br />Public pages are viewable by everyone,
		while hidden pages are only for administrators.
	    </p>
	    <p><strong style="font-size:12px;">On Menu:</strong><br />Select Yes or No to update the sites menu.</p>
	    <p><strong style="font-size:12px;">Page Template : </strong><br />Select the template that best suits this page.<br />
		If the page is a news or case studies page, there may be a special template available to <br />make the page display content correctly.
	    </p>
	    <p><strong style="font-size:12px;">Page Form : </strong><br />Select the form that matches the pages content.
	    </p>
	    <p><strong style="font-size:12px;">Page Output : </strong><br />Select the ouput format that matches the pages content.</p>
	</div>
    </li>
</ul>

<ul class="form accordian" style="float:left;width:60%">
    <li><label></label></li>
    <li><a class="accord-title button"><span class="icons downarrow"></span>Add Blog Entry</a>
	<div class="accord-body">
	    <div class="accord-container">
		<label>Blog Name</label>
		<input type="text" name="menu_name" value="" />
                <label>Page Under</label>
		<select name="menu_under" class="bgClass:'select_bg'">
        	<?php $r = getData("sketch_page,sketch_menu","*","WHERE page_type='blogl'"); 
					$counter=0;
					while($r->advance()){ 
						$counter++;
					?>
                   		<option value="<?php echo $r->sketch_menu_id; ?>" <?php if($counter==1){?>selected="selected"<?php } ?>><?php echo $r->menu_name; ?> (ARTICLE LISTING PAGE)</option>  
			<?php } ?>
			<?php adminFilter("menu",array("select"=>true,"id"=>0)); ?>
		</select>
		<div style="width:48%;float:left">
			<label>Blog title</label>
			<input type="text" name="page_title" value="">
                        <label>Page publish date</label>
      <input type="text" name="page_date" value="<?php echo date("d-m-Y");?>" class="calender" />
        <label>Cache Entry</label>
	<select name="page_cache" class="bgClass:'select_bg'">
          <option value="0">No</option>
          <option value="1">Yes</option>
	</select>
	<label>Blog Status</label>
	<select name="page_status" class="bgClass:'select_bg'">
          <option value="published" selected="selected">Published</option>
          <option value="hidden" >Hidden</option>
	  <option value="member">Members Only</option>
	</select>
		</div>
		<div style="width:50%;float:left">
		<label>Blog Type</label>
		<select name="page_type" class="bgClass:'select_bg'">
          <option value="general" 		>General</option>
		  <option value="news"    		>News</option>
		  <option value="gallery" 		>Gallery</option>
		  <option value="casestudies" 	>Case-studies</option>
		  <option value="product" 		>Product</option>
		  <option value="newsletter" 	>Newsletter</option>
		  <option value="blog" 			selected="selected">Blog</option>
          <option value="staff" 		>Staff</option>
          <option value="listing" 		>Listing</option>
		  <option value="member"        >Member</option>
          <option value="any"        	>any</option>
          <option value="article"    >Article listing</option>
          		<option value="newsl"      >Newsletter Listing</option>
          		<option value="blogl"      >Blog Listing</option>
          		<option value="productl"   >product Listing</option>
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
		<label>Blog Form</label>
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
			?><option value="<?php echo $r->template_name; ?>"><?php echo "OUTPUT: ". $r->template_name; ?></option><?php
		}
	}
?>
		</select>
		</div>
		</div>
		</div>
        
        <a class="accord-title button"><span class="icons downarrow"></span>Blog Page Content</a>
        <div class="accord-body">
        <div class="accord-container">
            <label>Main heading</label>
            <input type="text" name="page_heading" value="">
            <label>Lead Paragraph</label>
            <textarea name="leadparagraph" style="height:100px;width:95%"></textarea>
            <label>Main content</label>
            <textarea name="edit" class="doTiny:true tinySettings:1" id="edit" style="height:300px;width:95%"></textarea>
        </div>
        </div>
        <a class="accord-title button"><span class="icons downarrow"></span>Blog Summary Content</a>
		<div class="accord-body">
		<div class="accord-container">
        <label>Blog Image</label>
        <div class="clb" style="margin-top:5px;margin-bottom:5px;padding-bottom:5px; border-bottom:1px solid #ccc;float:left;width:95%">
        <input type="hidden" name="page_image" class="imageload" id="i0" value="" />
        </div>
        <label>Blog Intro</label>
        <textarea class="doTiny:true tinySettings:1" name="page_intro" id="page_intro"></textarea>
		</div>
		</div>
</li>
</ul>
<script type="text/javascript">
    accordRefresh.delay(500);
	</script>