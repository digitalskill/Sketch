<?php
session_start();
if(!is_file("sketch.sql")){
    session_destroy();
    ?><h4 style="color:#CB441A;">NO SQL FILE FOUND - CANNOT COMPLETE SETUP</h4>
	<p>Please ensure that install.php and sketch.sql are on the server in this folder.<br />

	If you have already run the setup - you may need to delete the install.php file manually.</p>
    <?php
    exit();
}
require_once("../config.php");
$error="";
$showSteps = true;
if(!is_writable("../sketch-files") || !is_writable("../sketch-images") || !is_writable("../config.php")){
    @chmod("../sketch-files",0777);
    @chmod("../sketch-images",0777);
    @chmod("../config.php",0777);
}

if(!is_writable("../sketch-files") || !is_writable("../sketch-images") || !is_writable("../config.php")){
    $error = "<h2 style='color:#CB441A;'>Cannot Create Setup Files</h2><p>SERVER SAID: sketch-images, sketch-files and config.php files cannot be updated</p>
                <p>You will need to use FTP to make the files and folders below writable:<br />Permissions should be 777 or read/write for the following:<p>
                <p><strong>".str_replace("/sketch-admin","",dirname(__FILE__)) ."/config.php</strong><br />
                <strong>".str_replace("/sketch-admin","",dirname(__FILE__)) ."/sketch-files</strong><br />
                <strong>".str_replace("/sketch-admin","",dirname(__FILE__)) ."/sketch-images</strong></p>
                <p>Still not sure what to do? <br />Use an FTP program or your servers control panel.<br />
                Still not sure? Contact your webmaster or web host for help.</p>";
    $showSteps = false;
     $_REQUEST['step']=1;
     $_POST['step']=1;
}

if(isset($_POST['step']) && $_POST['step']==2){
    $db =  @mysql_pconnect($_POST['hostname'], $_POST['username'], $_POST['password']);			// Connect to the database
    $r  =  @mysql_select_db($_POST['database']);

    if((!$r || !$db) && $_POST['dbtype'] != "NO"){
       $_REQUEST['step']=1;
       $_POST['step']=1;
       $error = "<h2 style='color:#CB441A;'>Database information not correct</h2><p style='padding-bottom:17px'>MYSQL SERVER SAID: ".mysql_error()."</p>";
    }
}

if(isset($_POST['step'])){
    foreach($site_settings as $key => $value){
        if(isset($_POST[$key])){
            if($value===true || $value===false){
                $site_settings[$key] = ($_POST[$key]==1)? true : false;
            }else{
                $site_settings[$key] = trim(stripslashes($_POST[$key]));
            }
        }
    }
}

if(isset($_POST['install'])){
        foreach($site_settings as $key => $value){
            if(isset($_POST[$key])){
                if($value===true || $value===false){
                    $site_settings[$key] = ($_POST[$key]=="true" || $_POST[$key]==1)? true : false;
                }else{
                    $site_settings[$key] = trim(stripslashes($_POST[$key]));
                }
            }
        }
	$db = false;
	$r = true;
	if($site_settings['dbtype']!= "NO"){
	    $db =  @mysql_pconnect($site_settings['hostname'], $site_settings['username'], $site_settings['password']) or die("Cannot connect to database - please do the setup again");			// Connect to the database
	    @mysql_select_db($site_settings['database']) or die("Database not found - please do the setup again");
	}
        $string = '<?php '."\r\n".'$site_settings = array();'."\r\n";
        foreach($site_settings as $key => $value){
            if($value=="true" || $value=="false"){
                $string .= '$site_settings["'.$key.'"] = '.$value.';'."\r\n";
            }else{
                $string .= '$site_settings["'.$key.'"] = "'.stripslashes(trim($value)).'";'."\r\n";
            }
        }
        $string .= 'define("SALT", "'.stripslashes(trim($_POST['salt'])).'");';
        file_put_contents("../config.php",$string) or die("Cannot create Config File");
	function secureit($text,$decode=false,$newSalt=""){
                $salt = ($newSalt=="")? SALT : $newSalt;
		if(function_exists("mcrypt_cbc") && function_exists("mcrypt_create_iv") && trim($text)!= ''){
			if($decode){
				return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $salt, base64_decode($text), MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND)));
			}else{
				return trim(base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $salt, $text, MCRYPT_MODE_ECB, mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB), MCRYPT_RAND))));
			}
		}
		return $text;
	}
	if($db){
		$data 	= str_replace(array("__",");","INSERT","DROP","CREATE",");","`;","WRITE;","TABLES;","PRE"),array("_".$site_settings['prefix']."_","---","#;-;#INSERT","#;-;#DROP","#;-;#CREATE",")#;-;#","`#;-;#","WRITE","TABLES",$site_settings['prefix']),file_get_contents("sketch.sql"));
		$data 	= str_ireplace(array("TABLE `","INTO `","EXISTS `","REFERENCES `"),array("TABLE `".$site_settings['prefix'],"INTO `".$site_settings['prefix'],"EXISTS `".$site_settings['prefix'],"REFERENCES `".$site_settings['prefix']),$data);
		
		$data = explode("#;-;#",$data);
		foreach($data as $key => $v){
			if(trim($v) != ""){
				@mysql_query(str_replace("---",");",$v),$db) or die(mysql_error()." ".$v);
			}
		}
		$SQL = "INSERT INTO ".$site_settings['prefix']."users (user_login,user_password,is_super) VALUES('".mysql_real_escape_string($_POST['user_login'])."','".mysql_real_escape_string(secureit($_POST['user_password'],false,stripslashes(trim($_POST['salt']))))."',1)";
		mysql_query($SQL,$db) or die(mysql_error());
		$SQL = "INSERT INTO ".$site_settings['prefix']."sketch_settings (sketch_settings_id,global_update,theme_path,main_site_url) VALUES (1,now(),'".mysql_real_escape_string(trim($_POST['theme_path']))."','".mysql_real_escape_string(end(explode("www.",$_POST['main_site_url'])))."')";
		mysql_query($SQL,$db) or die(mysql_error());
		$r = true;
	}
	if($r){
		if(is_file("../.htaccess")){
			$theFile  = file_get_contents("../.htaccess");
			if(trim($_POST['main_site_url'],"/")!="" && trim($_POST['main_site_url'],"/")!="localhost" && stripos(trim($_POST['main_site_url'],"/"),".")===false){
				$theFile = str_replace("RewriteBase /","RewriteBase /".trim(trim($_POST['main_site_url']),"/")."/",$theFile);
				chmod("../.htaccess",0777);
				file_put_contents("../.htaccess",$theFile);
				chmod("../.htaccess",0644);
			}
		}
	    unlink("sketch.sql");
	    unlink("install.php");
	    header("location: ../index.php?admin");
	    die();
	}else{
	    echo "Install Failed!!";
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
	<title>Install</title>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name='robots' content='noindex,nofollow' />
<?php $direc = scandir("../sketch-system/plugins/general/");
	foreach($direc as $key => $value){
		if(strpos($value,".css")!==false){
		 ?><link href="../sketch-system/plugins/general/<?php echo $value; ?>" rel="stylesheet" type="text/css" /><?php
		}
	}
?>
<?php $direc = scandir("../sketch-system/plugins/admin/");
	foreach($direc as $key => $value){
		if(strpos($value,".css")!==false){
		 ?><link href="../sketch-system/plugins/admin/<?php echo $value; ?>" rel="stylesheet" type="text/css" /><?php
		}
	}
?>
<?php $direc = scandir("../sketch-system/core/scripts/");
	foreach($direc as $key => $value){
		if(strpos($value,".js")!==false){
		 ?><script type="text/javascript" src="../sketch-system/core/scripts/<?php echo $value; ?>"></script><?php
		}
	}
?>
<?php $direc = scandir("../sketch-system/plugins/general/");
	foreach($direc as $key => $value){
		if(strpos($value,".js")!==false){
		 ?><script type="text/javascript" src="../sketch-system/plugins/general/<?php echo $value; ?>"></script><?php
		}
	}
?>

<style type="text/css">
	label{
		display:block;
		width:100%;
		clear:both;	
	}
</style>
</head>
<body style="padding-top:5px;">
<div id="load-box">
<div id="login" style="padding:20px;background-color:#FFFFFF; width:450px; border:1px solid #CCCCCC; margin:auto;margin-top:5px;">
    <div class="sketch-logo" style="float: right;margin-right: 8px;margin-top: -17px;width:50px;"><span>sketch</span></div>
<div><?php echo $error; ?></div>
<?php if($showSteps){?>
<form name="loginform" id="loginform" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" class="required form">
   <div style="display: <?php echo (!isset($_REQUEST['step']) || $_REQUEST['step']==1)? "block" : "none"; ?>">
    <h2 style="color: #666666;font-size: 1em;font-weight: normal;margin-bottom: 0;margin-top: -17px;padding-bottom: 0;padding-left: 2px;text-transform:uppercase;">Progress : Step 1 of 3</h2>
    <p>
        <label>Database Name (You need to create the database first - <em style="font-style:italic">Contact your web host if unsure</em>)</label>
        <input type="text" name="database" <?php if(isset($r) && !$r){?>style="color:#CB441A;"<?php } ?> value="<?php echo $site_settings['database']; ?>"/>
    </p>
    <p><label>Database Table Prefix</label>
        <input type="text" name="prefix" value="<?php echo $site_settings['prefix']; ?>" />
    </p>
    <p><label>Database Host (Contact your web host if unsure)</label>
        <input type="text" name="hostname" value="<?php echo $site_settings['hostname']; ?>" />
    </p>
    <p><label>Database Username (Contact your web host if unsure)</label>
        <input type="text" name="username" value="<?php echo $site_settings['username']; ?>" />
    </p>
    <p><label>Database Password (Contact your web host if unsure)</label>
        <input type="text" name="password" value="<?php echo $site_settings['password']; ?>" />
    </p>
    <p><label>Database Type</label>
        <select name="dbtype" >
            <option value="mysql" <?php if($site_settings['dbtype']=="mysql"){?>selected="selected"<?php } ?>>MySQL</option>
	    <option value="NO" <?php if($site_settings['dbtype']=="NO"){?>selected="selected"<?php } ?>>No Database - File system only</option>
        </select>
    </p>
    <p class="submit" style="margin-top:5px;float:left;width:100%;margin-bottom:5px;">
        <?php if(!isset($_REQUEST['step']) || $_REQUEST['step']==1){ ?><input type="hidden" name="step" value="2"/><?php } ?>
        <button type="submit" class="positive" style="float:right;"><span class="icons check"></span>NEXT</button>
	</p>
        <div class="clear" style="height:55px;"></div>
   </div>
 <div style="display: <?php echo (isset($_REQUEST['step']) && $_REQUEST['step']==2)? "block" : "none"; ?>">
    <h2 style="color: #666666;font-size: 1em;font-weight: normal;margin-bottom: 0;margin-top: -17px;padding-bottom: 0;padding-left: 2px;text-transform:uppercase;">Progress : Step 2 of 3</h2>
    <p><label>Show PHP Errors (If this site can be viewed by the public - set this to No)</label>
        <select name="show_php_errors" >
            <option value="1" <?php if($site_settings['show_php_errors']){?>selected="selected"<?php } ?>>Yes (Use this for testing or production servers only)</option>
            <option value="0" <?php if(!$site_settings['show_php_errors']){?>selected="selected"<?php } ?>>No</option>
        </select>
    </p>
    <p><label>Compress Output (Saves Bandwidth - Generally this should always be Yes) </label>
        <select name="compress" >
            <option value="1" <?php if($site_settings['compress']){?>selected="selected"<?php } ?>>Yes</option>
            <option value="0" <?php if(!$site_settings['compress']){?>selected="selected"<?php } ?>>No</option>
        </select>
    </p>
    <p><label>Put WWW in all urls <em>(This ensures that all site links have "www." in them)</em></label>
        <select name="www" >
            <option value="0" <?php if(!$site_settings['www']){?>selected="selected"<?php } ?>>No</option>
            <option value="1" <?php if($site_settings['www']){?>selected="selected"<?php } ?>>Yes</option>
        </select>
    </p>
    <p><label>Use .htacess (Unsure? Set it to "no" - sketch will manage this)</label>
        <select name="htaccess" >
            <option value="1" <?php if($site_settings['htaccess']){?>selected="selected"<?php } ?>>Yes</option>
            <option value="0" <?php if(!$site_settings['htaccess']){?>selected="selected"<?php } ?>>No</option>
        </select>
    </p>
    <p><label>Multiple Site access (If unsure - leave as Folder names)</label>
        <select name="directory" >
            <option value="1" <?php if($site_settings['directory']){?>selected="selected"<?php } ?>>Websites are accessed by Folder names</option>
            <option value="0" <?php if(!$site_settings['directory']){?>selected="selected"<?php } ?>>Websites are accessed by Domain Names</option>
        </select>
    </p>
    <p><label>Enable Caching (Make the site go faster)</label>
        <select name="cache" >
            <option value="1" <?php if($site_settings['cache']){?>selected="selected"<?php } ?>>Yes</option>
            <option value="0" <?php if(!$site_settings['cache']){?>selected="selected"<?php } ?>>No (select if site is in development)</option>
        </select>
    </p>
    <p><label>Get Mootools from Google (Google can deliver mootools instead of your site)</label>
        <select name="googleapi" >
            <option value="1" <?php if($site_settings['googleapi']){?>selected="selected"<?php } ?>>Yes</option>
            <option value="0" <?php if(!$site_settings['googleapi']){?>selected="selected"<?php } ?>>No (select if site is in development)</option>
        </select>
    </p>
    <p><label>Encryption Key (Used to encrypt passwords - DO NOT CHANGE THIS unless you are sure)</label>
        <input type="text" name="salt" id="theme_path" class="" value="<?php echo isset($_POST['salt'])?  trim(stripslashes($_POST['salt'])) : md5(rand()); ?>" size="20"/>
    </p>
    <p><label>Website Folder (Unsure? sketch will detect this for you)</label>
        <?php list($folder,) = explode("/sketch-admin",$_SERVER['REQUEST_URI']); ?>
        <input type="text" name="ignore" id="theme_path" class="" value="<?php echo trim(trim($folder),"/"); ?>" size="20"/>
    </p>
    <p><label>Path to sketch System Folder (if in site folder - sketch will attempt to find it)</label>
	<?php $pathtosk = str_replace("sketch-admin","",dirname(__FILE__)); ?>
        <input type="text" name="PathTosketch" id="theme_path" class="" value="<?php echo strpos(strtolower($pathtosk),"c:")===false? $pathtosk : "" ; ?>" size="20"/>
    </p>
    <p class="submit" style="margin-top:5px;float:left;width:100%">
        <?php if(isset($_REQUEST['step']) && $_REQUEST['step']==2){ ?><input type="hidden" name="step" value="3"/><?php } ?>
        <button type="submit" class="positive" style="float:right;"><span class="icons check"></span>NEXT</button>
        <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=1" style="float:left;"> &laquo; Back</a>
	</p>
        <div class="clear" style="height:55px;"></div>
 </div>
<?php if(isset($_POST['step']) && $_POST['step']==3){ ?>
  <h2 style="color: #666666;font-size: 1em;font-weight: normal;margin-bottom: 0;margin-top: -17px;padding-bottom: 0;padding-left: 2px;text-transform:uppercase;">Progress : Step 3 of 3</h2>
  <?php if($_POST['dbtype']!="NO"){?>
  <p>
		<label>Admin Name (This is what you will use to login to the site)</label>
		<input type="text" name="user_login" id="user_login" class="required" value="" size="20" />
	</p>
	<p>
		<label>Password (This is the password you will use to login to the site)</label>
		<input type="password" name="user_password" id="user_password" class="required" value="" size="20"/>
	</p>
  <?php } ?>
  <p>
		<label>Web site (sketch will attempt to locate)</label>
                   <?php $sitename = (isset($_POST['ignore']) && trim($_POST['ignore']) != '')?  $_POST['ignore']."/" : $_SERVER['HTTP_HOST'] ."/"; ?>
                   <input type="text" name="main_site_url" id="main_site_url" class="required" value="<?php echo $sitename; ?>" size="20"/>
    </p>
	<p>
		<label>Theme path (This is the path to your Themes folder)</label><br />
		<input type="text" name="theme_path" id="theme_path" class="required" value="theme" size="20"/>
	</p>
	<p class="submit" style="margin-top:5px;float:left;margin-bottom:5px;width:100%">
  	<input type="hidden" name="install" value="install" />
		<button type="submit" class="positive" style="float:right;"><span class="icons check"></span>Start install and go to login</button>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>?step=2" style="float:left;"> &laquo; Back</a>
	</p>
   <div class="clear" style="height:55px;"></div>
<?php } ?>
</form>
<?php } ?>
</div>
</div>
</body>
</html>