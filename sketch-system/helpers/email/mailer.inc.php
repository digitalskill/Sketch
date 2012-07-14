<?php
/*
CREATED BY KEVIN DIBBLE
EXPECTS AT LEAST AN EMAIL FIELD TO BE PRESENT IN THE FORM
REQUIRES THE SMTP PACKAGE

USE:
	$sendEmail = new Mailer($_SESSION['sent']);												// SESSION DATA TO CHECK IF PERSON HAS SENT A MESSAGE ALREADY
	$sendEmail->setRequiredFields('email,name,comments');									// SET REQUIRED FIELDS NEEDED BY FORM
	$sendEmail->checkEmailForm($_REQUEST);													// PASS THE FORM PARAMETERS
	$sendEmail->checkImageNumber($_SESSION['image_random_value'],$_REQUEST['security']); 	//CHECK IF THE NUMBER MATCHES THE SECURITY KEY
	$sendEmail->setFormFields('name,email,phone,subscription,comments',$_REQUEST);			// SEND ONLY THESE PARAMETERS
	$error = $sendEmail->createEmailMessage();												// Sends Email Message and Returns Result :
																							// "success"   	-- Email was sent with no issues
																							// "failed"	   	-- Email Could not be sent
																							// "incomplete"	-- Required Fields Were missing
																							// "sent"		-- Person has aleady sent an email - not sent
	$sendEmail->getErrorMessage();															// Returns a HTML string of the error
	$sendEmail->getMissingFields();															// Returns an Array of missing fields
																							// Use in_array('name',$sendEmail->getMissingFields()) to get missing field data
*/
class Mailer{										// CLASS MAILER
	var $errorMsg 		= "";						// SETUP ERROR MESSAGE
	var $canSend 		= true;						// VARIBLE TO CHECK IF THE EMAIL CAN BE SENT
	var $requiredFields	= array();					// THE EXPECTED FIELDS THAT MUST BE PRESENT
	var $allFeilds 		= array();					// ALL THE FIELDS EXPECTED TO BE SENT
	var $cleanedFields 	= array();					// THE CLEANED DATA EXPECTED (ESCAPED)
	var $hasSentEmail 	= false;					// CHECK IF THE USER HAS SENT AN EMAIL BEFORE
	var $emailMessage 	= "";						// THE MAIL MESSAGE (HTML FORMAT)
	var $htmlfile		= "";
	var $headers 		= "";						// THE MIME HEADERS TO SEND
	var $to 		= "kevin@sketchideas.com";			// THE DEFAULT ADDRESS TO SEND TO
	var $subject 		= "Website Enquiry";				// THE DEFAULT SUBJECT
	var $from 		= "kevin@sketchideas.com";			// THE FROM EMAIL FIELD
	var $securityFieldName	= 'num';					// DEFAULT SECURITY FIELD NAME
	var $missingFields	= array();
	var $replyTo 		= "";
	var $fileAttach		= false;
	var $fileAttachName	= "";
	var $htmlOnly		= "";
	var $bcc		= "";
	private $htmlfields;
	function Mailer($sent){							// INITILISATION CALL
		if($sent=="true"){
			$this->hasSentEmail = true;				// PASS $_SESSION['sent'] TO CHECK
		}								// OR JUST CALL THE FUNCTION WITH "FALSE" TO ALWAYS SEND
	}
	public static function mail($from,$to,$subject,$data,$file="html.html",$fileAttach="",$fileAttachName=""){
		$em = new Mailer('false');
		$em->setFrom($from);
		$em->setTo($to);
		$em->setSubject($subject);
		if(!is_file($file)){
		    $file = sketch("sketchPath")."helpers".sketch("slash")."email".sketch("slash")."html.html";
		}
		if(is_file($fileAttach)){
			$em->fileAttach 		= $fileAttach;
			$em->fileAttachName 	= $fileAttachName;
		}
		$em->htmlfile = file_get_contents($file);
		$em->setFormFields(implode(",",array_keys($data)),$data);
		return $em->createEmailMessage();
	}
	function setSubject($newSubject){					// SETUP THE EMAIL SUBJECT
		$this->subject = $newSubject;
	}
	function setHTMLFields($fields){
		$this->htmlfields = explode(',',$fields);
	}
	function setFrom($from){
		$this->from = $from;
	}
	function setTo($newTo){										// SETUP THE EMAIL RECEIPIENT (#address1,address2,address3")
		$this->to = explode(',',$newTo);						// CREATE AN ARRAY FROM THE LIST
	}
	function setBcc($newBcc){
		$this->bcc = explode(",",$newBcc);
	}
	function hasSentEmail(){
		return $this->hasSentEmail;
	}
	function getErrorMessage(){									// RETURN THE ERROR HTML STRING
		return $this->errorMsg;
	}
	function setRequiredFields($input){							// PASS A STRING OF REQUIRED FIELDS SEPERATED BY A COMMA
		$this->requiredFields = explode(',',$input);
	}
	function setFormFields($input,$data){						// PASS A STRING OF FIELDS TO SEND SEPERATED BY A COMMA
		$this->allFeilds = explode(',',$input);
		foreach($this->allFeilds as $key => $value){
			if(is_array($this->htmlfields) && in_array($value,$this->htmlfields)){
				$this->cleanedFields[$value] = $data[$value];
			}else{
				$this->cleanedFields[$value] = $this->make_data_safe($data[$value],"html_ent");
			}
		}
	}
	function getMissingFields(){								// RETURNS AN ARRAY OF MISSING FIELDS
		return $this->missingFields;
	}
	function checkEmailForm($userInput){						// CHECKS THAT ALL REQUIRED DATA IS PRESENT
		foreach($userInput as $key => $value){
			if (in_array($key,$this->requiredFields)){
				if ($key=='email') {
					list($userName, $mailDomain) = split("@", $value);
					if(strpos($value,'.')===false || strrpos($value,'.') < strpos($value,'@') || strlen($value) < 5){
						if($this->errorMsg ==""){
							$this->errorMsg ="The following input is needed: <br />";
						}
						$this->errorMsg .= " The email address is not valid. Please check your email address<br />";
						$this->canSend = false;
						$this->missingFields[] = $key;
					}
				}else if (trim($value) == ""){
					if($this->errorMsg ==""){
						$this->errorMsg ="The following input is needed: <br />";
					}
					$this->errorMsg .= " " . $key . " details are needed.<br />";
					$this->canSend = false;
					$this->missingFields[] = $key;
				}
			}
		}
	}
	function setSecurityField($securityField){
		$this->securityFieldName = $securityField;
	}
	function checkImageNumber($numberVal,$inputVal){			// CHECKS IF A SECURITY NUMBER WAS USED
		if($numberVal != md5($inputVal)){
			if($this->errorMsg ==""){
    			$this->errorMsg ="The following input is needed: <br />";
    		}
			$this->errorMsg .= "The number provided must match the number in the image";
			$this->canSend = false;
			$this->missingFields[] = $this->securityFieldName;
		}
	}
	function createHTMLOnlyEmail(){
		if($this->hasSentEmail == false){
			if($this->canSend==true){
				$this->emailMessage = str_replace(array("#subject#","#date#","#message#","#sketch#"),array(ucwords($this->subject),date("D d F, Y"),$this->htmlOnly,$_SERVER['HTTP_HOST']),$this->htmlfile);
				$mail 		= new SMTP;																			// CREATE NEW SMTP CLASS
				$mail->From($this->from);
				if($this->fileAttach != false){
					$attached = $mail->Attach(file_get_contents($this->fileAttach),FUNC::mime_type($this->fileAttach),$this->fileAttachName);
					if(!$attached){
						return "attachment";	
					}
				}
				$reply = ($this->replyTo != "")? $this->replyTo : $this->cleanedFields['email'];
				$mail->AddHeader('Reply-To',$reply);
				if(is_array($this->to)){
					foreach($this->to as $key => $value){
						$mail->AddTo($value);
					}
				}else{
					$mail->AddTo($this->to);
				}
				$mail->Html($this->emailMessage);
				$mail->Subject($this->subject);
				if($mail->Send('client')){
					return "success";
				}else{
					return "failed";
				}
			}else{
				return "incomplete";
			}
		}else{
			return "sent";
		}
		
	}
	function createEmailMessage(){								// CREATE HTML EMAIL MESSAGE
		if($this->hasSentEmail == false){
			if($this->canSend==true){
				$this->emailMessage = str_replace(array("#subject#","#date#","#sketch#"),array(ucwords($this->subject),date("D d F, Y"),$_SERVER['HTTP_HOST']),$this->htmlfile);
				$messageContent 	= '<table width="100%" border="0" cellpadding="2" cellspacing="1" class="messageTable">';
				foreach($this->cleanedFields as $key => $value){
					if(is_array($this->htmlfields) && in_array($key,$this->htmlfields)){
						$messageContent .= '<tr><td colspan="2" valign="top" bgcolor="#FFFFFF">'.$value.'</td></tr>';
					}else{
						$messageContent .= '<tr><td width="100" align="right" valign="top" bgcolor="#FFFFFF" class="leftCol">'.ucwords($key).'</td><td bgcolor="#FFFFFF">'.$value.'</td></tr>';
					}
				} 			
    			$messageContent 	.= '</table>';
				$this->emailMessage = str_replace("#message#",$messageContent,$this->emailMessage);
				$mail 		= new MAIL;																			// CREATE NEW SMTP CLASS
				$mail->From($this->from);
				if($this->fileAttach != false){
					$attached = $mail->Attach(file_get_contents($this->fileAttach),FUNC::mime_type($this->fileAttach),$this->fileAttachName);
					if(!$attached){
						return "attachment";	
					}
				}
				$reply = ($this->replyTo != "")? $this->replyTo : $this->cleanedFields['email'];
				$mail->AddHeader('Reply-To',$reply);
				if(is_array($this->to)){
					foreach($this->to as $key => $value){
						$mail->AddTo($value);
					}
				}else{
					$mail->AddTo($this->to);
				}
				if(is_array($this->bcc)){
					foreach($this->bcc as $key => $value){
						$mail->AddBcc($value);	
					}
				}else{
					if(trim($this->bcc) != ""){
						$mail->AddBcc($value);
					}
				}
				
				$mail->Html($this->emailMessage);
				$mail->subject($this->subject);
				if($mail->Send('client')){
					return "success";
				}else{
					return "failed";
				}
			}else{
				return "incomplete";
			}
		}else{
			return "sent";
		}
	}
	function make_data_safe($input_data,$var_type='text'){							// ESCAPE USER INPUT
		$input_data = (get_magic_quotes_gpc()) ? $input_data : addslashes($input_data);
  		switch ($var_type) {
			case "email":
				$input_data = ($input_data != "") ?  $input_data  : $this->to;
				break;
    		case "text":
      			$input_data = ($input_data != "") ? "'" . $input_data . "'" : "NULL";
      			break;    
    		case "int":
      			$input_data = ($input_data != "") ? intval($input_data) : "NULL";
      			break;
    		case "float":
				$input_data = ($input_data != "") ? "'" . floatval($input_data) . "'" : "NULL";
      			break;
    		case "date":
      			//SQL data are year Month Day
      			$input_data = ($input_data!= "") ? "'" . $input_data . "'" : "NULL";
      			break;
    		case "html_ent":								// Not for SQL use
    			$input_data = stripslashes($input_data); 	//stops \\\\\ appearing
      			break;
    		case "html":									// Not for SQL use
    			$input_data = stripslashes($input_data); 	//stops \\\\\ appearing
      			break;
  		}
		return $input_data;
	}
}?>