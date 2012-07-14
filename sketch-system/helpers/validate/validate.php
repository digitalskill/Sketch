<?php
// Created by Kevin Dibble
class VALIDATE{														// CLASS VALIDATE FORM
	public  $errorMsg;												// SETUP ERROR MESSAGE
	public 	$canSend;												// VARIBLE TO CHECK IF THE EMAIL CAN BE SENT
	public 	$allFeilds 		= array();								// ALL THE FIELDS EXPECTED TO BE SENT
	public 	$cleanedFields 	= array();								// THE CLEANED DATA EXPECTED (ESCAPED)
	public 	$missingFields	= array();
	public	$expectedFields = array();
	function __construct($form=""){
		$this->canSend 	= false;
		$this->errorMsg = "";
	}
	function __set($item,$value){
		global $_REQUEST;
		if(!isset($value['value']) || isset($_REQUEST[$item])){
			$value['value'] = @$_REQUEST[$item];
		}
		$this->expectedFields[$item] = $value;
	}
	function __get($item){
		if($item=="isFormValid" || $item=="valid"){
			return $this->canSend;
		}
		if($item=="getError"){
			return $this->errorMsg;
		}
		return $this->cleanedFields[$item];
	}
	public static function loadTemplate($name){
		$r = getData("template","*","template_name=".sketch("db")->quote($name));
		if($r->rowCount() > 0){
			$r->advance();
			$form = str_replace(array("phpstart","endphp"),array("<?php","?>"),$r->template_content);	
			$form = str_replace(array("<?php","?>","select","textarea","radio","checkbox","<?"),"",$form);
			$row = explode('<',$form);
			$final = array();
			$count = 0;
			foreach($row as $key => $value){
				if(strpos(strtolower($value),"name")){
					$value = str_replace(array("<","/>",">",'class="','value="','name="','title="'),array("","","","#c#","#v#","#n#","#t#"),$value);
					$items = explode('"',$value);
					$final[$count] = array("name"=>"","class"=>"","value"=>"","title"=>"");
					foreach($items as $key => $value){
						if(strpos($value,"#n#")!==false){
							$final[$count]['name'] = stripslashes(trim(str_replace("#n#","",$value)));	
						}
						if(strpos($value,"#c#")!==false){
							$final[$count]['class'] = stripslashes(trim(str_replace("#c#","",$value)));	
						}
						if(strpos($value,"#v#")!==false){
							$final[$count]['value'] = stripslashes(trim(str_replace("#v#","",$value)));	
						}
						if(strpos($value,"#t#")!==false){
							$final[$count]['title'] = stripslashes(trim(str_replace("#t#","",$value)));	
						}
						
					}
				$count++;
				}
			}
			$validate = new VALIDATE($form);
			foreach($final as $key => $value){
				if(isset($value['name']) && $value['name'] != ""){
					$validate->$value['name'] = $value;
				}
			}
			return $validate;
		}else{
			return false;	
		}
	}
	public static function loadForm($form){
		$form = loadForm($form);
		$form = str_replace(array("<?php","?>","select","textarea","radio","checkbox","<?"),"",$form);
		$row = explode('<',$form);
		$final = array();
		$count = 0;
		foreach($row as $key => $value){
			if(strpos(strtolower($value),"name")){
				$value = str_replace(array("<","/>",">",'class="','value="','name="','title="'),array("","","","#c#","#v#","#n#","#t#"),$value);
				$items = explode('"',$value);
				$final[$count] = array("name"=>"","class"=>"","value"=>"","title"=>"");
				foreach($items as $key => $value){
					if(strpos($value,"#n#")!==false){
						$final[$count]['name'] = stripslashes(trim(str_replace("#n#","",$value)));	
					}
					if(strpos($value,"#c#")!==false){
						$final[$count]['class'] = stripslashes(trim(str_replace("#c#","",$value)));	
					}
					if(strpos($value,"#v#")!==false){
						$final[$count]['value'] = stripslashes(trim(str_replace("#v#","",$value)));	
					}
					if(strpos($value,"#t#")!==false){
						$final[$count]['title'] = stripslashes(trim(str_replace("#t#","",$value)));	
					}
					
				}
			$count++;
			}
		}
		$validate = new VALIDATE($form);
		foreach($final as $key => $value){
			if(isset($value['name']) && $value['name'] != ""){
				$validate->$value['name'] = $value;
			}
		}
		return $validate;
	}
	function processForm($filledInFields){										// PASS AN ARRAY OF REQUESTED FIELDS
		$this->canSend = true;
		foreach($this->expectedFields as $key => $value){
			$error = "";
			@$this->cleanedFields[$key] = @$this->makeDataSafe($filledInFields[$key],$value['class']);
			if(strpos($value['class'],"required")!==false){
				if($this->cleanedFields[$key]==="" || !isset($this->cleanedFields[$key])){
					$this->canSend = false;
					$this->missingFields[] = $key;								// ADD TO THE ARRAY OF MISSING FIELDS
					if(isset($value['title']) && $value['title'] != ""){
						$error = $value['title'] ."<br />";						// ADD A CUSTOM ERROR MESSAGE
					}else{
						$error = "Please provide valid information for: ". $key ."<br />";
					}
				}else{
					if($error=="" && strpos($value['class'],"minValue:")){
						list($minval,) = explode("minValue:",$value['class']);
						list($minval,) = explode(" ",$minval);
						if(strlen($this->cleanedFields[$key]) < floatval($minval)){
							$this->canSend = false;
							$this->missingFields[] = $key;						// ADD TO THE ARRAY OF MISSING FIELDS
							$error = "The Feild: ".  $key ." must have at least ". floatval($minval) ." characters<br />";
						}
					}
				}
			}
			$this->errorMsg .= $error;
		}
		return $this->canSend;
	}
	function getError(){
		return $this->errorMsg;
	}
	function isFormValid(){
		return $this->canSend;
	}
	function getCleanedValues(){
		return $this->cleanedFields;
	}
	function getMissingFields(){									// RETURNS AN ARRAY OF MISSING FIELDS
		return $this->missingFields;
	}
	function convertDateIn($value){									// FUNCTION TO CONVERT DATES INPUT
		list($day,$month,$year) = explode('-',$value);
		return date("Y-m-d",mktime(0,0,0,$month,$day,$year));
	}
	function makeDataSafe($input_data,$var_type='text'){
		if (PHP_VERSION < 6) {
   			$input_data = get_magic_quotes_gpc() ? stripslashes($input_data) : $input_data;
  		}
		$input_data =  trim(sketch("db")->quote($input_data),"'");
		$vars = explode(" ",$var_type);
		foreach($vars as $key => $value){
			if(trim($value)=="credit" || trim($value)=="file" || trim($value)=="text"){
				$input_data = ($input_data != "")?  ($input_data)		: "";
			}else{
				if(trim($value)=="integer"){
					$input_data = ($input_data != "")? intval($input_data) 	: "";
				}else{
					if(trim($value)=="decimal"){
						$input_data = ($input_data != "")? doubleval($input_data) 	: "";
					}else{
						if(trim($value)=="date"){
							$input_data = ($input_data!= "") ?  $this->convertDateIn($input_data) : "";
						}else{
							if(trim($value=="email")){
								list($userName, $mailDomain) = split("@", $input_data);
								if($mailDomain != ""){
									if(function_exists("checkdnsrr")){
										if (@checkdnsrr($mailDomain, "MX")) {
																										// this is a valid email domain!
										}else{
											$input_data = "";											// Empty the email address
										}
									}else{
										$input_data = (strpos(".",$mailDomain)!==false)? $input_data : "";
									}
								}else{
									$input_data = "";												// Empty the email address
								} 
							}else{
								if(trim($value=="html_ent")){
									$input_data = stripslashes($input_data); 								//stops \\\\\ appearing
	      							$input_data = htmlentities($input_data);								//removes html markup
								}else{
									if(trim($value=="html")){		// !! NOT FOR SQL USE
										$input_data = stripslashes($input_data); 								//stops \\\\\ appearing
									}
								}
							}
						}
					}
				}
			}
		}
	return $input_data;	
	}
}