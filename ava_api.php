<?php
require('includes/configure.php');
require(DIR_WS_INCLUDES . 'database_tables.php');
require(DIR_WS_FUNCTIONS . 'database.php');
tep_db_connect() or die('Unable to connect to database server!');
  $configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);
  while ($configuration = tep_db_fetch_array($configuration_query)) {
    define($configuration['cfgKey'], $configuration['cfgValue']);
  }
require(DIR_WS_FUNCTIONS . 'general.php');  
require(DIR_WS_FUNCTIONS . 'sessions.php');

// alavara code #starts
require_once DIR_WS_MODULES . 'avatax/ava_tax.php';
require_once DIR_WS_MODULES . 'avatax/credentials.php'; 

if( (isset($_GET['mode'])) && ($_GET['mode'] == 'validateAddressDetails') ){
	
	 
	 $json = array();
	 
	 try {
		 
		$port = new AddressServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
		$address = new Address();
		$address->setLine1($_POST['entry_street_address_original']);
		$address->setLine2($_POST['entry_suburb_original']);
		$address->setCity($_POST['entry_city_original']);
		$address->setRegion($_POST['entry_state_original']);
		$address->setPostalCode($_POST['entry_postcode_original']);
		$result = $port->validate(new ValidateRequest($address,TextCase::$Upper));
		
		if ($result->getResultCode() == SeverityLevel::$Success){
			$addresses = $result->getValidAddresses();
			if (sizeof($addresses) > 0){
				$validAddress = $addresses[0];
				$json['success'] = 1;
				$json['entry_street_address_original'] = $validAddress->getLine1();
				$json['entry_suburb_original'] = $validAddress->getLine2();
				$json['entry_city_original'] = $validAddress->getCity();
				$json['entry_state_original'] = $validAddress->getRegion();
				$json['entry_postcode_original'] = $validAddress->getPostalCode();
				$json['entry_country_id_original'] = $validAddress->getCountry();
			}else{
				$json['error'] = 1;
			}
		}else{
			$json['error'] = 1;
		}
		 
		 
	 }catch (SoapFault $exception) {
	 	$json['error'] = 1;
	 }
	 
	 // add to log table
	updateAvataxLogTable($port->__getLastRequest(),$port->__getLastResponse(),'address_test');
	 
	 echo json_encode($json);
	 
	 exit;
}


if( (isset($_GET['mode'])) && ($_GET['mode'] == 'validateCheckoutAddressDetails') ){
	$address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$_POST['customer_id'] . "' and address_book_id = '" . (int)$_POST['sendto'] . "'");
	
	$address_to_validate = tep_db_fetch_array($address_query);
	
	$list_of_valid_countries = explode(",",MODULE_ORDER_TOTAL_AVATAX_VALID_COUNTRIES);
	
	$json = array();
	
	
	if(!in_array($address_to_validate['country_id'],$list_of_valid_countries)){
		$json['error'] = 2;
	}else{
			try {
			 
			$port = new AddressServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
			$address = new Address();
			$address->setLine1($address_to_validate['street_address']);
			$address->setLine2($address_to_validate['suburb']);
			$address->setCity($address_to_validate['city']);
			$address->setRegion($address_to_validate['state']);
			$address->setPostalCode($address_to_validate['postcode']);
			$result = $port->validate(new ValidateRequest($address,TextCase::$Upper));
			
			if ($result->getResultCode() == SeverityLevel::$Success){
				$addresses = $result->getValidAddresses();
				if (sizeof($addresses) > 0){
					$validAddress = $addresses[0];
					$json['success'] = 1;
					$json['entry_street_address_original'] = $validAddress->getLine1();
					$json['entry_suburb_original'] = $validAddress->getLine2();
					$json['entry_city_original'] = $validAddress->getCity();
					$json['entry_state_original'] = $validAddress->getRegion();
					$json['entry_postcode_original'] = $validAddress->getPostalCode();
					$json['entry_country_id_original'] = $validAddress->getCountry();
				}else{
					$json['error'] = 1;
				}
			}else{
				$json['error'] = 1;
			}
			 
			 
		 }catch (SoapFault $exception) {
			$json['error'] = 1;
		 }
		 // add to log table
		updateAvataxLogTable($port->__getLastRequest(),$port->__getLastResponse(),'address_test');
	}
	
	echo json_encode($json);
	 
	exit;
}

if( (isset($_POST['mode'])) && ($_POST['mode'] == 'update_doc_code') ){
	try {
		$client3 = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
		$post_tax_request = new PostTaxRequest();
		$post_tax_request->setDocType($_POST['doc_type']); 
		$post_tax_request->setDocCode($_POST['doc_code']);
		$post_tax_request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);
		$post_tax_request->setDocDate($_POST['doc_date']);
		$post_tax_request->setTotalAmount($_POST['doc_amount']);
		$post_tax_request->setTotalTax($_POST['doc_total_tax']);
		$post_tax_request->setHashCode($_POST['doc_hash_code']);
		$post_tax_request->setCommit(false);
		
		// update doc code with our system generated order id #start
		$post_tax_request->setNewDocCode("OBN-".$_POST['oID']);
		// update doc code with our system generated order id #ends
		
		$post_tax_response = $client3->postTax($post_tax_request);
		
		if ($post_tax_response->getResultCode() == SeverityLevel::$Success) {
			echo "1";
		}else{
			echo "-1";   
		}
		
	} catch (SoapFault $exception) {
	    echo "-1";
        /*$msg = 'SOAP Exception: ';

        if ($exception) {
            $msg .= $exception->faultstring;
        }

        echo 'AvaTax message is: ' . $msg;
        echo "<br>";
        echo 'AvaTax last request is: ' . $client3->__getLastRequest();
        echo "<br>";
        echo 'AvaTax last response is: ' . $client3->__getLastResponse();*/
	}
		// add to log table
	updateAvataxLogTable($client3->__getLastRequest(),$client3->__getLastResponse(),'update_doccode');
}


require(DIR_WS_INCLUDES.'application_bottom.php');
?>