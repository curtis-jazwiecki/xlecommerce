<?php
/*
  $Id: ckmerchantpartners.php,v 1.65 2007/02/06 05:51:31 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
----- Tony Cun tcun@merchantpartners.com ------
*/

  class ckmerchantpartners {
    var $code, $title, $description, $enabled;

// class constructor
    function ckmerchantpartners() {
      global $order;

      $this->code = 'ckmerchantpartners';
      $this->title = MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_TITLE;
      $this->description = MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_PAYMENT_CKMERCHANTPARTNERS_SORT_ORDER;
      $this->enabled = ((MODULE_PAYMENT_CKMERCHANTPARTNERS_STATUS == 'True') ? true : false);

      if ((int)MODULE_PAYMENT_CKMERCHANTPARTNERS_ORDER_STATUS_ID > 0) {
        $this->order_status = MODULE_PAYMENT_CKMERCHANTPARTNERS_ORDER_STATUS_ID;
      }

      if (is_object($order)) $this->update_status();

      $this->form_action_url = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', true);

    }

// class methods
    function update_status() {
      global $order;

      if ( ($this->enabled == true) && ((int)MODULE_PAYMENT_CKMERCHANTPARTNERS_ZONE > 0) ) {
        $check_flag = false;
        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_PAYMENT_CKMERCHANTPARTNERS_ZONE . "' and zone_country_id = '" . $order->billing['country']['id'] . "' order by zone_id");
        while ($check = tep_db_fetch_array($check_query)) {
          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_id'] == $order->billing['zone_id']) {
            $check_flag = true;
            break;
          }
        }

        if ($check_flag == false) {
          $this->enabled = false;
        }
      }
    }

    function javascript_validation() {
      $js = '  if (payment_value == "' . $this->code . '") {' . "\n" .
            '    var ck_owner = document.checkout_payment.ckmerchantpartners_ck_owner.value;' . "\n" .
            '    var ck_number = document.checkout_payment.ckmerchantpartners_ck_number.value;' . "\n" .
            '    if (ck_owner == "") { ' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_JS_CK_OWNER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '    if (ck_number == "") {' . "\n" .
            '      error_message = error_message + "' . MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_JS_CK_NUMBER . '";' . "\n" .
            '      error = 1;' . "\n" .
            '    }' . "\n" .
            '  }' . "\n";

      return $js;
    }

    function selection() {
      global $order;

      for ($i=1; $i < 13; $i++) {
        $expires_month[] = array('id' => sprintf('%02d', $i), 'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));
      }

      $today = getdate();
      for ($i=$today['year']; $i < $today['year']+10; $i++) {
        $expires_year[] = array('id' => strftime('%y',mktime(0,0,0,1,1,$i)), 'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));
      }

      $selection = array('id' => $this->code,
                         'module' => $this->title,
                         'fields' => array(array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_OWNER,
                                                 'field' => tep_draw_input_field('ckmerchantpartners_ck_owner', $order->billing['firstname'] . ' ' . $order->billing['lastname'])),
                                           array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_NUMBER,
                                                 'field' => tep_draw_input_field('ckmerchantpartners_ck_number')),
                                           array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_ABA,
                                                 'field' => tep_draw_input_field('ckmerchantpartners_ck_aba'))));

      return $selection;
    }


    function pre_confirmation_check() {
      global $HTTP_POST_VARS;

      include(DIR_WS_CLASSES . 'cc_validation.php');

      $cc_validation = new cc_validation();
      $result = '1';

      $error = '';
      switch ($result) {
        case -1:
          $error = sprintf(TEXT_CCVAL_ERROR_UNKNOWN_CARD, substr($cc_validation->ck_number, 0, 4));
          break;
        case -2:
        case -3:
        case -4:
          $error = TEXT_CCVAL_ERROR_INVALID_DATE;
          break;
        case false:
          $error = TEXT_CCVAL_ERROR_INVALID_NUMBER;
          break;
      }

      if ( ($result == false) || ($result < 1) ) {
        $payment_error_return = 'payment_error=' . $this->code . '&error=' . urlencode($error) . '&ckmerchantpartners_cc_owner=' . urlencode($HTTP_POST_VARS['ckmerchantpartners_cc_owner']) . '&ckmerchantpartners_cc_expires_month=' . $HTTP_POST_VARS['ckmerchantpartners_cc_expires_month'] . '&ckmerchantpartners_cc_expires_year=' . $HTTP_POST_VARS['ckmerchantpartners_cc_expires_year'] . '&ckmerchantpartners_cc_checkcode=' . $HTTP_POST_VARS['ckmerchantpartners_cc_checkcode'];

        tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, $payment_error_return, 'SSL', true, false));
      }

      $this->cc_card_type = 'CK'; // $cc_validation->cc_type;
      $this->ck_number = $HTTP_POST_VARS['ckmerchantpartners_ck_number'];   // $cc_validation->ck_number;
      $this->ck_aba = $HTTP_POST_VARS['ckmerchantpartners_ck_aba']; // $cc_validation->cc_expiry_month;
      // $this->ck_acct = $HTTP_POST_VARS['ckmerchantpartners_ck_acct']; // $cc_validation->cc_expiry_year;
    }

    function confirmation() {
      global $HTTP_POST_VARS;

      $confirmation = array('title' => $this->title . ': ' . $this->cc_card_type,
                            'fields' => array(array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_OWNER,
                            'field' => $HTTP_POST_VARS['ckmerchantpartners_ck_owner']),
                                       array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_NUMBER,
                            'field' => $HTTP_POST_VARS['ckmerchantpartners_ck_number']),
                                       array('title' => MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_ABA,
                                             'field' => $HTTP_POST_VARS['ckmerchantpartners_ck_aba'])));


      return $confirmation;
    }


   function process_button() {
    global $HTTP_POST_VARS, $HTTP_GET_VARS, $order, $customer_id, $session_id, $emailtext, $merchantordernumber;

    $process_button_string =   tep_draw_hidden_field('ckname', $HTTP_POST_VARS['ckmerchantpartners_ck_owner']) .
                               tep_draw_hidden_field('ckacct', $HTTP_POST_VARS['ckmerchantpartners_ck_number']) .
                               tep_draw_hidden_field('ckaba', $HTTP_POST_VARS['ckmerchantpartners_ck_aba']) .
			       tep_draw_hidden_field('emailtext', $emailtext) .
			       tep_draw_hidden_field('merchantordernumber', $merchantordernumber) .
			       tep_draw_hidden_field('ci_memo', $HTTP_POST_VARS['comments']);

      return $process_button_string;
    }

    function before_process() {
    global $HTTP_POST_VARS, $HTTP_GET_VARS, $order, $emailtext, $merchantordernumber, $orderid;

	     $url="https://trans.merchantpartners.com/cgi-bin/process.cgi";

	     $params = array(
	     	action => ns_quicksale_check,
	        acctid => MODULE_PAYMENT_MERCHANTPARTNERS_ACCTID,
	        subid => MODULE_PAYMENT_MERCHANTPARTNERS_SUBID,
	        merchantpin => MODULE_PAYMENT_MERCHANTPARTNERS_MERCHANTPIN,
	        amount => number_format($order->info['total'], 2, '.', ''),
	        ci_email => $order->customer['email_address'],
			ci_billaddr1 => $order->billing['street_address'],
			ci_billaddr2 => $order->billing['suburb'],
			ci_billcity => $order->billing['city'],
			ci_billstate => $order->billing['state'],
			ci_billzip => $order->billing['postcode'],
			ci_billcountry => $order->billing['country']['title'],
			ci_consumername => $order->delivery['firstname'] . ' ' . $order->delivery['lastname'],
			ci_shipaddr1 => $order->delivery['street_address'],
			ci_shipaddr2 => $order->delivery['suburb'],
			ci_shipcity => $order->delivery['city'],
			ci_shipstate => $order->delivery['state'],
			ci_shipzip => $order->delivery['postcode'],
	        ci_shipcountry => $order->delivery['country']['title'],
	     merchantordernumber => $HTTP_POST_VARS['merchantordernumber'],
                ci_phone => $order->customer['telephone'],
	    	ccname => $HTTP_POST_VARS['ckname'],
	    	ckacct => $HTTP_POST_VARS['ckacct'],
	    	ckaba => $HTTP_POST_VARS['ckaba'],
                ci_ipaddress => $_SERVER['REMOTE_ADDR'],
	    	emailtext => $emailtext);

	     while(list($key, $val) = each($params)) {
	     	$formargs .= $key . '=' . urlencode(preg_replace('/,/', '', $val)) . '&';
	     }

	     $ch=curl_init();
	     curl_setopt($ch, CURLOPT_URL, $url);
	     curl_setopt($ch, CURLOPT_TIMEOUT, 180); // should never take more than 3 mins
	     curl_setopt($ch, CURLOPT_POST, 1);
	     curl_setopt($ch, CURLOPT_POSTFIELDS, $formargs);
	     curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	     $result=curl_exec($ch);
	     $fatal_error=curl_error($ch);
	     curl_close($ch);

	     $result = explode("\n", $result ); // parsing text/html outputs
	     $totalVars = count($result);

	     for ($i=0; $i<$totalVars; $i++) {
	     $resultTempVars = explode("=", rtrim($result[$i]));
	     $resultVars[$resultTempVars[0]] = $resultTempVars[1];
	     }

	     if ($resultVars[Status] != "Accepted") {
	     tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'payment_error=' . $this->code . '&error=' . $resultVars['Declined'] . $fatal_error, 'SSL', true));
	     }
	     $orderid = $resultVars['orderid'];
     return $orderid;
    }



    function after_process() {
      return false;
    }

    function get_error() {
      global $HTTP_GET_VARS, $HTTP_POST_VARS;

      if (isset($HTTP_GET_VARS['Status'])) {
         $error = array('title' => CKMERCHANTPARTNERS_ERROR_HEADING,
                     'error' => stripslashes(urldecode($HTTP_GET_VARS['Status'])) . ': ' .  stripslashes(urldecode($HTTP_GET_VARS['reason'])) . stripslashes(urldecode($HTTP_GET_VARS['Reason'])));
      }
      else {
      $error = array('title' => CKMERCHANTPARTNERS_ERROR_HEADING,
                     'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));
      }
      return $error;
    }

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_CKMERCHANTPARTNERS_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Merchant Partners Check Module', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_STATUS', 'True', 'Do you want to accept Merchant Partners payments?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('AcctID', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ACCTID', 'TEST0', 'The acctID used for the merchantPartners service', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('SubID', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_SUBID', '', 'The subID for the merchantPartners service', '6', '3', now())");
       tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Merchant PIN', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_MERCHANTPIN', '', 'The MerchantPIN for the service', '6', '3', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Payment Zone', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ZONE', '0', 'If a zone is selected, only enable this payment method for that zone.', '6', '2', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort order of display.', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_SORT_ORDER', '0', 'Sort order of display. Lowest is displayed first.', '6', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ORDER_STATUS_ID', '0', 'Set the status of orders made with this payment module to this value', '6', '0', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

    function keys() {
      return array('MODULE_PAYMENT_CKMERCHANTPARTNERS_STATUS', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ACCTID', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_SUBID', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_MERCHANTPIN', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ZONE', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_ORDER_STATUS_ID', 'MODULE_PAYMENT_CKMERCHANTPARTNERS_SORT_ORDER');
    }
  }
?>
