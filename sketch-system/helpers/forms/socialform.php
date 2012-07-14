<ul class="form">
    <li id="tweatMessageArea"></li>
        <li class="accordian">
        	<a class="accord-title button"><span class="icons downarrow"></span>Facebook Share Settings</a>
			<div class="accord-body">
		    <div class="accord-container">
            	<?php $r = getData("sketch_page,tag","*","tag.page_id=".sketch("page_id"));
						$chosen = array();
						$chosen['og:title'] = "Title";
						$chosen['og:type']  = "Type";
						$chosen['og:image'] = "Image";
						$chosen['og:site_name'] = "Site Name";
						$chosen['og:description'] = "description";
					  while($r->advance()){
						  ?>
                          <div style="float:left;width:100%;clear:left;">
                          	<div style="float:left;width:15%;margin-right:1%">
                       		<label>Tag Name</label>
                            <input type="hidden" name="tag_id[]" value="<?php echo $r->tag_id; ?>" />
                           <select name="tag_name[]" class="bgClass:'select_bg'">
                            	<option value="og:title" 		<?php if($r->tag_name=="og:title"){ 		unset($chosen[$r->tag_name]); ?>selected<?php } ?>>Title</option>
                                <option value="og:type" 		<?php if($r->tag_name=="og:type"){ 			unset($chosen[$r->tag_name]);?>selected<?php } ?>>Type</option>
                                <option value="og:image" 		<?php if($r->tag_name=="og:image"){ 		unset($chosen[$r->tag_name]);?>selected<?php } ?>>Image</option>
                                <option value="og:site_name"	<?php if($r->tag_name=="og:site_name"){   	unset($chosen[$r->tag_name]);?>selected<?php } ?>>Website name</option>
                                <option value="og:description"  <?php if($r->tag_name=="og:description"){ 	unset($chosen[$r->tag_name]);?>selected<?php } ?>>Description</option>
                            </select> 
                            </div>
                            <div style="float:left;width:80%;clear:none;">
                            <label>Tag Content</label>
                            <input type="text" name="tag_content[]" value="<?php echo $r->tag_content; ?>" /> 
                            </div>
                            </div> 
                          <?php 
					  }
				foreach($chosen as $key => $value){?>
				<div style="float:left;width:100%;clear:left;">
                          	<div style="float:left;width:15%;margin-right:1%">
                       		<label>Tag Name</label>
                            <input type="hidden" name="tag_id[]" value="" />
                            <select name="tag_name[]" class="bgClass:'select_bg'">
                            	<?php foreach($chosen as $key => $value){?>
                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                <?php } ?>
                            </select> 
                            </div>
                            <div style="float:left;width:80%;clear:none;">
                            <label>Tag Content</label>
                            <input type="text" name="tag_content[]" value="" /> 
                            </div>
                            </div> 	
				<?php } ?>  
        	</div>
            </div>
        	<a class="accord-title button"><span class="icons downarrow"></span>Post to Facebook</a>
			<div class="accord-body">
		    <div class="accord-container">
	   	 		<?php if($this->e("access_token") != ""){ ?>
                	<label>Message</label>
            		<textarea name="message" cols="5"></textarea>
                	<label>Message Name</label>
                    <input type="text" name="messagename" />
                    <label>Message Description</label>
                    <input type="text" name="messagedescription" />
                    <label>Link</label>
                    <input type="text" name="messagelink" value="<?php echo urlPath(sketch("menu_guid")); ?>"/>
                <?php }
				if(superUser()){
					helper("facebook");
					$facebook = new Facebook(array(
							'appId' => $this->e("APPID"),
							'secret' => $this->e("Secret")
					));
				?>
                <p>Please sign into facebook</p>
                <a href="<?php echo $facebook->getLoginUrl(array("redirect_uri"=>urlPath(sketch("menu_guid"))));?>&scope=email,publish_stream,offline_access">Connect</a>
                <label>Application Settings</label>
                <input name="APPID" type="text" value="<?php echo $this->e("APPID"); ?>" />
                <label>Application Secret</label>
                <input type="text" name="Secret" value="<?php echo $this->e("Secret");?>"/>
				<?php } ?>
            </div>
            </div>
        
         	<a class="accord-title button"><span class="icons downarrow"></span>Post to Twitter</a>
			<div class="accord-body">
		    <div class="accord-container">
            <?php if(!$twitterInfo || $this->e("oauth_token", "") == "" || $this->e("oauth_token_secret") == ""){?>
        	<label class="pt10">Auth Url</label><div class="clb"></div><br />
    		<a class="input" style="width:98%" href="<?php echo $this->twitterAPI->getAuthorizationUrl(array("oauth_callback" => urlPath($sketch->menu_guid))); ?>">Authorise sketch to access your twitter account (this will also update authorisation should your tweats stop appearing)</a>
    		<div class="clb"></div>
        	<?php } ?>
		 <?php if ($twitterInfo && $this->e("oauth_token", "") != "" && $this->e("oauth_token_secret") != "") { ?>
        <label>Profile name and image</label><div class="clb"></div>
    	<div style="float:left;padding:5px;"><img src="<?php echo $twitterInfo->profile_image_url; ?>" /></div>
    	<div style="float:left"><?php echo $twitterInfo->screen_name; ?></div><div class="clb"></div>
            <label class="pt10" style="float:left;width:115px;">Add a Tweet</label><div style="float:left;width:400px;text-align:right;color:#999;font-weight:bold;" id="tweetnumcount">0</div>
            <div class="clb"></div>
            <textarea name="mynewtweat" id="mynewtweat"></textarea>
             <?php } ?>
        </div>
        </div>
        <?php helper("oauth"); ?>
        <a class="accord-title button"><span class="icons downarrow"></span>Post to Linked in</a>
			<div class="accord-body">
		    <div class="accord-container">
            <?php
			$linked 	= new linkedin();
			$linked->init($this->e('litoken_secret'),$this->e('lioauth_token'),$this->e('oauth_verifier')); ?>
        	<label>Authorize sketch to connect to linkedIn or Update Account</label><div class="clb"></div>
            <input type="hidden" name="liconn" value="noconn" id="connecttoli"/>
    		<button type="submit" name="connecttoLinkedin" onclick="$('connecttoli').value='connect';">Connect to Linked in</button>
            <div class="clb"></div>
		 <?php if ( $this->e('lioauth_token','')!='') {
			 		$linked->get_profile("http://api.linkedin.com/v1/people/~"); ?>
        	<label>Linked in for <?php echo $linked->get_firstname() ." " . $linked->get_lastname(); ?></label><div class="clb"></div>
            <label>Title</label>
            <input type="text" name="lititle" />
            <label>Comment</label>
            <textarea name="mynewlinkedin" id="mynewlinkedin"></textarea>
            <label>Url</label>
            <input type="text" name="liurl" value="<?php echo urlPath(sketch("menu_guid")); ?>" />
            <label>Image</label>
            <select name="liimage" class="bgClass:'select_bg'" onchange="$('imagesummary').src='<?php echo str_replace("index.php","",urlPath()); ?>/' + this.value;">
          <option value="">None</option>
          <?php $allI = getImages();
           foreach($allI as $key => $value){
             foreach($value as $k => $v){
          ?>
          <option value="<?php echo $k; ?>" <?php if ( sketch( "page_image" ) == $k) { ?>selected="selected"<?php } ?>><?php echo $k ; ?></option>
          <?php
             }
          } ?>
        </select>
        <div style="clear:both;height:50px;overflow:hidden;">
            <img src="<?php echo str_replace("index.php","",urlPath(sketch("page_image"))); ?>" style="height:50px !important;width:auto !important" id="imagesummary" alt="Product image"/>
        </div>
         <?php } ?>
        </div>
        </div>
        <div id="messageresult"></div>
        </li>
</ul>
<script type="text/javascript">
    function numcount(){
		accordRefresh();
		if($("mynewtweat")){
			$("mynewtweat").addEvent("keyup",function(event){
				var ev = new Event(event);
				$("tweetnumcount").set("html",$("mynewtweat").value.length);
				if($("mynewtweat").value.length > 140){
					$("mynewtweat").value = $("mynewtweat").value.substring(0,140).clean();
					$("tweetnumcount").set("html",$("mynewtweat").value.length);
					$("mynewtweat").highlight();
				}
			});
		}
    }
    numcount.delay(500);
</script>