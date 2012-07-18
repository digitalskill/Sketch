<?php
/*
EMAIL FUNCTIONS
*/
//include_once("smtp.php");
include_once("MAIL.php");
include_once("mailer.inc.php");
function createEmail($sent){
	$mail = new Mailer($sent);
	$mail->htmlfile = file_get_contents("html.html");
	return $mail;
}
function email($from,$to,$subject,$data,$file="html.html",$fileAttach="",$fileAttachName=""){
	$from = filter_var($from, FILTER_VALIDATE_EMAIL);
	$to = filter_var($to, FILTER_VALIDATE_EMAIL);
	if($from !==false && $to !== false){
		return Mailer::mail($from,$to,$subject,$data,$file,$fileAttach,$fileAttachName);
	}
	return false;
}
function captcha(){?>
    <img src="<?php echo urlPath("sketch-system/helpers/email/random_image.php"); ?>" />
<?php }