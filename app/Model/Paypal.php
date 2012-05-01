<?php
App::uses('AppModel', 'Model');
/**
 * Paypal Model
 *
 * @property File $File
 */
class Paypal extends AppModel {
	
	public $useTable = false;
	 
	//configuration
	protected $environment = 'sandbox';	// or 'beta-sandbox' or 'live'
	protected $version = '72.0';
	
	//Add creadentials below
	//https://cms.paypal.com/cms_content/US/en_US/files/developer/PP_ExpressCheckout_IntegrationGuide.pdf
	public $API_UserName  = 'robksawyer_api1.gmail.com';
	public $API_Password  = 'M53YBES6NM45ZQ64';
	public $API_Signature = 'AOFNga5P-1b18OfvNXqpc9CeHkghAZtND.DpIr8HN2-wfPJvQHu.oOuS';
	
	//Add test credentials
	public $API_Sandbox_UserName  = 'robmer_1335893675_biz_api1.gmail.com';
	public $API_Sandbox_Password  = '1335893700';
	public $API_Sandbox_Signature = 'AFcWxV21C7fd0v3bYYYRCpSSRl31AUl7Gx0V9Do9gaeVwNHpLjQ6umBI';
	
	//variables 
	public $errors		= null;
	public $token		= null;
	public $transId		= null;
		
	 
	/**
	 * Send HTTP POST Request
	 *
	 * @param	string	The API method name
	 * @param	string	The POST Message fields in &name=value pair format
	 * @return	array	Parsed HTTP Response body
	 */
	public function PPHttpPost($methodName, $nvpStr) {
		
		$API_Endpoint = "https://api-3t.paypal.com/nvp";
		
		// Set up your API credentials, PayPal end point, and API version.
		if("sandbox" === $this->environment || "beta-sandbox" === $this->environment) {
			$API_UserName = $this->API_Sandbox_UserName;
			$API_Password = $this->API_Sandbox_Password;
			$API_Signature = $this->API_Sandbox_Signature;
			$API_Endpoint = "https://api-3t.$this->environment.paypal.com/nvp";
		}else{
			$API_UserName = $this->API_UserName;
			$API_Password = $this->API_Password;
			$API_Signature = $this->API_Signature;
		}
	
		$version = urlencode($this->version);
 
		// Set the curl parameters.
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
 
		// Turn off the server and peer verification (TrustManager Concept).
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
 
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
 
		// Set the API operation, version, and API signature in the request.
		$nvpreq = "METHOD=$methodName&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature&$nvpStr";

		// Set the request as a POST FIELD for curl.
		curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
 
		// Get response from the server.
		$httpResponse = curl_exec($ch);
 
		if(!$httpResponse) {
			exit("$methodName failed: ".curl_error($ch).'('.curl_errno($ch).')');
		}
 
		// Extract the response details.
		$httpResponseAr = explode("&", $httpResponse);
 
		$httpParsedResponseAr = array();
		foreach ($httpResponseAr as $i => $value) {
			$tmpAr = explode("=", $value);
			if(sizeof($tmpAr) > 1) {
				$httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
			}
		}
 
		if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) {
			exit("Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.");
		}
 
		return $httpParsedResponseAr;
	}
	 
	/*
	 * get PayPal Url for redirecting page
	 */
	public function getPaypalUrl($token = '') {
		$payPalURL = "https://www.paypal.com/incontext?token={$token}";
		if("sandbox" === $this->environment || "beta-sandbox" === $this->environment) {			
			$payPalURL = "https://www.sandbox.paypal.com/incontext?token={$token}";
		}
		return $payPalURL;
	}
 
		 
	/*
	 * call PayPal API: SetExpressCheckout
	 */
	public function setExpressCheckout($nvpStr = '') {
		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = $this->PPHttpPost('SetExpressCheckout', $nvpStr);
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$this->token = urldecode($httpParsedResponseAr["TOKEN"]);
			return true;
		} else	{
			$this->errors = $httpParsedResponseAr;
			return false;
		}
	}
 
	/*
	 * call PayPal API: DoExpressCheckoutPayment
	 */
	public function doExpressCheckoutPayment($nvpStr = '') {
		// Execute the API operation; see the PPHttpPost function above.
		$httpParsedResponseAr = $this->PPHttpPost('DoExpressCheckoutPayment', $nvpStr);
		if("SUCCESS" == strtoupper($httpParsedResponseAr["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($httpParsedResponseAr["ACK"])) {
			$this->transId = urldecode($httpParsedResponseAr["PAYMENTINFO_0_TRANSACTIONID"]);
			return true;
		} else	{
			$this->errors = $httpParsedResponseAr;
			return false;
		}		
	}
	
	/**
	 * Builds the NVP String
	* @param int codePrice
	* @param string itemName 
	* @param upload_id
	* @param user_id
	* @param int totalCodes 
	* Example: 'RETURNURL=http://grabitdown/uploads/paypal_return/100&CANCELURL=http://grabitdown/uploads/paypal_cancel&PAYMENTREQUEST_0_CURRENCYCODE=USD&PAYMENTREQUEST_0_AMT=25&PAYMENTREQUEST_0_ITEMAMT=25&PAYMENTREQUEST_0_PAYMENTACTION=Sale&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital&L_PAYMENTREQUEST_0_NAME0=100 download codes&L_PAYMENTREQUEST_0_AMT0=25&L_PAYMENTREQUEST_0_QTY0=1&NOSHIPPING=1'
	 */
	public function buildNVPString($codePrice = 0, $itemName = '', $upload_id=null, $user_id='', $totalCodes = 0){
		//build nvp string
		//use your own logic to get and set each variable
		$returnURL = Router::url(array('controller'=>'uploads','action'=>'paypal_return','uuid'=>$user_id,'uid'=>$upload_id,'total_codes'=>$totalCodes),true);
		$cancelURL = Router::url(array('controller'=>'uploads','action'=>'paypal_cancel'),true);
		
		$currencyCode = 'USD';
		$paymentAmount = $codePrice;
		$paymentItemAmount = $codePrice;
		$paymentQty = 1;
		$totalPaymentAmount = $codePrice;
		
		$nvpStr =
		 "RETURNURL=$returnURL&CANCELURL=$cancelURL"
		."&PAYMENTREQUEST_0_CURRENCYCODE=$currencyCode"
		."&PAYMENTREQUEST_0_AMT=$paymentAmount"
		."&PAYMENTREQUEST_0_ITEMAMT=$paymentItemAmount"
		."&PAYMENTREQUEST_0_PAYMENTACTION=Sale"
		."&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital"
		."&L_PAYMENTREQUEST_0_NAME0=$itemName"
		."&L_PAYMENTREQUEST_0_AMT0=$totalPaymentAmount"
		."&L_PAYMENTREQUEST_0_QTY0=$paymentQty"
		."&NOSHIPPING=1"
		;
		
		return $nvpStr;
	}
	
	/**
	 * Builds the NVP String
	* @param int codePrice
	* @param string itemName 
	* @param int totalCodes 
	* Example: 'RETURNURL=http://grabitdown/uploads/paypal_return/100&CANCELURL=http://grabitdown/uploads/paypal_cancel&PAYMENTREQUEST_0_CURRENCYCODE=USD&PAYMENTREQUEST_0_AMT=25&PAYMENTREQUEST_0_ITEMAMT=25&PAYMENTREQUEST_0_PAYMENTACTION=Sale&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital&L_PAYMENTREQUEST_0_NAME0=100 download codes&L_PAYMENTREQUEST_0_AMT0=25&L_PAYMENTREQUEST_0_QTY0=1&NOSHIPPING=1'
	 */
	public function buildNVPCheckoutString($token='',$payerId=null,$codePrice = 0,$itemName = ''){
		//build nvp string
		$currencyCode = 'USD';
		$paymentAmount = $codePrice;
		$paymentItemAmount = $codePrice;
		$paymentQty = 1;
		$totalPaymentAmount = $codePrice;
		
		$nvpStr =
		 "TOKEN=$token&PAYERID=$payerId"
		."&PAYMENTREQUEST_0_CURRENCYCODE=$currencyCode"
		."&PAYMENTREQUEST_0_AMT=$paymentAmount"
		."&PAYMENTREQUEST_0_ITEMAMT=$paymentItemAmount"
		."&PAYMENTREQUEST_0_PAYMENTACTION=Sale"
		."&L_PAYMENTREQUEST_0_ITEMCATEGORY0=Digital"
		."&L_PAYMENTREQUEST_0_NAME0=$itemName"
		."&L_PAYMENTREQUEST_0_AMT0=$totalPaymentAmount"
		."&L_PAYMENTREQUEST_0_QTY0=$paymentQty"
		."&NOSHIPPING=1"
		;
		
		return $nvpStr;
	}
}