<?php
function CallShortcutExpressCheckout($extras){
    $nvpstr="&PAYMENTREQUEST_0_AMT=". $extras['paymentAmount'];
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $extras['paymentType'];
    $nvpstr = $nvpstr . "&RETURNURL=" . $extras['returnURL'];
    $nvpstr = $nvpstr . "&CANCELURL=" . $extras['cancelURL'];
    $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $extras['currencyCodeType'];
    $resArray=hash_call("SetExpressCheckout", $nvpstr,$extras);
    $ack = strtoupper($resArray["ACK"]);
    if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING"){
	$token = urldecode($resArray["TOKEN"]);
	$_SESSION['TOKEN']=$token;
    }
    return $resArray;
}

function CallMarkExpressCheckout( $paymentAmount, $currencyCodeType, $paymentType, $returnURL,
									  $cancelURL, $shipToName, $shipToStreet, $shipToCity, $shipToState,
									  $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum,$extras
									){
		$nvpstr="&PAYMENTREQUEST_0_AMT=". $paymentAmount;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
		$nvpstr = $nvpstr . "&RETURNURL=" . $returnURL;
		$nvpstr = $nvpstr . "&CANCELURL=" . $cancelURL;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
		$nvpstr = $nvpstr . "&ADDROVERRIDE=1";
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTONAME=" . $shipToName;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $shipToStreet;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $shipToStreet2;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCITY=" . $shipToCity;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $shipToState;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $shipToCountryCode;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOZIP=" . $shipToZip;
		$nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $phoneNum;
	    $resArray=hash_call("SetExpressCheckout", $nvpstr,$extras);
		$ack = strtoupper($resArray["ACK"]);
		if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING")
		{
			$token = urldecode($resArray["TOKEN"]);
			$_SESSION['TOKEN']=$token;
		}

	    return $resArray;
	}

function GetShippingDetails( $token,$extras ){
    $nvpstr="&TOKEN=" . $token;
    $resArray=hash_call("GetExpressCheckoutDetails",$nvpstr,$extras);
    $ack = strtoupper($resArray["ACK"]);
    if($ack == "SUCCESS" || $ack=="SUCCESSWITHWARNING"){
	$_SESSION['payer_id'] =	@$resArray['PAYERID'];
    }
    return $resArray;
}

function ConfirmPayment($extras ){
	$token 			= urlencode($extras['TOKEN']);
	$paymentType 		= urlencode($extras['paymentType']);
	$currencyCodeType 	= urlencode($extras['currencyCodeType']);
	$payerID 		= urlencode($extras['payer_id']);
	$serverName 		= urlencode($_SERVER['SERVER_NAME']);
	$nvpstr  = '&TOKEN=' . $token . '&PAYERID=' . $payerID . '&PAYMENTREQUEST_0_PAYMENTACTION=' . $paymentType . '&PAYMENTREQUEST_0_AMT=' . $extras['paymentAmount'];
	$nvpstr .= '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType . '&IPADDRESS=' . $serverName;
	$resArray=hash_call("DoExpressCheckoutPayment",$nvpstr,$extras);
	$ack = strtoupper($resArray["ACK"]);

	return $resArray;
}

function DirectPayment( $paymentType, $paymentAmount, $creditCardType, $creditCardNumber,
						$expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip,
						$countryCode, $currencyCode )
{
	//Construct the parameter string that describes DoDirectPayment
	$nvpstr = "&AMT=" . $paymentAmount;
	$nvpstr = $nvpstr . "&CURRENCYCODE=" . $currencyCode;
	$nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymentType;
	$nvpstr = $nvpstr . "&CREDITCARDTYPE=" . $creditCardType;
	$nvpstr = $nvpstr . "&ACCT=" . $creditCardNumber;
	$nvpstr = $nvpstr . "&EXPDATE=" . $expDate;
	$nvpstr = $nvpstr . "&CVV2=" . $cvv2;
	$nvpstr = $nvpstr . "&FIRSTNAME=" . $firstName;
	$nvpstr = $nvpstr . "&LASTNAME=" . $lastName;
	$nvpstr = $nvpstr . "&STREET=" . $street;
	$nvpstr = $nvpstr . "&CITY=" . $city;
	$nvpstr = $nvpstr . "&STATE=" . $state;
	$nvpstr = $nvpstr . "&COUNTRYCODE=" . $countryCode;
	$nvpstr = $nvpstr . "&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];

	$resArray=hash_call("DoDirectPayment", $nvpstr);

	return $resArray;
}


	/**
	  '-------------------------------------------------------------------------------------------------------------------------------------------
	  * hash_call: Function to perform the API call to PayPal using API signature
	  * @methodName is name of API  method.
	  * @nvpStr is nvp string.
	  * returns an associtive array containing the response from the server.
	  '-------------------------------------------------------------------------------------------------------------------------------------------
	*/
	function hash_call($methodName,$nvpStr,$extras)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$extras['API_Endpoint']);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);

		//turning off the server and peer verification(TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
		curl_setopt($ch, CURLOPT_POST, 1);

	    //if USE_PROXY constant set to TRUE in Constants.php, then only proxy will be enabled.
	   //Set proxy name to PROXY_HOST and port number to PROXY_PORT in constants.php
		if($extras['USE_PROXY'])
			curl_setopt ($ch, CURLOPT_PROXY, $extras['PROXY_HOST']. ":" . $extras['PROXY_PORT']);

		//NVPRequest for submitting to server
		$nvpreq="METHOD=" . urlencode($methodName) . "&VERSION=" . 
				    urlencode($extras['version']) . "&PWD=" .
				    urlencode($extras['API_Password']) . "&USER=" .
				    urlencode($extras['API_UserName']) . "&SIGNATURE=" .
				    urlencode($extras['API_Signature']) . $nvpStr . "&BUTTONSOURCE=" .
				    urlencode($extras['sBNCode']);

		//setting the nvpreq as POST FIELD to curl
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

		//getting response from server
		$response = curl_exec($ch);

		//convrting NVPResponse to an Associative Array
		$nvpResArray=deformatNVP($response);
		$nvpReqArray=deformatNVP($nvpreq);
		$_SESSION['nvpReqArray']=$nvpReqArray;

		if (curl_errno($ch))
		{
			// moving to display page to display curl errors
			  $_SESSION['curl_error_no']=curl_errno($ch) ;
			  $_SESSION['curl_error_msg']=curl_error($ch);

			  //Execute the Error handling module to display errors.
		}
		else
		{
			 //closing the curl
		  	curl_close($ch);
		}

		return $nvpResArray;
	}

	/*'----------------------------------------------------------------------------------
	 Purpose: Redirects to PayPal.com site.
	 Inputs:  NVP string.
	 Returns:
	----------------------------------------------------------------------------------
	*/
	function RedirectToPayPal ( $token,$extras )
	{
		$payPalURL = $extras['PAYPAL_URL'] . $token;
		header("Location: ".$payPalURL);
		exit();
	}


	/*'----------------------------------------------------------------------------------
	 * This function will take NVPString and convert it to an Associative Array and it will decode the response.
	  * It is usefull to search for a particular key and displaying arrays.
	  * @nvpstr is NVPString.
	  * @nvpArray is Associative Array.
	   ----------------------------------------------------------------------------------
	  */
	function deformatNVP($nvpstr)
	{
		$intial=0;
	 	$nvpArray = array();

		while(strlen($nvpstr))
		{
			//postion of Key
			$keypos= strpos($nvpstr,'=');
			//position of value
			$valuepos = strpos($nvpstr,'&') ? strpos($nvpstr,'&'): strlen($nvpstr);

			/*getting the Key and Value values and storing in a Associative Array*/
			$keyval=substr($nvpstr,$intial,$keypos);
			$valval=substr($nvpstr,$keypos+1,$valuepos-$keypos-1);
			//decoding the respose
			$nvpArray[urldecode($keyval)] =urldecode( $valval);
			$nvpstr=substr($nvpstr,$valuepos+1,strlen($nvpstr));
	     }
		return $nvpArray;
	}
function getCountryCode($country,$code=""){
    $countries = array("AF"=>"AFGHANISTAN",
	"AX"=>"ALAND ISLANDS",
	"AL"=>"ALBANIA",
	"DZ"=>"ALGERIA",
	"AS"=>"AMERICAN SAMOA",
	"AD"=>"ANDORRA",
	"AO"=>"ANGOLA",
	"AI"=>"ANGUILLA",
	"AQ"=>"ANTARCTICA",
	"AG"=>"ANTIGUA AND BARBUDA",
	"AR"=>"ARGENTINA",
	"AM"=>"ARMENIA",
	"AW"=>"ARUBA",
	"AU"=>"AUSTRALIA",
	"AT"=>"AUSTRIA",
	"AZ"=>"AZERBAIJAN",
	"BS"=>"BAHAMAS",
	"BH"=>"BAHRAIN",
	"BD"=>"BANGLADESH",
	"BB"=>"BARBADOS",
	"BY"=>"BELARUS",
	"BE"=>"BELGIUM",
	"BZ"=>"BELIZE",
	"BJ"=>"BENIN",
	"BM"=>"BERMUDA",
	"BT"=>"BHUTAN",
	"BO"=>"BOLIVIA",
	"BA"=>"BOSNIA AND HERZEGOVINA",
	"BW"=>"BOTSWANA",
	"BV"=>"BOUVET ISLAND",
	"BR"=>"BRAZIL",
	"IO"=>"BRITISH INDIAN OCEAN TERRITORY",
	"BN"=>"BRUNEI DARUSSALAM",
	"BG"=>"BULGARIA",
	"BF"=>"BURKINA FASO",
	"BI"=>"BURUNDI",
	"KH"=>"CAMBODIA",
	"CM"=>"CAMEROON",
	"CA"=>"CANADA",
	"CV"=>"CAPE VERDE",
	"CI"=>"CâTE D'IVOIRE",
	"KY"=>"CAYMAN ISLANDS",
	"CF"=>"CENTRAL AFRICAN REPUBLIC",
	"TD"=>"CHAD",
	"CL"=>"CHILE",
	"CN"=>"CHINA",
	"CX"=>"CHRISTMAS ISLAND",
	"CC"=>"COCOS (KEELING) ISLANDS",
	"CO"=>"COLOMBIA",
	"KM"=>"COMOROS",
	"CG"=>"CONGO",
	"CD"=>"CONGO, THE DEMOCRATIC REPUBLIC of THE",
	"CK"=>"COOK ISLANDS",
	"CR"=>"COSTA RICA",
	"HR"=>"CROATIA",
	"CU"=>"CUBA",
	"CY"=>"CYPRUS",
	"CZ"=>"CZECH REPUBLIC",
	"DK"=>"DENMARK",
	"DJ"=>"DJIBOUTI",
	"DM"=>"DOMINICA",
	"DO"=>"DOMINICAN REPUBLIC",
	"EC"=>"ECUADOR",
	"EG"=>"EGYPT",
	"SV"=>"EL SALVADOR",
	"GQ"=>"EQUATORIAL GUINEA",
	"ER"=>"ERITREA",
	"EE"=>"ESTONIA",
	"ET"=>"ETHIOPIA",
	"FK"=>"FALKLAND ISLANDS (MALVINAS)",
	"FO"=>"FAROE ISLANDS",
	"FJ"=>"FIJI",
	"FI"=>"FINLAND",
	"FR"=>"FRANCE",
	"GF"=>"FRENCH GUIANA",
	"PF"=>"FRENCH POLYNESIA",
	"TF"=>"FRENCH SOUTHERN TERRITORIES",
	"GA"=>"GABON",
	"GM"=>"GAMBIA",
	"GE"=>"GEORGIA",
	"DE"=>"GERMANY",
	"GH"=>"GHANA",
	"GI"=>"GIBRALTAR",
	"GR"=>"GREECE",
	"GL"=>"GREENLAND",
	"GD"=>"GRENADA",
	"GP"=>"GUADELOUPE",
	"GU"=>"GUAM",
	"GT"=>"GUATEMALA",
	"GN"=>"GUINEA",
	"GW"=>"GUINEA-BISSAU",
	"GY"=>"GUYANA",
	"HT"=>"HAITI",
	"HM"=>"HEARD ISLAND AND MCDONALD ISLANDS",
	"VA"=>"HOLY SEE (VATICAN CITY STATE)",
	"HN"=>"HONDURAS",
	"HK"=>"HONG KONG",
	"HU"=>"HUNGARY",
	"IS"=>"ICELAND",
	"IN"=>"INDIA",
	"ID"=>"INDONESIA",
	"IR"=>"IRAN ISLAMIC REPUBLIC of",
	"IQ"=>"IRAQ",
	"IE"=>"IRELAND",
	"IL"=>"ISRAEL",
	"IT"=>"ITALY",
	"JM"=>"JAMAICA",
	"JP"=>"JAPAN",
	"JO"=>"JORDAN",
	"KZ"=>"KAZAKHSTAN",
	"KE"=>"KENYA",
	"KI"=>"KIRIBATI",
	"KP"=>"KOREA DEMOCRATIC PEOPLE\'S REPUBLIC of",
	"KR"=>"KOREA REPUBLIC of",
	"KW"=>"KUWAIT",
	"KG"=>"KYRGYZSTAN",
	"LA"=>"LAO PEOPLE\'S DEMOCRATIC REPUBLIC",
	"LV"=>"LATVIA",
	"LB"=>"LEBANON",
	"LS"=>"LESOTHO",
	"LR"=>"LIBERIA",
	"LY"=>"LIBYAN ARAB JAMAHIRIYA",
	"LI"=>"LIECHTENSTEIN",
	"LT"=>"LITHUANIA",
	"LU"=>"LUXEMBOURG",
	"MO"=>"MACAO",
	"MK"=>"MACEDONIA, THE FORMER YUGOSLAV REPUBLIC of",
	"MG"=>"MADAGASCAR",
	"MW"=>"MALAWI",
	"MY"=>"MALAYSIA",
	"MV"=>"MALDIVES",
	"ML"=>"MALI",
	"MT"=>"MALTA",
	"MH"=>"MARSHALL ISLANDS",
	"MQ"=>"MARTINIQUE",
	"MR"=>"MAURITANIA",
	"MU"=>"MAURITIUS",
	"YT"=>"MAYOTTE",
	"MX"=>"MEXICO",
	"FM"=>"MICRONESIA, FEDERATED STATES of",
	"MD"=>"MOLDOVA, REPUBLIC of",
	"MC"=>"MONACO",
	"MN"=>"MONGOLIA",
	"MS"=>"MONTSERRAT",
	"MA"=>"MOROCCO",
	"MZ"=>"MOZAMBIQUE",
	"MM"=>"MYANMAR",
	"NA"=>"NAMIBIA",
	"NR"=>"NAURU",
	"NP"=>"NEPAL",
	"NL"=>"NETHERLANDS",
	"AN"=>"NETHERLANDS ANTILLES",
	"NC"=>"NEW CALEDONIA",
	"NZ"=>"NEW ZEALAND",
	"NI"=>"NICARAGUA",
	"NE"=>"NIGER",
	"NG"=>"NIGERIA",
	"NU"=>"NIUE",
	"NF"=>"NORFOLK ISLAND",
	"MP"=>"NORTHERN MARIANA ISLANDS",
	"NO"=>"NORWAY",
	"OM"=>"OMAN",
	"PK"=>"PAKISTAN",
	"PW"=>"PALAU",
	"PS"=>"PALESTINIAN TERRITORY, OCCUPIED",
	"PA"=>"PANAMA",
	"PG"=>"PAPUA NEW GUINEA",
	"PY"=>"PARAGUAY",
	"PE"=>"PERU",
	"PH"=>"PHILIPPINES",
	"PN"=>"PITCAIRN",
	"PL"=>"POLAND",
	"PT"=>"PORTUGAL",
	"PR"=>"PUERTO RICO",
	"QA"=>"QATAR",
	"RE"=>"REUNION",
	"RO"=>"ROMANIA",
	"RU"=>"RUSSIAN FEDERATION",
	"RW"=>"RWANDA",
	"SH"=>"SAINT HELENA",
	"KN"=>"SAINT KITTS AND NEVIS",
	"LC"=>"SAINT LUCIA",
	"PM"=>"SAINT PIERRE AND MIQUELON",
	"VC"=>"SAINT VINCENT AND THE GRENADINES",
	"WS"=>"SAMOA",
	"SM"=>"SAN MARINO",
	"ST"=>"SAO TOME AND PRINCIPE",
	"SA"=>"SAUDI ARABIA",
	"SN"=>"SENEGAL",
	"CS"=>"SERBIA AND MONTENEGRO",
	"SC"=>"SEYCHELLES",
	"SL"=>"SIERRA LEONE",
	"SG"=>"SINGAPORE",
	"SK"=>"SLOVAKIA",
	"SI"=>"SLOVENIA",
	"SB"=>"SOLOMON ISLANDS",
	"SO"=>"SOMALIA",
	"ZA"=>"SOUTH AFRICA",
	"GS"=>"SOUTH GEORGIA AND THE SOUTH SANDWICH ISLANDS",
	"ES"=>"SPAIN",
	"LK"=>"SRI LANKA",
	"SD"=>"SUDAN",
	"SR"=>"SURINAME",
	"SJ"=>"SVALBARD AND JAN MAYEN",
	"SZ"=>"SWAZILAND",
	"SE"=>"SWEDEN",
	"CH"=>"SWITZERLAND",
	"SY"=>"SYRIAN ARAB REPUBLIC",
	"TW"=>"TAIWAN PROVINCE of CHINA",
	"TJ"=>"TAJIKISTAN",
	"TZ"=>"TANZANIA UNITED REPUBLIC of",
	"TH"=>"THAILAND",
	"TL"=>"TIMOR-LESTE",
	"TG"=>"TOGO",
	"TK"=>"TOKELAU",
	"TO"=>"TONGA",
	"TT"=>"TRINIDAD AND TOBAGO",
	"TN"=>"TUNISIA",
	"TR"=>"TURKEY",
	"TM"=>"TURKMENISTAN",
	"TC"=>"TURKS AND CAICOS ISLANDS",
	"TV"=>"TUVALU",
	"UG"=>"UGANDA",
	"UA"=>"UKRAINE",
	"AE"=>"UNITED ARAB EMIRATES",
	"GB"=>"UNITED KINGDOM",
	"US"=>"UNITED STATES",
	"UM"=>"UNITED STATES MINOR OUTLYING ISLANDS",
	"UY"=>"URUGUAY",
	"UZ"=>"UZBEKISTAN",
	"VU"=>"VANUATU",
	"VE"=>"VENEZUELA",
	"VN"=>"VIETNAM",
	"VG"=>"VIRGIN ISLANDS BRITISH",
	"VI"=>"VIRGIN ISLANDS U.S.",
	"WF"=>"WALLIS AND FUTUNA",
	"EH"=>"WESTERN SAHARA",
	"YE"=>"YEMEN",
	"ZM"=>"ZAMBIA",
	"ZW"=>"ZIMBABWE");
    if($code==""){
	foreach($countries as $k => $v){
	    if(strtolower($country) == strtolower($v)){
		return $k;
	    }
	}
    }else{
	foreach($countries as $k => $v){
	    if(strtolower($code) == strtolower($k)){
		return $v;
	    }
	}
    }
}