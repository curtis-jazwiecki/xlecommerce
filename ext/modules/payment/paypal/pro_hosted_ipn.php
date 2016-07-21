<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  chdir('../../../../');
  require('includes/application_top.php');

  if ( !defined('OSCOM_APP_PAYPAL_HS_STATUS') || !in_array(OSCOM_APP_PAYPAL_HS_STATUS, array('1', '0')) ) {
    exit;
  }

  require(DIR_WS_LANGUAGES . $language . '/modules/payment/paypal_pro_hs.php');
  require('includes/modules/payment/paypal_pro_hs.php');

  $result = false;

  if ( isset($HTTP_POST_VARS['txn_id']) && !empty($HTTP_POST_VARS['txn_id']) ) {
    $paypal_pro_hs = new paypal_pro_hs();

    $result = $paypal_pro_hs->_app->getApiResult('APP', 'GetTransactionDetails', array('TRANSACTIONID' => $HTTP_POST_VARS['txn_id']), (OSCOM_APP_PAYPAL_HS_STATUS == '1') ? 'live' : 'sandbox', true);
  }

  if ( is_array($result) && isset($result['ACK']) && (($result['ACK'] == 'Success') || ($result['ACK'] == 'SuccessWithWarning')) ) {
    $pphs_result = $result;

    $paypal_pro_hs->verifyTransaction(true);
  }

  require('includes/application_bottom.php');
?>
