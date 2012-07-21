<?php
class SHOPPINGCART extends PLUGIN {
    function SHOPPINGCART($args) {
	helper("shoppingcart");
	helper("member");
	$settings = array("location" => "sidebar", "php" => 1, "menuName" => "E-Commerce", "adminclass" => "updateForm:false showReEdit:false showPreview:false showPublish:false", "pluginsection" => "sitesettings");
	$settings['content'] = array("checkoutpage" => "");
	$this->start($settings, $args);
    }

    function update($old, $new) {	     // [ REQUIRED ]
	return $new;
    }
    function display() {		    // [ REQUIRED ]
		global $_GET;
		$products = "";
		if(addProduct()){
			header("Location:".urlPath($this->e("checkoutpage")));
			exit();
		}
		if(isset($_GET['result']) || isset($_GET['token'])){
			if(isset($_GET['result'])){
				$this->pxReceipt();
			}else{
				$this->payPalConfirm();
			}
		}else{
	    	if(isset($_GET['status']) && isset($_GET['chk']) && sketch("menu_guid")==$this->e("checkoutpage")){
				$tmp 		= explode("-",$_GET['status']);
				$tmp 		= end( $tmp );
				$invoiceID 	= intval($tmp);
				$r = getData("invoice","*","invoice_id=".intval($invoiceID));
				if($r->advance()){
					if($r->response_code==="0" && md5(intval(memberid())."-".$r->invoice_id)==$_GET['chk'] || adminCheck()){
					  clearShoppingCart();
					  $showReceipt	= true;
					  $deliveryInfo	= unserialize($r->invoice_details);
					  $products	= $deliveryInfo['itembreakdown'];
					  $internetBanking = trim($r->invoice_response)=="INTERNET BANKING REQUEST"? true : false;
					  unset($deliveryInfo['itembreakdown']);
					  @include(loadForm("shoppingcartPayment",false));
					}else{
					if(md5(intval(memberid())."-".$r->invoice_id)==$_GET['chk']){
						$errorMessage = "Sorry - you must be logged in to view your order.";
					}else{
						$errorMessage = "Sorry - your payment was not approved.<br />Reason:".$r->invoice_response."</p>";
					}
		    	}
			}
	    }else{
			if(sketch("menu_guid")==$this->e("checkoutpage")){
		    	if(isset($_POST['payonline']) || isset($_POST['banktransfer'])){
					if(isset($_POST['payonline'])){
						$this->pay();
					}else{
			    		if(isset($_POST['banktransfer'])){
							$this->bankTransfer();
			    		}
					}
		    	}else{
			if(isset($_POST['continue'])){
			    helper("validate");
			    $form = VALIDATE::loadForm("shoppingcartAddress");
			    if($form->processForm($_POST)){
					saveDeliveryInfo($form->getCleanedValues());
					@include(loadForm("shoppingcartPayment",false));
			    }else{
					$errorMessage = $form->getError();
			       @include(loadForm("shoppingcartAddress",false));
			    }
			}else{
			    if(isset($_POST['docheckout']) || isset($_POST['checkout'])){
					if($this->e("cctypes")=="paypal" && isset($_POST['checkout'])){
				    	$this->startPayPal();
					}else{
				    	if(is_array(getDeliveryInfo())){
							$_POST = getDeliveryInfo();
				    	}
				    	@include(loadForm("shoppingcartAddress",false));
					}
			    }else{
					@include(loadView("shoppingcartCheckout",false,true));
			    }
			}
		    }
		}
	    }
	}
    }

    function preview() {
		$this->display();
    }

    function filter($args=''){
		if(isset($args['currency'])){
		   echo $this->e("currency");
		}else{
			helper("session");
			$allProducts = (array)sessionGet("cart");
			$qty = 0;
			foreach($allProducts as $k => $amount){
				$qty += $amount;	
			}
			if($qty > 0 && sketch("menu_guid") != $this->e("checkoutpage")){
			?>
            <div class="sidebox">
            <h3>Shopping Cart</h3>
			<p class="amountInCart"><?php echo $qty; ?> Item<?php echo $qty > 1? "s": ""; ?> in cart.<br/ >
			<a href="<?php echo urlPath($this->e("checkoutpage")); ?>" class="button"><span class="icons rightarrow"></span>Checkout</a></p>
			</div><?php
			}
		}
    }

    function form() {			    // [ REQUIRED ]
		@include(loadForm("shoppingcartform",false));
    }

    function bankTransfer(){
		$itemBreakDown['itembreakdown'] = sessionGet('itembreakdown');
		$deliveryInfo		    	= (array)getDeliveryInfo();
		$data			    		= array();
		$data['page_id']	    	= intval(memberid());
		$data['invoice_details']    = serialize(array_merge($itemBreakDown,$deliveryInfo));
		$data['amount']		    	= floatval($itemBreakDown['itembreakdown']['amount']);
		$data['invoice_date']	    = date("Y-m-d H:i:s");
		$data['response_code']	    = 0;
		$data['invoice_response']   = "INTERNET BANKING REQUEST";
		
		
		foreach($itemBreakDown['itembreakdown'] as $key => $value){
			list($id,$size,$color) = explode(":",$key);
			$qty = $value[0];	
			$updated = getData("sketch_page","content","page_id=".intval($id));
			$updated->advance();
			$c = contentToArray($updated->content);
			$amountLeft = intval($c['product_stock']) - intval($qty) >= 0 ? intval($c['product_stock']) - intval($qty) : 0;
			if($c['product_stock'] != '-1'){
				$updateqty = array();
				$c['product_stock'] = intval($c['product_stock']) - intval($qty);
				$updateqty['content'] 	= serialize($c);
				$updateqty['edit'] 		= $updateqty['content'];
				setData("sketch_page",$updateqty,"Where page_id=".intval($id));
			}
		}
		
		$r			    			= ACTIVERECORD::keeprecord(insertDB("invoice",$data));
		if($r){
			$iid = lastInsertId();
			$this->email_notice($iid);
		}
    }
    function email_notice($iid){
	helper("email");
	if($this->e("email_address")!= ""){
	    $r = getData("invoice","*","WHERE invoice_id=".intval($iid)." AND (emailed=0 OR emailed IS NULL)"); // Dont Email twice
	    if($r){
			$r->advance();
			if($r->response_code==="0"){
				$data 						= contentToArray($r->invoice_details);
				$data['amount'] 			= $r->amount;
				$data['Invoice Response']   = $r->invoice_response;
				$data['Member Id']			= $r->page_id;
				$data['Invoice Ref No.']	= $r->invoice_ref;
				
				$totals						= 0;
				list($data['country'],)		= explode(":",$data['country']);
				$items 		= $data['itembreakdown'];
				foreach($items as $key => $value){
					if($key != 'sizes' && $key != 'colors'){
						if(is_array($value)){
							$itm_r = getData("sketch_page,sketch_menu","*",getSettings("prefix")."sketch_page.page_id=".intval($key));
							if($itm_r){
								$itm_r->advance();
								list($id,$size,$color) = explode(":",$key);
								$data['Item id: '.$id] = "Item Ordered: ".$itm_r->page_title.
															"<br />Item Cost: $".number_format($value[1],2,'.',',').'($NZ)ea
															 <br />Colour: '.$color.'
															 <br />Size: '.$size.'
															 <br />Amount Ordered: '.$value[0];
								$totals += $value[1] * $value[0];
							}
						}
					}
				}
				unset($data['itembreakdown']);
				unset($data['page_id']);
				unset($data['response_code']);
				unset($data['paid']);
				unset($data['emailed']);
				unset($data['result']);
				unset($data['continue']);
				unset($data['terms']);
				unset($data['submit']);
				$data['GST']			= "$".floatval($this->e("gst")) > 0? number_format(floatval($data['amount']) * 3 / 23,2,'.',',') : 0 ;
				if($data['GST'] > 0){
					$data['freight']	= floatval($data['amount']) - ((floatval($data['amount'])) * 3 / 23) - $totals;
					$data['freight']	= "$".number_format($data['freight'],2,'.',',');
				}else{
					$data['freight']	= ($data['amount'] - $totals);
				}
				if($data['freight'] < 0){
						unset($data['freight']);
				}else{
					$data['freight']    = "$".number_format($data['freight'],2,'.',',');	
				}
				if($data['GST'] == 0){
					unset($data['GST']);	
				}
				$data['View Order']	= urlPath(sketch("menu_guid"))."?status=".intval(memberid())."-".$iid."&chk=".md5(intval(memberid())."-".$iid);
				$data['amount']		= "$".number_format(floatval($data['amount']),2,'.',',');
				$rsp				= email(trim($this->e("email_from")),trim($this->e("email_from")).",".trim($this->e("email_address")),"Online Order and Payment",$data);
				unset($data['rrn']);
				unset($data['invoice_ref']);
				unset($data['Member Id']);
				unset($data['Invoice Ref No.']);
				unset($data['Packing Slip']);
				$rsp			= email(trim($this->e("email_from")),$data['email'],"Online Payment",$data);
				if($rsp =="success"){
					ACTIVERECORD::keeprecord(updateDB("invoice",array("emailed"=>1),"WHERE invoice_id=".$iid));
				}
			}
	    }
	}
	header("Location: ".urlPath(sketch("menu_guid"))."?status=".intval(memberid())."-".$iid."&chk=".md5(intval(memberid())."-".$iid));
	exit();
    }
    function pay(){
		if($this->e("cctypes")=="dps"){
	    	$this->dpsRedirect();
		}else{
	    	if($this->e("cctypes")=="paypal"){
				$this->payPalConfirmed();
	    	}
		}
    }
    function dpsRedirect(){
		global $_POST;
		helper("pxpay");
		$itemBreakDown['itembreakdown'] = sessionGet("itembreakdown");
		$deliveryInfo			= (array)getDeliveryInfo();
		$PxAccess_Url			= $this->e('vpcURL');
		$PxAccess_Userid		= $this->e('vpc_Merchant');
		$PxAccess_Key			= $this->e('secure_secret');
		$Mac_Key				= substr($this->e('secure_secret'),strlen($this->e('secure_secret'))-16);
		$pxaccess				= new PxAccess($PxAccess_Url, $PxAccess_Userid, $PxAccess_Key, $Mac_Key);

		$SQL	= "INSERT INTO ".getSettings("prefix")."invoice (page_id,invoice_details,amount,invoice_date) VALUES (".intval(memberid()).",'".serialize(array_merge($itemBreakDown,$deliveryInfo))."',".floatval($itemBreakDown['itembreakdown']['amount']).",'".date("Y-m-d :H:i:s")."')";
		$r	= ACTIVERECORD::keeprecord($SQL);
	if($r){
	    $iid	    = lastInsertId();
	    $request	    = new PxPayRequest();
	    $AmountInput    = number_format(floatval($itemBreakDown['itembreakdown']['amount']),2,'.','');
	    #Set up PxPayRequest Object
	    $request->setAmountInput($AmountInput);
	    $request->setTxnData1($this->e('ordertitle'));	# whatever you want to appear

	    $request->setTxnData2(memberid());		# whatever you want to appear
	    $request->setTxnData3($iid);			# whatever you want to appear

	    $request->setTxnType("Purchase");
	    $request->setInputCurrency("NZD");
	    $request->setMerchantReference($this->e('vpc_MerchTxnRef')."-".intval(memberid())."-".$iid); # fill this with your order number
	    $request->setEmailAddress(trim($this->e("email_from")));
	    $request->setUrlFail(urlPath(sketch("menu_guid")));
	    $request->setUrlSuccess(urlPath(sketch("menu_guid")));

	    #Call makeResponse of PxAccess object to obtain the 3-DES encrypted payment request
	    $request_string = $pxaccess->makeRequest($request);
	    header("Location: $request_string");
	    exit();
	}
    }
    function pxReceipt(){
	global $_GET;
	helper("pxpay");
	$PxAccess_Url	    = $this->e('vpcURL'); //"https://www.paymentexpress.com/pxpay/pxpay.aspx";
	$PxAccess_Userid    = $this->e('vpc_Merchant');
	$PxAccess_Key	    = $this->e('secure_secret');
	$Mac_Key	    	= substr($this->e('secure_secret'),strlen($this->e('secure_secret'))-16);
	$pxaccess	    	= new PxAccess($PxAccess_Url, $PxAccess_Userid, $PxAccess_Key, $Mac_Key);
	$enc_hex	    	= $_GET["result"];
	#getResponse method in PxAccess object returns PxPayResponse object
	#which encapsulates all the response data
	$rsp = $pxaccess->getResponse($enc_hex);
	if ($rsp->getStatusRequired() == "1"){
		$result = "An error has occurred.";
	 }elseif ($rsp->getSuccess() == "1"){
		$result = "The transaction was approved.";
		$response_code = 0;
	 }else{
		$result = "The transaction was declined.";
		$response_code = "F";
	 }

	# the following are the fields available in the PxPayResponse object
	$Success           = $rsp->getSuccess();   # =1 when request succeeds
	$Retry             = $rsp->getRetry();     # =1 when a retry might help
	$StatusRequired    = $rsp->getStatusRequired();      # =1 when transaction "lost"
	$AmountSettlement  = $rsp->getAmountSettlement();
	$AuthCode          = $rsp->getAuthCode();  # from bank
	$CardName          = $rsp->getCardName();  # e.g. "Visa"
	$receiptNo	   	   = $rsp->getDpsTxnRef();

	# the following values are returned, but are from the original request
	$TxnType           = $rsp->getTxnType();
	$TxnData1          = $rsp->getTxnData1();
	$TxnData2          = $rsp->getTxnData2();
	$TxnData3          = $rsp->getTxnData3();
	$CurrencyInput     = $rsp->getCurrencyInput();
	$EmailAddress      = $rsp->getEmailAddress();
	$merchTxnRef 	   = $rsp->getMerchantReference();

	$MerchantTxnId	   = $rsp->getMerchantTxnId();
	$CardNumber	   		= $rsp->getCardNumber();
	$DateExpiry	   		= $rsp->getDateExpiry();
	$CardHolderName	   = $rsp->getCardHolderName();

	$invoiceid	   		= end(explode('-',$merchTxnRef));

	$SQL		   		= "UPDATE ".getSettings("prefix")."invoice SET rrn=".sketch("db")->quote($receiptNo).",amount='".floatval($AmountSettlement)."', invoice_ref=".sketch("db")->quote($merchTxnRef).",invoice_response='".$result."', response_code='".$response_code."' WHERE invoice_id='".intval($invoiceid)."'";
	$r		   			= ACTIVERECORD::keeprecord($SQL,"invoice");
	if($rsp->getSuccess()== "1"){
		$invoi = getData("invoice","invoice_details","WHERE invoice_id='".intval($invoiceid)."'");
		$invoi->advance();
		$c = contentToArray($invoi->invoice_details);
		foreach($c['itembreakdown'] as $key => $value){
			if(isset($value[0]) && $value[0] > 0){
				list($id,$size,$color) = explode(":",$key);
				$qty = $value[0];	
				$updated = getData("sketch_page","content","page_id=".intval($id));
				$updated->advance();
				$cin = contentToArray($updated->content);
				$amountLeft = intval($cin['product_stock']) - intval($qty) >= 0 ? intval($cin['product_stock']) - intval($qty) : 0;
				if($cin['product_stock'] != '-1'){
					$data = array();
					$cin['product_stock'] = intval($cin['product_stock']) - intval($qty);
					$data['content'] 	= serialize($cin);
					$data['edit'] 		= $data['content'];
					setData("sketch_page",$data,"Where page_id=".intval($id));
				}
			}
		}
	    $this->email_notice($invoiceid);
	    header("Location: ".urlPath(sketch("menu_guid"))."?status=".intval(memberid())."-".$invoiceid."&chk=".md5(intval(memberid())."-".$invoiceid));
	    exit();
	}else{
	     $errorMessage = "Sorry - your payment was not approved.<br />Reason:".$result."</p>";
	    @include(loadForm("shoppingcartPayment",false));
	}
    }
    function startPayPal(){
		helper("paypal");
		$resArray		= CallShortcutExpressCheckout ($this->getPayPalDetails());
		$ack			= strtoupper($resArray["ACK"]);
		if($ack=="SUCCESS" || $ack=="SUCCESSWITHWARNING"){
	    	RedirectToPayPal ( $resArray["TOKEN"],$this->getPayPalDetails());
		}else{
	    	$errorMessage =	 "PayPal API Call Failed.<br />Error Code: ". urldecode($resArray["L_ERRORCODE0"]);
	    	$errorMessage .=     "<br/>Error Message: ". urldecode($resArray["L_LONGMESSAGE0"]);
	    	$errorMessage .=     "<br/ >Severity Code: ". urldecode($resArray["L_SEVERITYCODE0"]);
	    	@include(loadView("shoppingcartCheckout",false,true));
		}
    }
    function getPayPalDetails(){
		global $_SESSION;
		$extras					= array();
		$extras['SandboxFlag']	= $this->e("payPalLive")== "yes" ? true : false;
		$extras['API_Endpoint']	= ($this->e("payPalLive") == "yes") ? "https://api-3t.paypal.com/nvp" : "https://api-3t.sandbox.paypal.com/nvp";
		$extras['API_UserName'] = $this->e("payPalUsername");
		$extras['API_Password'] = $this->e("payPalPassword");
		$extras['API_Signature']= $this->e("payPalSignature");
		$extras['sBNCode']		= "PP-ECWizard";
		$extras['USE_PROXY']	= false;
		$extras['PROXY_HOST']	= '127.0.0.1';
		$extras['PROXY_PORT']	= '808';
		$extras['PAYPAL_URL']	= ($this->e("payPalLive") == "yes")  ? "https://www.paypal.com/webscr?cmd=_express-checkout&token=" : "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
		$extras['version']		= "64";
		$extras['paymentAmount']= $_SESSION["paymentAmount"];
		$extras['currencyCodeType'] = $this->e("payPalCurrencyCode","NZD");
		$extras['paymentType']	= "Sale";
		$extras['returnURL']	= urlPath(sketch("menu_guid"));
		$extras['cancelURL']	= urlPath(sketch("menu_guid"));
		$extras['TOKEN']		= sessionGet("token");
		$extras['payer_id']		= sessionGet("payer_id");
		return $extras;
    }
    function payPalConfirmed(){
		global $_SESSION;
		helper("paypal");
		$resArray = ConfirmPayment ($this->getPayPalDetails());
		$ack = strtoupper($resArray["ACK"]);
	if( $ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING" ){
	    $itemBreakDown['itembreakdown'] = sessionGet("itembreakdown");
	    $deliveryInfo		    = (array)getDeliveryInfo();
	    $transactionId		= @$resArray["PAYMENTINFO_0_TRANSACTIONID"];	// ' Unique transaction ID of the payment. Note:  If the PaymentAction of the request was Authorization or Order, this value is your AuthorizationID for use with the Authorization & Capture APIs.
	    $transactionType 	= @$resArray["PAYMENTINFO_0_TRANSACTIONTYPE"];		//' The type of transaction Possible values: l  cart l  express-checkout
	    $paymentType		= @$resArray["PAYMENTINFO_0_PAYMENTTYPE"];			//' Indicates whether the payment is instant or delayed. Possible values: l  none l  echeck l  instant
	    $orderTime 			= @$resArray["ORDERTIME"];			//' Time/date stamp of payment
	    $amt				= @$resArray["PAYMENTINFO_0_AMT"];		//' The final amount charged, including any shipping and taxes from your Merchant Profile.
	    $currencyCode		= @$resArray["CURRENCYCODE"];			//' A three-character currency code for one of the currencies listed in PayPay-Supported Transactional Currencies. Default: USD.
	    $feeAmt				= @$resArray["FEEAMT"];				//' PayPal fee amount charged for the transaction
	    $settleAmt			= @$resArray["SETTLEAMT"];			//' Amount deposited in your PayPal account after a currency conversion.
	    $taxAmt				= @$resArray["TAXAMT"];				//' Tax charged on the transaction.
	    $exchangeRate		= @$resArray["EXCHANGERATE"];			//' Exchange rate if a currency conversion occurred. Relevant only if your are billing in their non-primary currency. If the customer chooses to pay with a currency other than the non-primary currency, the conversion occurs in the customer's account.
	    $paymentStatus		= @$resArray["PAYMENTINFO_0_PAYMENTSTATUS"];
	    $pendingReason		= @$resArray["PAYMENTINFO_0_PENDINGREASON"];
	    $reasonCode			= @$resArray["PAYMENTINFO_0_REASONCODE"];
	    if($resArray['ACK']!="Success"){
			$errorMessage = "Sorry - your payment was not approved.<br />Payment Status:".$paymentStatus."<br/>Reason code".$reasonCode."</p>";
			@include(loadForm("shoppingcartPayment",false));
	    }else{
		$SQL    = "INSERT INTO ".getSettings("prefix")."invoice (response_code,rrn,invoice_ref,invoice_response,page_id,invoice_details,amount,invoice_date) ".
			    "VALUES (0,".sketch("db")->quote($transactionId).",".
			    sketch("db")->quote($paymentType).",".
			    sketch("db")->quote(@$resArray['ACK']).
			    ",".intval(memberid()).",'".serialize(array_merge($itemBreakDown,$deliveryInfo))."',".floatval($amt).",'".date("Y-m-d :H:i:s")."')";
		$r	    = ACTIVERECORD::keeprecord($SQL);
		$invoiceid  = lastInsertId();
		
		foreach($itemBreakDown['itembreakdown'] as $key => $value){
			list($id,$size,$color) = explode(":",$key);
			$qty = $value[0];	
			$updated = getData("sketch_page","content","page_id=".intval($id));
			$updated->advance();
			$c = contentToArray($updated->content);
			$amountLeft = intval($c['product_stock']) - intval($qty) >= 0 ? intval($c['product_stock']) - intval($qty) : 0;
			if($c['product_stock'] != '-1'){
				$data = array();
				$c['product_stock'] = intval($c['product_stock']) - intval($qty);
				$data['content'] 	= serialize($c);
				$data['edit'] 		= $data['content'];
				setData("sketch_page",$data,"Where page_id=".intval($id));
			}
		}
		
		$this->email_notice($invoiceid);
	    }
	}else{
	    $errorMessage =	 "PayPal API Call Failed.<br />Error Code: ". urldecode($resArray["L_ERRORCODE0"]);
	    $errorMessage .=     "<br/>Error Message: ". urldecode($resArray["L_LONGMESSAGE0"]);
	    $errorMessage .=     "<br/ >Severity Code: ". urldecode($resArray["L_SEVERITYCODE0"]);
	    @include(loadForm("shoppingcartPayment",false));
	}
    }
    function payPalConfirm(){
		global $_GET;
		helper("paypal");
		$token	    = $_GET['token'];
		$resArray   = GetShippingDetails( $token,$this->getPayPalDetails());
		$ack = strtoupper($resArray["ACK"]);
		// A payerID should be set - if not -user has cancelled
		if( $ack == "SUCCESS" || $ack == "SUCESSWITHWARNING"){
	    	if(!isset($resArray['PAYERID'])){
				$errorMessage = "Payment Cancelled";
				@include(loadView("shoppingcartCheckout",false,true));
	   		}else{
			$shipDetails = array();
			sessionSet("token",@$resArray['TOKEN']);
			sessionSet("invoiceNumber",@$resArray["INVNUM"]);
			sessionSet("payer_id",@$resArray["PAYERID"]);
			$shipDetails['email'] 		= @$resArray["EMAIL"];		// ' Email address of payer.
			$shipDetails['payerStatus']	= @$resArray["PAYERSTATUS"];	// ' Status of payer. Character length and limitations: 10 single-byte alphabetic characters.
			$shipDetails['salutation']	= @$resArray["SALUTATION"];	// ' Payer's salutation.
			$shipDetails['firstname']	= @$resArray["FIRSTNAME"];	// ' Payer's first name.
			$shipDetails['middlename']	= @$resArray["MIDDLENAME"];	// ' Payer's middle name.
			$shipDetails['lastname']	= @$resArray["LASTNAME"];	// ' Payer's last name.
			$shipDetails['suffix']		= @$resArray["SUFFIX"];		// ' Payer's suffix.
			$shipDetails['business']	= @$resArray["BUSINESS"];	// ' Payer's business name.
			$shipDetails['ship To Name']	= @$resArray["SHIPTONAME"];	// ' Person's name associated with this address.
			$shipDetails['ship To Street']	= @$resArray["SHIPTOSTREET"];	// ' First street address.
			$shipDetails['ship To Street2']	= @$resArray["SHIPTOSTREET2"];	// ' Second street address.
			$shipDetails['ship To City']	= @$resArray["SHIPTOCITY"];	// ' Name of city.
			$shipDetails['ship To State']	= @$resArray["SHIPTOSTATE"];	// ' State or province
			$shipDetails['ship To Zip']		= @$resArray["SHIPTOZIP"];	// ' U.S. Zip code or other country-specific postal code.
			$shipDetails['phone']		= @$resArray["PHONENUM"];	// ' Payer's contact telephone number. Note:  PayPal returns a contact telephone number only if your Merchant account profile settings require that the buyer enter one.
			$shipDetails['country']		= false;
			$cCode				= getCountryCode("",@$resArray["COUNTRYCODE"]);
			foreach(explode(",",$this->e("countries")) as $key => $value){
		    	if(stripos($value,$cCode)!==false){
					$shipDetails['country'] = $value;
		    	}
			}
			if($shipDetails['country'] !== false){
		    	$errorMessage =	 "Sorry - we do not ship to that country.";
		    	@include(loadView("shoppingcartCheckout",false,true));
			}else{
		    	saveDeliveryInfo($shipDetails);
		    	@include(loadForm("shoppingcartPayment",false));
			}
	    }
	} else {
	    $errorMessage =	 "PayPal API Call Failed.<br />Error Code: ". urldecode($resArray["L_ERRORCODE0"]);
	    $errorMessage .=     "<br/>Error Message: ". urldecode($resArray["L_LONGMESSAGE0"]);
	    $errorMessage .=     "<br/ >Severity Code: ". urldecode($resArray["L_SEVERITYCODE0"]);
	    @include(loadView("shoppingcartCheckout",false,true));
	}

    }
}