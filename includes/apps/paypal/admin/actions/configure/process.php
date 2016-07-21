<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  if ( $current_module == 'G' ) {
    $cut = 'OSCOM_APP_PAYPAL_';
  } else {
    $cut = 'OSCOM_APP_PAYPAL_' . $current_module . '_';
  }

  $cut_length = strlen($cut);

  foreach ( $OSCOM_PayPal->getParameters($current_module) as $key ) {
    $p = strtolower(substr($key, $cut_length));

    if ( isset($HTTP_POST_VARS[$p]) ) {
      $OSCOM_PayPal->saveParameter($key, $HTTP_POST_VARS[$p]);
    }
  }

  $OSCOM_PayPal->addAlert($OSCOM_PayPal->getDef('alert_cfg_saved_success'), 'success');

  tep_redirect(tep_href_link('paypal.php', 'action=configure&module=' . $current_module));
?>
