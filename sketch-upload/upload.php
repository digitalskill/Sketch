<?php
// Code for Session Cookie workaround
	if (isset($_POST["PHPSESSID"]) && trim($_POST["PHPSESSID"]) != "") {
		session_id(trim($_POST["PHPSESSID"]));
	} else if (isset($_GET["PHPSESSID"]) && trim($_GET["PHPSESSID"])!= "") {
		session_id(trim($_GET["PHPSESSID"]));
	}
	$slash = "/";
	if(strpos(dirname(__FILE__),"\\")!==false){
		$slash = "\\";
	}
	require_once("config.php");
	session_start();											// Start the session
	
	$handle = fopen('log.txt','w');
	fwrite($handle,"Requests: \r\n");
	foreach($_REQUEST as $key => $value){
		fwrite($handle,$key."=".$value."\r\n");
	}
	fwrite($handle,"FILES: \r\n");
	foreach($_FILES as $key => $value){
		fwrite($handle,$key."=".$value."\r\n");
	}
	fclose($handle);
	if(isset($_SESSION['imagetoken']) && $_SESSION['imagetoken']==md5(date("Y-m-d"))){
		$timeStamp 		= ""; //date("YmdHis");					// Create a timestamp for the file
		$POST_MAX_SIZE  = ini_get('post_max_size');				// GET THE FILE SIZE UPLOAD LIMIT from the server
		$unit = strtoupper(substr($POST_MAX_SIZE, -1));
		$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));
	
		if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
			HandleError("Too Big");
			exit(0);
		}
		$save_path 		= (isset($_REQUEST['folder']) && $_REQUEST['folder'] != "")? "..".$slash.$_REQUEST['folder'].$slash  :  "..".$slash."sketch-images".$slash;	// The path to save the file
		$save_path		= str_replace(array("/","//",$slash.$slash,$slash),$slash,$save_path);
		$upload_name 	= "Filedata";								// The name of the $_FILES field
		if(isset($_REQUEST['theFile']) && trim($_REQUEST['theFile'])!= ""){
			$upload_name = trim($_REQUEST['theFile']);
		}
		
		if(isset($_SESSION['folderpath'])){
			$save_path = $_SESSION['folderpath'];
			$_SESSION['path'] = $save_path;
			unset($_SESSION['folderpath']);
		}else{
			$_SESSION['path'] = str_replace("..".$slash,"",$save_path);	
		}
		$max_file_size_in_bytes = 20971520;							// 20 mb in bytes
		$extension_whitelist 	= $fileTypes; 						// Allowed file extensions
		$valid_chars_regex 		= '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';	// Characters allowed in the file name (in a Regular Expression format)
		$MAX_FILENAME_LENGTH 	= 200;								// Set the maximum length of the file name
		$file_name 				= "";
		$file_extension 		= "";
	
		if (!isset($_FILES[$upload_name])) {						// Check for a file
			HandleError("No File. Upload Name=". $upload_name);									// No File found - Exit
			exit(0);
		} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {	// check for File Error
			//HandleError("No Path");
			HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);								// Error found = Exit
			exit(0);
		} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
			HandleError("No Path");									// not a valid uploaded file
			exit(0);
		} else if (!isset($_FILES[$upload_name]['name'])) {											// Check for file name
			HandleError("No Path");														// no Name Found - Exit
			exit(0);
		}
	
		$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);									// Get the File size
		if (!$file_size || $file_size > $max_file_size_in_bytes) {
			HandleError("Too Big");																	// File too big - Exit
			exit(0);
		}
		
		if ($file_size <= 0) {																		// No File size
			HandleError("Too Small");																// Possible error - Exit
			exit(0);
		}
	
		$file_name = str_replace(" ","",preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']))); // Remove invalid Characters from the name
		if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {					// check name length
			HandleError("Invalid");																	// Name too long - Exit
			exit(0);
		}
	
		if(isset($_SESSION[$upload_name]) && isset($_SESSION['deleteold']) && $_SESSION['deleteold']==true){
			@unlink($save_path.$_SESSION[$upload_name]);											// Remove the old file uploaded
		}
		$path_info = pathinfo($_FILES[$upload_name]['name']);										// Validate file extension
		$file_extension = $path_info["extension"];
		$is_valid_extension = false;																// Assume an invalid extension
		foreach ($extension_whitelist as $key => $extension) {
			if (strcasecmp($file_extension, $extension) == 0) {										// Compare extension to white list
				$_SESSION['mediatype'] = $extension;												// Save extension values for the Database
				$is_valid_extension = true;															// Valid Extension found
				break;																				// Continue
			}
		}
		if (!$is_valid_extension) {																	// Is extension valid
			HandleError("Invalid");																	// Invalid Extension - exit
			exit(0);
		}
		
		if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$timeStamp.$file_name)) {
			HandleError("Not Saved");
			exit(0);
		}
		$_SESSION[$upload_name]	= $timeStamp.$file_name;											// Save the file uploaded in the session data
		chmod($save_path.$timeStamp.$file_name,0777) or die("Cannot change permissions");
		HandleError("Success");
		exit(0);
	}else{
		HandleError("Invalid");																	// Name too long - Exit
		exit(0);
	}
function HandleError($message) {																// Flash 8 - just return a 500 error (dumb feedback) // Flash 9 do more
	echo $message;
}