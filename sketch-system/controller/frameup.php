<?php
class FRAMEUP extends CONTROLLER{
  function FRAMEUP($page){
    parent::__construct($page);
	if(!adminCheck()){
		die("Cannot Upload - Permission Denied");
	}
	if(isset($_POST['uploadme']) && $_POST['imagetoken']==$_SESSION['imagetoken']){
		$this->processFile();
	}else{
		$this->showForm();
	}
  }
 function showForm($msg=""){
	header("Cache-Control: no-cache, must-revalidate"); 
	header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); 
	header('Pragma: no-cache');
	$_SESSION['imagetoken']==md5(date("Y-m-d"));
	$validFiles = array();
	$validFiles["images"]=".jpg;.gif;.png;.jpeg";
	$validFiles["files"]=".pdf;.doc;.docx;.txt;.flv;.swf;.xls";
	?>
    <!DOCTYPE HTML>
	<html>
	<head>
	<meta charset="UTF-8" />
     <link href="<?php echo urlPath();?>styles?v=1337342400" type="text/css" rel="stylesheet" />
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/mootools/1.4.0/mootools-yui-compressed.js"></script>
	<script type="text/javascript" src="<?php echo urlPath(); ?>/scripts?v=1337342400"></script>
    <style type="text/css">
		body .alert-container{
			width:200px !important;	
		}
		body .sketch-alert{
			left:5px !important;	
		}
	</style>
    </head>
    <body style="padding:5px;margin:0px;background:#fff">
    <?php if($msg!=""){
			if($msg =="Success"){?>
            	<div class="notice" id="sucessarea" style="overflow:hidden;position:relative;margin:5px 0 250px 0">
                <?php echo $_SESSION['afile']; ?>
                Uploaded.</div>
                <script type="text/javascript">
					function hidesuccess(){
						$('sucessarea').morph({"height":0,"padding":0,"margin":0,"opacity":0});
					}
					hidesuccess.delay(1500);
				</script>
            <?php }else{ ?>
    			<div class="alert" id="sucessarea" style="overflow:hidden;position:relative;margin:0"><?php echo $msg; ?></div> 
    <?php 	} ?>
    <?php
		 } 
		 ?>
	<form class="required" style="margin:0px;width:95%" enctype="multipart/form-data"  onSubmit="if($('afile').get('value')!=''){$(document.body).spin(false);}" action="<?php echo urlPath("frameup"); ?>" target="_self" method="post" id="uploadform">
        	<input type="hidden" name="imagetoken" value="<?php echo $_SESSION['imagetoken']; ?>"/>
            <input type="hidden" name="folder" value="<?php echo urldecode(trim($_REQUEST['folder'])); ?>">
        	<div>
                	<input type="file" name="afile" class="required nojs bgClass:'somclass' fileTypes:'<?php echo strpos($_REQUEST['folder'],"sketch-images")!==false? $validFiles["images"]  : $validFiles["files"] ; ?>'" id="afile" title="Please select a file to upload" />
         	</div>
            <div>
                	<button type="submit" name="uploadme" onclick="$('uploadform').set('action', $('uploadform').get('action') +'?' + (Math.random()));">Upload</button>
            </div>
        </form>
        <?php if($msg=="Success"){?>
        	<div style="float:left;clear:both;margin-top:5px;background:#fff;width:100%;position:relative">
        		<img src="<?php echo urlPath(trim($_REQUEST['folder'],"/")."/".$_SESSION['afile']); ?>" style="height:auto;width:90%;"/>
             </div>
        <?php }?>
     </body>
     </html>
    <?php 
  }
  function processFile(){
	  	$POST_MAX_SIZE  = ini_get('post_max_size');				// GET THE FILE SIZE UPLOAD LIMIT from the server
	 	$max_file_size_in_bytes = 20971520;							// 20 mb in bytes
		$valid_chars_regex 		= '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';	// Characters allowed in the file name (in a Regular Expression format)
		$MAX_FILENAME_LENGTH 	= 200;								// Set the maximum length of the file name
		$file_name 				= "";
		$file_extension 		= "";
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
		$canUpload = true;
		if(!in_array(end(explode(".",$_FILES['afile']["name"])),array("gif","jpg","doc","pdf","png","xls","docs","docx"))){
			$this->showForm("Invalid File Type");																	// File too big - Exit
			$canUpload = false;
		}
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			$this->showForm("Sorry the file is too big");
			$canUpload = false;
		}
		if($canUpload == true){
			$file_size = @filesize($_FILES['afile']["tmp_name"]);									// Get the File size
			if (!$file_size || $file_size > $max_file_size_in_bytes) {
				$this->showForm("The File size is too big");																	// File too big - Exit
				$canUpload = false;
			}
		}
		if($canUpload == true){
			if ($file_size <= 0) {																		// No File size
				$this->showForm("File size is too small");																// Possible error - Exit
				$canUpload = false;
			}
		}
		
		if($canUpload == true){
			$file_name = str_replace(" ","",preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES['afile']['name']))); // Remove invalid Characters from the name
			if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {					// check name length
				$this->showForm("Invalid File name. Please change the file name and try again");																	// Name too long - Exit
				$canUpload = false;
			}
			$_REQUEST['folder'] = stripslashes(trim($_REQUEST['folder'],"/"));
			$save_path 		= (isset($_REQUEST['folder']) && $_REQUEST['folder'] != "")? sketch("abspath").$_REQUEST['folder'].sketch("slash")  :   sketch("abspath")."sketch-images".sketch("slash");	// The path to save the file
			
			$save_path		= str_replace(array("/","//",sketch("slash").sketch("slash"),sketch("slash")),sketch("slash"),$save_path);	
			if (!@move_uploaded_file($_FILES['afile']["tmp_name"], $save_path.$file_name)) {
				$this->showForm("Sorry - the file could not be saved");
				$canUpload = false;
			}
			if($canUpload == true){
				$_SESSION['afile']	= $file_name;											// Save the file uploaded in the session data
				@chmod($save_path.$file_name,0777);
				$this->showForm("Success");
			}
		}
  }
}