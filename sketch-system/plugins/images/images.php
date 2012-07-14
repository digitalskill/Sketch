<?php
class IMAGES extends PLUGIN {
    function IMAGES($args) {
	$settings = array("location" => "meta", "global" => 1, "php" => 1, "admin" => 1, "css" => 1, "menuName" => "Assets", "topnav" => 1, "pluginsection" => "Assets", "adminclass" => "showReEdit:false showPreview:false showPublish:false showSave:false"); // [ OPTIONAL - pageEdit | js | css | php | global | location | admin | topnav ]
	$this->start($settings, $args);
    }

    function update($old, $new) {    // [ REQUIRED ]
	return $new;
    }

    function display($args='') {		     // [ REQUIRED ]
	$this->preview();
    }

    function preview() {    // [ REQUIRED ]
	global $sketch;
	$alli = array_merge($sketch->getImages(), $sketch->getFiles()); ?>
<ul class="imagelist" id="imagesidelist"><?php foreach ($alli as $key => $value) { ?>
        <li><label><span class="icon drop"></span><?php echo end(explode($sketch->slash, rtrim($key, $sketch->slash))); ?></label><ul><?php
		foreach ($value as $k => $v) {
		    if (isset($v['details']['width'])) {
?>
	    	    <li><div style="position:relative"><img src="<?php echo str_replace("index.php", "", urlPath($k)); ?>" alt=""/></div></li>
	    <?php } else {
			if (isset($v['file']) && trim($v['file']) != "") { ?>
			    <li><div style="position:relative"><a href="<?php echo str_replace("index.php", "", urlPath($k)); ?>" title="<?php echo end(explode($sketch->slash, $k)); ?>" alt="<?php echo end(explode($sketch->slash, $k)); ?>"><?php echo end(explode($sketch->slash, $k)); ?></a></div></li>
    <?php
			}
		    }
		}
    ?>
	    	</ul></li>
<?php } ?>
	</ul>
	<script type="text/javascript">
		var tmpImg;
	    function fadeImageList(){
		$("imagesidelist").getElements("li").each(function(item,index){
		    if($(item).getElements("ul").length > 0){
				$(item).addEvent("click",function(){$(this).toggleClass("hover");});
		    }else{
				$(item).addEvent("click",function(event){ new Event(event).stop();});
				$(item).addEvent("mouseenter",function(){if((this).getElement("div")){$(this).getElement("div").fade(0.5);}});
				$(item).addEvent("mouseleave",function(){if((this).getElement("div")){ $(this).getElement("div").fade(1);}});
		    }
		});
		$("imagesidelist").getElements("a").each(function(item,index){
		    $(item).addEvent("click",function(event){ new Event(event).stop();});
		});
		$("imagesidelist").getElements("div").each(function(item,index){
			$(item).addEvent("mousedown",function(event){ 
				new Event(event).stop();
				tinyMCE.execCommand("mceInsertContent", false, $(this).get("html") );
			});
		});
	    }
	    fadeImageList.delay(500);
	</script>
<?php
	}

	function showForm() {     // [ OVERRIDE ]
	    global $_SESSION,$_GET;
	    $_SESSION['imagetoken'] = md5(date("Y-m-d"));
		if(isset($_GET['outside'])){
			?><form class="required" method="post"><?php	
		}
	    $this->form();
		if(isset($_GET['outside'])){
			?>
			</form>
			<script type="text/javascript">
				function startoutform(){
					$$('form').each(function(item,index){
						new Validate(item);
					});
					$$('input[type=file]').each(function(item,index){ 	// Get all file feilds on the form
						uploaders[index] = new uploader(item);
					});
					$$(".folders").addEvent("click",function(event){
						var ev = new Event(event);
						if($(ev.target).get("src") != ""){
							var src = $(ev.target).get("src").split("sketch-images");
							$("i<?php echo $_REQUEST['area']; ?>").set("value","sketch-images"+src[1]);
							$("im<?php echo $_REQUEST['area']; ?>").set("src",$(ev.target).get("src"));
							$(document.body).fireEvent("closepop");
						}else{
							if($(ev.target).get("href") != ""){
								var src = $(ev.target).get("href").split("sketch-files");
								$("i<?php echo $_REQUEST['area']; ?>").set("value","sketch-files"+src[1]);
								$("im<?php echo $_REQUEST['area']; ?>").set("src","");
								$(document.body).fireEvent("closepop");
							}
						}
					});
				}
				startoutform.delay(500);
			</script><?php
		}
	}

	function doUpdate() {     // [ OVERRIDE ]
	    if (adminCheck ()) {
		global $_REQUEST;
		if (isset($_REQUEST['remove'])) {
		    $file = trim($_REQUEST['remove']);
		    $r = @unlink($file);
		    if ($r) {
 ?>
			<script type="text/javascript">
			    if($("<?php echo $_REQUEST['item']; ?>")){
				$("<?php echo $_REQUEST['item']; ?>").addClass("hide");
			    }
			</script>
<?php
		    }
		} else {
		    if (isset($_REQUEST['newimagefolder']) && trim($_REQUEST['newimagefolder']) != "") {
			$newFolder = str_replace(array(" ", "\\", "/", "&", "$", "#", "@", "^", "*", ".", "+", "=", "~", "`", '"', "'"), "", strtolower(trim($_REQUEST['newimagefolder'])));
			@mkdir(sketch("abspath") . "sketch-images" . sketch("slash") . $newFolder);
			@chmod(sketch("abspath") . "sketch-images" . sketch("slash") . $newFolder, 0777);
		    }
		    if (isset($_REQUEST['newfilefolder']) && trim($_REQUEST['newfilefolder']) != "") {
			$newFolder = str_replace(array("%", " ", "\\", "/", "&", "$", "#", "@", "^", "*", ".", "+", "=", "~", "`", '"', "'"), "", strtolower(trim($_REQUEST['newfilefolder'])));
			@mkdir(sketch("abspath") . "sketch-files" . sketch("slash") . $newFolder);
			@chmod(sketch("abspath") . "sketch-files" . sketch("slash") . $newFolder, 0777);
		    }
?>
		    <script type="text/javascript">
		        $("section_Assets").getElement(".current").fireEvent("click");
		    </script><?php
		}
	    }
	    exit();
	}

	function form() {       // [ REQUIRED ]
	    global $sketch; ?>
<div id="asset_menu" class="AdminSubMenu tabs:'a' boxcontainer:'asset_container' boxes:'.asset_box'">
    <a class="current button"><span class="icons book"></span>Images</a><a class="button"><span class="icons book"></span>Files</a><a class="button"><span class="icons magnifier"></span>Search</a>
</div>
<div id="asset_container" style="clear:both;">
    <div class="asset_box">
    <table border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
    <td valign="top" align="left" width="200" style="position:relative;vertical-align: top;">
	<div id="directory_image_list" class="tabs:'li' boxcontainer:'directory_i_images' boxes:'.folders' directory_list" style="width:100%;position:relative;top:5px;left:0;">
		<?php
		$count = 0;
		$alli = $sketch->getImages();
		ksort($alli);
		?>
	    <ul>
<?php foreach ($alli as $key => $value) { ?>
    		<li class="<?php if ($count == 0) { ?>current<?php } ?> input">Images/<?php echo end(explode("sketch-images" . $sketch->slash, $key)); ?></li>
<?php
		    $count++;
		}
?>
    	    </ul>
    	</div>
<?php if(!isset($_GET['outside'])){?>
        <div style="background-image:none;border:none;" class="iformholder">
    		    <form id="newimagefolderform" class="required ajax:true output:'load-box'" style="width:160px !important;clear:both;float:left;position:relative" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>">
    			<label>Create new folder</label>
    			<input type="text" name="newimagefolder" class="required" />
    			<button type="submit" >Create Folder</button>
    		    </form>
    		</div>
<?php } ?>
        </td><td valign="top" align="left" style="vertical-align: top;">
    	<ul id="directory_i_images" style="position:relative;width:95%">
		<?php
		$count = 0;
		$subcount = 0;
		foreach ($alli as $key => $value) {
 ?>
    	    <li class="folders <?php if ($count > 0) {
 ?>hide<?php } ?>">
		<div class="upload_container accordian">
        	<a class="accord-title button" style="width:95%" ><span class="icons downarrow"></span>Flash Upload</a>
            <div class="accord-body">
	    		<div class="accord-container" style="height:300px;width:95%;padding:5px;">
		    		<div class="inner_upload">
					<input type="file" rel="{types:'*.jpg;*.gif;*.png;',upload_url:'<?php echo str_replace("index.php/","", urlPath("/sketch-upload/upload.php")); ?>',params:{folder:'sketch-images<?php echo rtrim(str_replace("//","/",end(explode("sketch-images", $key))),"/"); ?>/',PHPSESSID:'<?php echo session_id(); ?>'}}"/>
		    		</div>
            	</div>
            </div>
            <a class="accord-title button" style="width:95%"><span class="icons downarrow"></span>Standard Upload</a>
            <div class="accord-body">
	    		<div class="accord-container" style="width:95%;padding:5px;">
            		<iframe frameborder="0" src="<?php echo urlPath("frameup"); ?>?folder=<?php echo urlencode("sketch-images". rtrim(str_replace("//","/",end(explode("sketch-images", $key))),"/")); ?>/" height="250" width="100%" scrolling="no"></iframe>
            	</div>
            </div>
		</div>
		    <?php
		    if (count($value) > 0) {
			foreach ($value as $k => $v) {
			    if (isset($v['details']['width'])) {
				$subcount++; ?>
	    		<div class="admin_image" rel="<?php echo end(explode($sketch->slash, $k)); ?>" id="admin_image<?php echo $subcount; ?>">
		    <?php
				$w = $v['details']['width'];
				$h = $v['details']['height'];
				$percent = 1;
				if ($w >= $h) {
				    $percent = 100 / $w;
				} else {
				    $percent = 100 / $h;
				}
				$h = $h * $percent;
				$w = $w * $percent;
				if ($w < 100) {
				    $w = "100%";
				    $h = "auto";
				} else {
				    if ($h < 100) {
					$w = 'auto';
					$h = "100px";
				    }
				}
		    ?>
    		    <img src="<?php echo str_replace("index.php/", "", urlPath($k)); ?>"  alt="" />
				<?php if(!isset($_GET['outside'])){?> <div class="del hide">
		<?php if (count($v['usedon']) < 1) {
 ?>
		    			<a href="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>?remove=<?php echo $k; ?>&item=admin_image<?php echo $subcount; ?>" class="button negative ajaxlink output:'admin_image<?php echo $subcount; ?>'">Delete</a>
	    <?php } else {
 ?>&nbsp;<?php } ?>
 						<div><a href="<?php echo urlPath('cropmyimage'); ?>?image=<?php echo end(explode("sketch-images/",$k)); ?>" class="button positive popup iframe:true width:<?php echo ($v['details']['width'] > 270)? $v['details']['width'] + 40 : 270+40;?> height:<?php echo ($v['details']['height']) + 130; ?>" style="color:#778899;margin-left:-2px;border:none;">Edit Image</a></div>
		    			<div>Width:  <?php echo $v['details']['width']; ?>px</div>
		    			<div>Height: <?php echo $v['details']['height']; ?>px</div>
		    			<div>Used on:
<?php foreach ($v['usedon'] as $ke => $va) { ?>
<?php echo $va . "<br />"; ?>
	    <?php } ?></div>
		    		    </div>
                        <?php } ?>
		    		    <div class="image_name"><?php echo str_ireplace(array("_", "-", ".jpg", ".png", ".gif", ".jpg"), " ", end(explode("/", $k))); ?></div>
		    		</div>
<?php
			    }
			}
		    } ?>
    	    </li>
		<?php
		    $count++;
		}
		?>
	</ul>
    </td></tr></table>
    </div>
    <div class="asset_box hide">
    <table cellpadding="0" cellspacing="0" width="100%" border="0">
    <tr>
    <td valign="top" align="left" width="200" style="position:relative;vertical-align:top;">
	<div id="directory_file_list" class="tabs:'li' boxcontainer:'directory_f_images' boxes:'.folders' directory_list" style="width:100%;position:relative;top:5px;left:0">
<?php
		$alli = $sketch->getFiles();
		ksort($alli);
		$count = 0;
?>
    	    <ul>
	    <?php foreach ($alli as $key => $value) {
 ?>
			<li class="<?php if ($count == 0) { ?>current<?php } ?> input">Files/<?php echo end(explode("sketch-files" . $sketch->slash, $key)); ?></li>
<?php
		    $count++;
		}
?>
<?php if(!isset($_GET['outside'])){?>
    		<li style="background-image:none;border:none;" class="iformholder">
    		    <form id="newfilefolderform" class="required ajax:true output:'load-box'" method="post" action="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>">
			<label>Create new folder</label>
			<input type="text" name="newfilefolder" class="required" />
			<button type="submit">Create Folder</button>
		    </form>
		</li>
<?php } ?>
	    </ul>
	</div>
    </td>
    <td valign="top" align="left" style="vertical-align: top;">
	<ul id="directory_f_images" style="position:relative;width:95%">
			<?php
			$count = 0;
			$subcount = 0;
			foreach ($alli as $key => $value) {
			?>
    	    <li class="folders <?php if ($count > 0) {
 ?>hide<?php } ?>">
 
 		<div class="upload_container accordian">
        	<a class="accord-title button" style="width:95%" ><span class="icons downarrow"></span>Flash Upload</a>
            <div class="accord-body">
	    		<div class="accord-container" style="height:300px;width:95%;padding:5px">
		    		<div class="inner_upload">
					<input type="file" rel="{types:'*.pdf;*.doc;*.docx;*.ppt;*.flv;*.swf;*.mp3;*.wav;*.dot;*.xls;',upload_url:'<?php echo str_replace("index.php/","", urlPath("/sketch-upload/upload.php")); ?>',params:{folder:'sketch-files<?php echo rtrim(str_replace("//","/",end(explode("sketch-files", $key))),"/"); ?>/',PHPSESSID:'<?php echo session_id(); ?>'}}"/>
		    		</div>
            	</div>
            </div>
            
            <a class="accord-title button" style="width:95%"><span class="icons downarrow"></span>Standard Upload</a>
            <div class="accord-body">
	    		<div class="accord-container" style="width:95%;padding:5px;">
            		<iframe frameborder="0" src="<?php echo urlPath("frameup"); ?>?folder=<?php echo urlencode("sketch-files". rtrim(str_replace("//","/",end(explode("sketch-files", $key))),"/")); ?>/" height="250" width="100%" scrolling="no"></iframe>
            	</div>
            </div>
		</div>
		<?php
			    if (count($value) > 0) {
				foreach ($value as $k => $v) {
				    if (is_file($k)) {
					$subcount++;
					$theRel = trim(end(explode($sketch->slash, $k)));
					if ($theRel != "") {
 ?>
						<div class="admin_image" rel="<?php echo $theRel; ?>" id="admin_file<?php echo $subcount; ?>">
						    <div><?php echo end(explode($sketch->slash, $k)); ?></div>
						    <div class="del hide">
<?php if (count($v['usedon']) < 1) { ?>
				    			<a href="<?php echo urlPath("admin"); ?>/admin_plugin_<?php echo $this->settings['name']; ?>?remove=<?php echo $k; ?>&item=admin_file<?php echo $subcount; ?>" class="ajaxlink output:'admin_file<?php echo $subcount; ?>'">Delete</a>
<?php } ?>
							<div>
							    <a href="<?php echo str_replace("index.php/", "", urlPath($k)); ?>" target="_blank" style="color:#00F;">View (opens a new window)</a>
<?php if (count($v['usedon']) > 0) { ?> Used on:
<?php foreach ($v['usedon'] as $ke => $va) { ?>
<?php echo $va . " | "; ?>
<?php }
					    } ?></div>
						    </div>
						</div>
<?php
					}
				    }
				}
			    }
?>
			    	    </li>
<?php
			    $count++;
			}
?>
				</ul>
               </td></tr></table>
			    </div>
			    <div class="asset_box hide">
				<ul class="form"><li><label>Search Text</label><input type="text" name="image_search" id="image_search" style="width:120px"/></li>
				    <li class="folders" id="search_result">

				    </li>
				</ul>
			    </div>
			</div>
			<script type="text/javascript">
				function editPops(){
					$$('.popup').each(function(item,index){
						new Popup(item);
					});
					$("asset_container").getElements('.accordian').each(function(item,index){
	    				new accord(item);
    				});
				}
				editPops.delay(500);
			    imageTabs.delay(500);
    			
				
			</script>
<?php
		    }

		}