<form class="required clear" action="<?php echo urlPath(sketch("menu_guid")); ?>?blogcat=<?php echo $i; ?>" method="post">
    <label>Post Title: </label>
    <input type="text" name="page_heading" class="required"><br />
    <div style="position:relative;">
    <label style="position: absolute; top: 5px; left: 5px; z-index: 99; display: block;" id="textlbl">Add your post here...</label>
    <textarea name="edit" class="required rich:true label:'textlbl'" cols="50" rows="3"></textarea>
    </div>
    <div style="position:relative;float:left;">
	<label>Post Image</label><br>
	<select onchange="$('page_bimage_d').set('src','<?php echo str_replace("index.php","",urlPath()); ?>/'+ this.value); $('page_bimage_d').removeClass('hide');" name="page_image">
	    <option value="">None</option>
	    <?php $alli = getImages();
		    foreach($alli as $key => $value){
			if(count($value)>0){
			    foreach($value as $k => $v){?>
					<option value="<?php echo $k; ?>"><?php echo end(explode("/",$k)); ?></option><?php
			    }
			}
		    }
	    ?>
	</select>
    </div>
    <div style="float:left;height:50px;margin-left:5px">
	<img style="width:auto;height:50px;" class="hide" src="" alt="Post Image" id="page_bimage_d" />
	</div>
    <input type="hidden" name="addpost" value="addpost">
    <input type="hidden" value="<?php echo $i; ?>" class="required integer" name="blog">
    <input type="hidden" name="token" value="<?php helper("session"); $tok = md5(rand()); sessionSet("token",$tok,false); echo sessionGet("token"); ?>" class="required"/>
    <input type="hidden" name="blogcat" value="<?php echo $i; ?>" />
    <div class="clear"></div>
    <button type="submit" class="positive"><span class="icons check"></span>Add Post</button>
</form>