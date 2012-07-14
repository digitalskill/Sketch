<?php
error_reporting(E_ALL);
ini_set("display_errors",1);
class CROPMYIMAGE extends CONTROLLER{
  function CROPMYIMAGE($page){
    parent::__construct($page);
	if(!adminCheck()){
		header("location:".urlPath());
		exit();	
	}
	if(isset($_GET['savechanges'])){
		$filename  = sketch("abspath")."sketch-images".sketch("slash").$_GET['image'];
		$percent   = floatval($_GET['crScale']);
		$percent   = $percent < 0 ? 1 : $percent;
		list($width, $height) = getimagesize($filename);
		$image_p 	= imagecreatetruecolor($width * $percent, $height * $percent);
		
		switch(strtolower(end(explode(".",$filename)))){
			case "png":
					$image 		= imagecreatefrompng($filename);
					imagealphablending($image_p, false);
				break;
			case "gif":
					$image 		= imagecreatefromgif($filename);
				break;	
			default:
					$image 		= imagecreatefromjpeg($filename);
		}
		
		// Scale Image
		imagecopyresampled($image_p, $image, 0, 0,0, 0, $width * $percent, $height * $percent, $width, $height);
		
		// Crop Image
		$new_width  = intval($_GET['crWidth']);
		$new_height = intval($_GET['crHeight']);
		$fromX = intval($_GET['crLeft']);
		$fromY = intval($_GET['crTop']);
		$image_final 	= imagecreatetruecolor($new_width, $new_height);

		switch(strtolower(end(explode(".",$filename)))){
			case "png":
					imagealphablending($image_final, false);
				break;
		}
		// Perform Crop
		imagecopyresampled($image_final, $image_p,0, 0,$fromX * $percent,$fromY * $percent, $new_width, $new_height, $new_width, $new_height);
		
		
		$r = false;
		switch(strtolower(end(explode(".",$filename)))){
			case "png":
				imagesavealpha($image_final, true);
				$r = imagepng($image_final, sketch("abspath")."sketch-images".sketch("slash").$_GET['image_name'],0);
				break;
			case "gif":
				$r = imagegif($image_final, sketch("abspath")."sketch-images".sketch("slash").$_GET['image_name']);
				break;	
			default:
				$r = imagejpeg($image_final, sketch("abspath")."sketch-images".sketch("slash").$_GET['image_name'],100); 
		}
		
		// Free Memory
		@imagedestroy($image_final);
		@imagedestroy($image_p);
		@imagedestroy($image);
		if($r){
			header("location: ". urlPath("cropmyimage?image=".$_GET['image_name']));
			exit();	
		}else{
			echo "Error!! - cannot save file - It may be too large or a type that is not supported";	
		}
	}
	if(isset($_REQUEST['image'])){
		$this->showImage($_REQUEST['image']);
	}else{
		echo "No Image";	
	}
  }
  function showImage($image){
	 $image = sketch("abspath")."sketch-images".sketch("slash").$image;
	 if(!is_file($image)){
		echo "File not found! Please select a different image";
	 }else{
		list($width, $height, $type, $attr) = getimagesize($image);
		?>
        <!DOCTYPE HTML>
        <html>
        <head>
        <meta charset="UTF-8" />
        <title><?php echo strip_tags(sketch("page_title")); ?></title>
		<?php
		getStylePath();
		getScriptPath(); 
		?>
        <style type="text/css">
			.crhandle{
				width:6px;height:6px;
				border:1px solid #ccc;
				background-color:#666;
				position:absolute;
			}
			.crtop{
				margin-top:-4px;
			}
			.crbottom{
				margin-top:-4px;
			}
			.crleft{
				margin-left:-4px;
			}
			.crright{
				margin-left:-4px;
			}
			.iMove{
				background-color:#000;	
			}
			.crbox.crhandle{
				width:auto;
				height:auto;
				background-color:#fff;
				bottom:0px;right:0px;top:0px;left:0px;position:absolute;
				border:1px dashed #666;
				z-index:10;
			}
		</style>
        </head>
        <body style="background-image:none;padding:0px;margin:0px;">
        <div id="cropFrame" style="margin:5px;position:relative;float:left;padding:0px">
        <div id="theGrabber" style="padding:5px;border:1px solid #667788;float:left;clear:both">
        <div id="theMasterContainer">
        <div style="position:relative;height:<?php echo $height; ?>px;width:<?php echo $width; ?>px;">
        	<div id="scaleImageIn" class="round crhandle scalein" style="position:absolute;left:0;top:0px;height:5%;min-height:6px;margin-top:-6px;z-index:2;margin-left:-10px;border:1px solid #ccc;"></div>
        	<img src="<?php echo urlPath("sketch-images/".$_REQUEST['image']); ?>" style="height:100%;width:100%;position:absolute;top:0px;left:0px;z-index:0" id="theImage"/>
        	<div class="crbox crhandle" id="box">
                	<div class="crhandle round crtop 	crleft" style="left:0px;top:0px;"></div>
                    <div class="crhandle round crtop 	crright" style="left:100%;"></div>
                    <div class="crhandle round crbottom crleft" style="left:0;top:100%"></div>
                    <div class="crhandle round crbottom crright" style="left:100%;top:100%"></div> 
            </div>
        </div>
        </div>
        </div>
        
        
        <ul class="forms" style="margin:5px;position:relative;">
            <li>
            <?php $newName = explode(".",stripslashes(urldecode(trim($_REQUEST['image'])))); 
				  $namec = strpos($_REQUEST['image'],"_crop") !== false ? $newName[0] : $newName[0] ."_crop".date("ymdhis"); 
			?>
        	<input type="text" name="image_name" id="newCr_name" class="required" value="<?php echo $namec.".".end($newName); ?>" style="width:270px"/>
            </li>
            <li><label style="padding-left:0px;margin-left:0px;width:30px;text-align:left;padding-right:0px">Width</label><input type="text" id="theCrWdith" value="<?php echo $width; ?>" style="width:25px;">
            	<label style="width:45px;padding-right:0px">Height</label><input type="text" id="theCrHeight" value="<?php echo $height; ?>" style="width:25px">
         
            <button type="button" onclick="if($(this).get('html')=='Preview'){ $('CRhider').setStyles({left:0,top:0}); $(this).set('html','Re-edit');}else{$('CRhider').setStyles({left:-9999,top:-9999}); $(this).set('html','Preview');} return false" style="margin-left:10px;">Preview</button>
            <button name="savechanges" class="positive" type="submit" onclick="updateScaleForm(); return false" style="margin-left:0px;">Save</button>
            </li>
            </ul>
        </div>
        
        <div id="CRhider" style="position:absolute;left:-9999px;top:-9999px;padding:5px;height:<?php echo $height + 10; ?>px;width:<?php echo $width + 10; ?>px;background:#fff;z-index:999">
            <div style="margin:0;border:1px solid #778899;float:left;clear:both;padding:5px;position:relative;margin:auto;" id="stablizer">
                <div style="position:relative;overflow:hidden;height:<?php echo $height; ?>px;width:<?php echo $width; ?>px" id="cropImage">
                <div style="position:relative;overflow:hidden;height:<?php echo $height; ?>px;width:<?php echo $width; ?>px" class="toScale">
                <img src="<?php echo urlPath("sketch-images/".$_REQUEST['image']); ?>" style="height:100%;width:100%;position:absolute;top:0px;left:0px" id="theInnerImage"/>
                </div>
            </div>
            </div>
        </div>
        <div id="crResult"></div>
       	<script type="text/javascript">
			$('box').fade(0.5);
			var move 			= false;
			var target 			= false;
			var startFrom 		= null;
			var offSetX 		= 0;
			var offSetY 		= 0;
			var scaleFactor 	= 1;
			var startingPos 	= $('theImage').getSize();
			var startingPlace 	= $('theImage').getPosition();
			var startScalePos 	= $('scaleImageIn').getPosition();	
			$('theCrWdith').addEvent("keyup",function(){
				if(isNaN(this.value) || this.value > <?php echo $width; ?> || $(this).value < 0){
					this.value = <?php echo $width; ?>;
				}
				$('box').setStyles({"left":0,"right":startingPos.x - $(this).value});
			});
			$('theCrHeight').addEvent("keyup",function(){
				if(isNaN(this.value) || this.value > <?php echo $height; ?> || $(this).value < 0){
					this.value = <?php echo $height; ?>;
				}
				$('box').setStyles({"top":0,"bottom":startingPos.y - $(this).value});
			});
			function updateScaleForm(){
				var sf = 1-scaleFactor > 0? 1-scaleFactor: 1
				var boxPos = $('box').getPosition();
				var left = boxPos.x - startingPlace.x;
				var top  = boxPos.y - startingPlace.y;
				var width = $('box').getSize().x * sf;
				var height = $('box').getSize().y * sf;
				var str = "<?php echo urlPath("cropmyimage"); ?>?savechanges=true&crScale=" + sf + "&crLeft=" + left + "&crTop=" + top + "&crWidth="+width + "&crHeight="+height + "&image_name="+$('newCr_name').value+"&image=<?php echo $_REQUEST['image']; ?>";
				window.location = str;
			}
			function updateHeightandWidthBoxes(){
				var sf = 1-scaleFactor > 0? 1-scaleFactor: 1
				var boxPos = $('box').getPosition();
				var left = boxPos.x - startingPlace.x;
				var top  = boxPos.y - startingPlace.y;
				var width = $('box').getSize().x * sf;
				var height = $('box').getSize().y * sf;	
				
				$('theCrHeight').value = height;
				$('theCrWdith').value = width;
			}
			$('cropFrame').addEvent("mousemove",function(event){
					if(move && target){
						var sf = 1-scaleFactor > 0? 1-scaleFactor: 1;
						var ev = new Event(event).page;
						if($(target).hasClass("scalein")){
							var offset = $(target).getSize().y / 2;
							if(ev.y - startScalePos.y > 0 && ev.y < startingPlace.y + startingPos.y){
								var per =  (((ev.y - startScalePos.y - offset)) / (startingPos.y));
								scaleFactor = per > 100? 100 : per < 0 ? 0 : per;
								$(target).setStyle('top',(scaleFactor * 100)+ "%");
							}
						}
						
						if($(target).hasClass("crleft")){
							if(ev.x - startingPlace.x > 0){
								$('box').setStyles({"left":ev.x - startingPlace.x});
							}else{
								$('box').setStyles({"left":0});
							}
						}
						if($(target).hasClass("crtop")){
							if(ev.y - startingPlace.y > 0){
								$('box').setStyles({"top":ev.y - startingPlace.y});
							}else{
								$('box').setStyles({"top":0});
							}
						}
						if($(target).hasClass("crright")){
							if(startingPos.x - (ev.x - startingPlace.x) > 0){
								$('box').setStyles({"right":startingPos.x - (ev.x - startingPlace.x)});
							}else{
								$('box').setStyles({"right":0});
							}
						}
						if($(target).hasClass("crbottom")){
							if(startingPos.y - (ev.y - startingPlace.y) > 0){
								$('box').setStyles({"bottom":startingPos.y - (ev.y - startingPlace.y)});
							}else{
								$('box').setStyles({"bottom":0});
							}
						}
						if($(target).hasClass("crbox")){
							var forSize = $('box').getSize();
							ev.x = ev.x - offSetX;
							ev.y = ev.y - offSetY;
							if(ev.x < startingPlace.x){
								ev.x = startingPlace.x;
							}
							if(ev.x + forSize.x > startingPlace.x + startingPos.x){
								ev.x = startingPlace.x + startingPos.x - $('box').getSize().x;
							}
							
							if(ev.y + forSize.y > startingPlace.y + startingPos.y){
								ev.y = startingPlace.y + startingPos.y - $('box').getSize().y;
							}
							if(ev.y < startingPlace.y){
								ev.y = startingPlace.y;
							}
							$('box').setStyles({
								"left":ev.x - $("theImage").getPosition().x,
								"top":ev.y - $("theImage").getPosition().y,
								"bottom":startingPos.y - ((ev.y + forSize.y) - startingPlace.y),
								"right":startingPos.x - ((ev.x + forSize.x) - startingPlace.x)
							});
						}
						var sf = 1-scaleFactor > 0? 1-scaleFactor: 1;
						var tleft = $('theImage').getPosition().x - $('box').getPosition().x;
						var ttop  = $('theImage').getPosition().y - $('box').getPosition().y;
						$('cropImage').setStyles({"width":$('box').getSize().x * sf,"height":$('box').getSize().y * sf,});
						$('theInnerImage').setStyles({"left":tleft * sf,"top": ttop * sf});
						scaleCrop();
					}
			});
			$(document.body).addEvent("mouseup",function(){
				move = false;
				target=false;
				$$('.iMove').removeClass("iMove");
			});
			$$('.crhandle').each(function(item,index){
				$(item).addEvent("mousedown",function(event){
					move=true;target=this;
					$(this).addClass("iMove");
					if($(this).hasClass("crbox")){
						var ev = new Event(event).page;
						var pos = $('box').getPosition(); 
						offSetX = ev.x - pos.x;	
						offSetY = ev.y - pos.y;
					}
					return false });
			});
			function scaleCrop(){
				var sf = 1-scaleFactor > 0? 1-scaleFactor: 1;
				$$(".toScale").setStyles({"width":startingPos.x * sf,"height":startingPos.y * sf});
				updateHeightandWidthBoxes();
			}
		</script>
        </body>
        </html>
		<?php
	 }
  }
}