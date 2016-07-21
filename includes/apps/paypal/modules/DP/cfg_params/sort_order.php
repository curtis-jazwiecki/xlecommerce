<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class OSCOM_PayPal_DP_Cfg_sort_order {
    var $default = '0';
    var $title;
    var $description;
    var $app_configured = false;

    function OSCOM_PayPal_DP_Cfg_sort_order() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_dp_sort_order_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_dp_sort_order_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('sort_order', OSCOM_APP_PAYPAL_DP_SORT_ORDER, 'id="inputDpSortOrder"');

      $result = <<<EOT
<div>
  <p>
    <label for="inputDpSortOrder">{$this->title}</label>

    {$this->description}
  </p>

  <div>
    {$input}
  </div>
</div>
EOT;

      return $result;
    }
  }
?>
