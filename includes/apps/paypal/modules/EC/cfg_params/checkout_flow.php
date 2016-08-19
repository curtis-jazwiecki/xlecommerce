<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class OSCOM_PayPal_EC_Cfg_checkout_flow {
    var $default = '0';
    var $title;
    var $description;
    var $sort_order = 200;

    function OSCOM_PayPal_EC_Cfg_checkout_flow() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ec_checkout_flow_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ec_checkout_flow_desc');
    }

    function getSetField() {
      global $OSCOM_PayPal;

      if ( !file_exists(DIR_FS_CATALOG . 'includes/apps/paypal/with_beta.txt') ) {
        return false;
      }

      $input = '<input type="radio" id="checkoutFlowSelectionDefault" name="checkout_flow" value="0"' . (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '0' ? ' checked="checked"' : '') . '><label for="checkoutFlowSelectionDefault">' . $OSCOM_PayPal->getDef('cfg_ec_checkout_flow_default') . '</label>' .
               '<input type="radio" id="checkoutFlowSelectionInContext" name="checkout_flow" value="1"' . (OSCOM_APP_PAYPAL_EC_CHECKOUT_FLOW == '1' ? ' checked="checked"' : '') . '><label for="checkoutFlowSelectionInContext">' . $OSCOM_PayPal->getDef('cfg_ec_checkout_flow_in_context') . '</label>';

      $result = <<<EOT
<div>
  <p>
    <label>{$this->title}</label>

    {$this->description}
  </p>

  <div id="checkoutFlowSelection">
    {$input}
  </div>
</div>

<script>
$(function() {
  $('#checkoutFlowSelection').buttonset();
});
</script>
EOT;

      return $result;
    }
  }
?>
