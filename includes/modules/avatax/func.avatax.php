<?php
/**
 * ot_avatax order-total module
 *
 * @package orderTotal
 * @copyright Copyright 2012 adTumbler, Inc.
 * @version 1.39 2012-11-23 16:00:00Z adb $
*/

/**
 * osCommerce Connector for AvaTax
 *
 */

function avatax_lookup_tax($order, $products) {

    require_once DIR_WS_MODULES . 'avatax/ava_tax.php';

    require_once DIR_WS_MODULES . 'avatax/credentials.php';

    global $db, $messageStack;

    // Calculate the next expected order id (adapted from code written by Eric Stamper - 01/30/2004 Released under GPL)

    $new_order_id = 0;

    $last_order_row = tep_db_query("select * from " . TABLE_ORDERS . " order by orders_id desc limit 1");

    $last_order_array = tep_db_fetch_array($last_order_row);

    $last_order_id = intval($last_order_array['orders_id']);

    $new_order_id = ($last_order_id + 1);

    $client = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);

    $request = new GetTaxRequest();

    // Construct Origin Address

    $origin = new AvaAddress();
	
	$origin->setLine1(MODULE_ORDER_TOTAL_AVATAX_STREET_ADDRESS);

    $origin->setLine2(MODULE_ORDER_TOTAL_AVATAX_SUBURB);

    $origin->setCity(MODULE_ORDER_TOTAL_AVATAX_CITY);

    $origin->setRegion(MODULE_ORDER_TOTAL_AVATAX_STATE);

    $origin->setPostalCode(MODULE_ORDER_TOTAL_AVATAX_ZIPCODE);
	
    $request->setOriginAddress($origin);

    //Add Destination address

    $destination = new AvaAddress();

    $destination->setLine1($order->delivery['street_address']);

    $destination->setLine2($order->delivery['suburb']);

    $destination->setCity($order->delivery['city']);

    $destination->setRegion($order->delivery['state']);

    $destination->setPostalCode($order->delivery['postcode']);

    $request->setDestinationAddress($destination);     //Address

    $request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);

    if (tep_href_link(FILENAME_CHECKOUT_PROCESS)) {
        $request->setDocType('SalesInvoice');
    } else {
        $request->setDocType('SalesOrder');
    }

    //$request->setDocCode(time() .'-OBN-'. $new_order_id . '');
	$request->setDocCode(time() .'-OBN-'. time());

    $dateTime = new DateTime();

    $request->setDocDate(date_format($dateTime, "Y-m-d"));           //date

    // $request->setSalespersonCode("");             // string Optional
    
	if ( (!tep_session_is_registered('customer_id')) || (empty($_SESSION['customer_id'])) ) {
		$request->setCustomerCode(rand(99,9999)); // fix applied for one page checkout 
	}else{
		$request->setCustomerCode($_SESSION['customer_id']); // $account - string Required
	}
	

    $request->setCustomerUsageType("");   //string   Entity Usage

    $request->setDiscount(0.00);            //decimal

    $request->setPurchaseOrderNo("");     //string Optional

    
	$exemption = '';
	if ( (tep_session_is_registered('customer_id')) || (!empty($_SESSION['customer_id'])) ) {
	
		$customer_exemption_query = tep_db_fetch_array(tep_db_query("select is_tax_exempt,entry_company_tax_id from customers where customers_id  = '".$_SESSION['customer_id']."'"));
		
		if( ( ($customer_exemption_query['is_tax_exempt'] == '1') || ((tep_session_is_registered('sppc_customer_group_tax_exempt')) && (!empty($_SESSION['sppc_customer_group_tax_exempt']))) ) ){
			if(!empty($customer_exemption_query['entry_company_tax_id'])){
				$exemption = $customer_exemption_query['entry_company_tax_id'];
			}
				
		}
	}
	
	
	
	$request->setExemptionNo($exemption);         //string   if not using ECMS which keys on customer code

    $request->setDetailLevel(DetailLevel::$Tax);         //Summary or Document or Line or Tax or Diagnostic

    // $request->setLocationCode("");        //string Optional - aka outlet id for tax forms

	
    $i = 1;
	
	foreach ($products as $k => $product) {
		
		$product_upc_query = tep_db_fetch_array(tep_db_query("select p.avalara_tax_code,pe.upc_ean from products as p left join products_extended as pe on (p.products_id = pe.osc_products_id) where p.products_id = '".tep_get_prid($product['id'])."'"));
		
		if(!empty($product_upc_query['upc_ean'])){
			$item_code = $product_upc_query['upc_ean'];
		}else{
			$item_code = $product['model'];
		}
		
		$product_tax_code = '';
		if(!empty($product_upc_query['avalara_tax_code'])){
			$product_tax_code = $product_upc_query['avalara_tax_code'];
		}
		
		
		${'line' . $i} = new Line();

        ${'line' . $i}->setNo($i);

        ${'line' . $i}->setItemCode($item_code);

        ${'line' . $i}->setDescription($product['name']);

        ${'line' . $i}->setTaxCode($product_tax_code);

        ${'line' . $i}->setQty($product['qty']);

        ${'line' . $i}->setAmount($product['final_price'] * $product['qty']);

        ${'line' . $i}->setDiscounted('false');

        ${'line' . $i}->setRevAcct('');

        ${'line' . $i}->setRef1('');

        ${'line' . $i}->setRef2('');

        ${'line' . $i}->setExemptionNo('');

        ${'line' . $i}->setCustomerUsageType('');

        $lines[] = ${'line' . $i};

        $i++;

    }



    ${'line' . $i} = new Line();

    ${'line' . $i}->setNo($i);

    ${'line' . $i}->setItemCode('Shipping');

    ${'line' . $i}->setDescription('Public Carrier');

    ${'line' . $i}->setTaxCode(MODULE_ORDER_TOTAL_AVATAX_FREIGHT_TAX_CODE);

    ${'line' . $i}->setQty(1);

    ${'line' . $i}->setAmount($order->info['shipping_cost']);

    ${'line' . $i}->setDiscounted('false');

    ${'line' . $i}->setRevAcct('');

    ${'line' . $i}->setRef1('');

    ${'line' . $i}->setRef2('');

    ${'line' . $i}->setExemptionNo('');

    ${'line' . $i}->setCustomerUsageType('');

    $lines[] = ${'line' . $i};

    $i++;

    

    // Calculate the low order fee amount

    $low_order_fee = 0;

    if (MODULE_ORDER_TOTAL_LOWORDERFEE_STATUS == 'true') {

        $low_order_fee = calculateLowOrderFee();    

        

        

        if(!empty($low_order_fee)){

            

            ${'line' . $i} = new Line();

            ${'line' . $i}->setNo($i);

            ${'line' . $i}->setItemCode('LowOrderFee');

            ${'line' . $i}->setDescription('low order fee');

            ${'line' . $i}->setTaxCode('');

            ${'line' . $i}->setQty(1);

            ${'line' . $i}->setAmount($low_order_fee);

            ${'line' . $i}->setDiscounted('false');

            ${'line' . $i}->setRevAcct('');

            ${'line' . $i}->setRef1('');

            ${'line' . $i}->setRef2('');

            ${'line' . $i}->setExemptionNo('');

            ${'line' . $i}->setCustomerUsageType('');

            $lines[] = ${'line' . $i};

            $i++;

                    

        } 

    }

    // Calculate the coupon discount amount

    $coupon_value = 0;

    if ($_SESSION['cc_id']) {

        $coupon_value = getCouponAmount($order,(int) $_SESSION['cc_id']);

    }

    

    // Calculate the membership discount amount

    $membership_amount = 0;

    if(MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS == 'true'){

        $membership_amount = calculateMembershipDiscount();   

    }

    

    if ($coupon_value != 0 || $membership_amount != 0) {

        ${'line' . $i} = new Line();

        ${'line' . $i}->setNo($i);

        ${'line' . $i}->setItemCode('Coupon');

        ${'line' . $i}->setDescription('Coupon Discount');

        ${'line' . $i}->setTaxCode('OD010000');

        ${'line' . $i}->setQty(1);

        ${'line' . $i}->setAmount((($membership_amount + $coupon_value) * -1));

        ${'line' . $i}->setDiscounted('false');

        ${'line' . $i}->setRevAcct('');

        ${'line' . $i}->setRef1('');

        ${'line' . $i}->setRef2('');

        ${'line' . $i}->setExemptionNo("");

        ${'line' . $i}->setCustomerUsageType('');

        $lines[] = ${'line' . $i};

    }

    

    $request->setLines($lines);


    // Try AvaTax

	try {

        $getTaxResult = $client->getTax($request);
		
		
		
		
		
		// code to commit #start
        $doc_type      = $getTaxResult->getDocType(); 
        $doc_code      = $getTaxResult->getDocCode(); 
        $doc_date      = $getTaxResult->getDocDate();
        $doc_amount    = $getTaxResult->getTotalAmount();
        $doc_total_tax = $getTaxResult->getTotalTax();
        $doc_hash_code = $getTaxResult->getHashCode();
        
        
        $_SESSION['avalara_data']['doc_type']       = $doc_type; 
        $_SESSION['avalara_data']['doc_code']       = $doc_code;
        $_SESSION['avalara_data']['doc_date']       = $doc_date;
        $_SESSION['avalara_data']['doc_amount']     = $doc_amount;
        $_SESSION['avalara_data']['doc_total_tax']  = $doc_total_tax;
        $_SESSION['avalara_data']['doc_hash_code']  = $doc_hash_code;
        
        /*
        $client3 = new TaxServiceSoap(MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS);
        $post_tax_request = new PostTaxRequest();
        $post_tax_request->setDocType($doc_type); 
        $post_tax_request->setDocCode($doc_code);
        $post_tax_request->setCompanyCode(MODULE_ORDER_TOTAL_AVATAX_CODE);
        $post_tax_request->setDocDate($doc_date);
        $post_tax_request->setTotalAmount($doc_amount);
        $post_tax_request->setTotalTax($doc_total_tax);
        $post_tax_request->setHashCode($doc_hash_code);
        $post_tax_request->setCommit(true);
        $post_tax_response = $client3->postTax($post_tax_request);
        */ 
        
        // code to commit #ends
        
        if ($getTaxResult->getResultCode() == SeverityLevel::$Success) {

            $tax_data = array(

                'tax_amount' => $getTaxResult->getTotalTax(),

                'taxable_amount' => $getTaxResult->getTotalTaxable(),

                'total_amount' => $getTaxResult->getTotalAmount(),

            );

        } else {

            foreach ($getTaxResult->getMessages() as $msg) {

                //$messageStack->add('header', 'AvaTax error: ' . $msg->getName() . ": " . $msg->getSummary() . '', 'error');

            }

            return FALSE;

        }

    } catch (SoapFault $exception) {

        $msg = 'SOAP Exception: ';

        if ($exception) {

            $msg .= $exception->faultstring;

        }

        //$messageStack->add('header', 'AvaTax message is: ' . $msg . '.', 'error');

        //$messageStack->add('header', 'AvaTax last request is: ' . $client->__getLastRequest() . '.', 'error');

        //$messageStack->add('header', 'AvaTax last response is: ' . $client->__getLastResponse() . '.', 'error');
		
		 // add to log table
		

        return FALSE;

    }
	
	updateAvataxLogTable($client->__getLastRequest(),$client->__getLastResponse(),'tax_calculation');

    return $tax_data;

}



function getCouponAmount($order,$coupon_code) {

        global $ot_coupon, $currency, $currencies;

        $coupon_amount = 0;

        $coupon = tep_db_query("select * from " . TABLE_COUPONS . " where coupon_id = '" . $coupon_code . "'");

        $coupon_result = tep_db_fetch_array($coupon);

        if ($coupon_result['coupon_type'] != 'G') {



            $date_query = tep_db_query("select coupon_start_date from " . TABLE_COUPONS . " where coupon_start_date <= now() and coupon_id = '" . $coupon_code . "'");



            if (tep_db_num_rows($date_query) == 0) {

                return 0;

            }



            $date_query = tep_db_query("select coupon_expire_date from " . TABLE_COUPONS . " where coupon_expire_date >= now() and coupon_id = '" . $coupon_code . "'");



            if (tep_db_num_rows($date_query) == 0) {

                return 0;

            }



            $coupon_count = tep_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon_code . "'");



            $coupon_count_customer = tep_db_query("select coupon_id from " . TABLE_COUPON_REDEEM_TRACK . " where coupon_id = '" . $coupon_code . "' and customer_id = '" . $_SESSION['customer_id'] . "'");



            if (tep_db_num_rows($coupon_count) >= $coupon_result['uses_per_coupon'] && $coupon_result['uses_per_coupon'] > 0) {

                return 0;  

            }



            if (tep_db_num_rows($coupon_count_customer) >= $coupon_result['uses_per_user'] && $coupon_result['uses_per_user'] > 0) {

                return 0;

            }



            $coupon_amount = tep_round($ot_coupon->pre_confirmation_check($order->info['subtotal']), $currencies->currencies[$currency]['decimal_places']);



            return $coupon_amount;

        }

}



function calculateMembershipDiscount() {

    

    global  $order, $cart;

    $amount_order = $order->info['total'];    

    

    if (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_INC_TAX == 'false') {

      $amount_order = $amount_order - $order->info['tax'];   

    }

    

    if (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_INC_SHIPPING == 'false'){

      $amount_order = $amount_order - $order->info['shipping_cost'];  

    } 

    

    

    $od_amount=0;

    $discount = false;

    if (tep_session_is_registered('member_discount')) {  

     	$discount = true;

    } else {

    	$products = $cart->get_products();

        for ($i=0, $n=sizeof($products); $i<$n; $i++) {	

        	if ($products[$i]['id'] == MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT) {

    			$discount = true;

    			break;

    		}

    	} 

    }

 

    if ($discount) {	

        $od_pc = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT;

    	$od_amount = ($amount_order * ($od_pc/100));

    }

    

    return round($od_amount, 2);

}



function calculateLowOrderFee(){

    global $order, $currencies;

    $low_order_fee = 0;

    switch (MODULE_ORDER_TOTAL_LOWORDERFEE_DESTINATION) {

      case 'national':

        if ($order->delivery['country_id'] == STORE_COUNTRY) $pass = true; break;

      case 'international':

        if ($order->delivery['country_id'] != STORE_COUNTRY) $pass = true; break;

      case 'both':

        $pass = true; break;

      default:

        $pass = false; break;

    }



    if ( ($pass == true) && ( ($order->info['total'] - $order->info['shipping_cost']) < MODULE_ORDER_TOTAL_LOWORDERFEE_ORDER_UNDER) ) {

      $tax = tep_get_tax_rate(MODULE_ORDER_TOTAL_LOWORDERFEE_TAX_CLASS, $order->delivery['country']['id'], $order->delivery['zone_id']);

      $low_order_fee = MODULE_ORDER_TOTAL_LOWORDERFEE_FEE + tep_calculate_tax(MODULE_ORDER_TOTAL_LOWORDERFEE_FEE, $tax);

        

    }

    return $low_order_fee;

    

}	