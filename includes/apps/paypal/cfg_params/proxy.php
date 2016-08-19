<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class OSCOM_PayPal_Cfg_proxy {
    var $default = '';
    var $title;
    var $description;
    var $sort_order = 400;

    function OSCOM_PayPal_Cfg_proxy() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_proxy_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_proxy_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('proxy', OSCOM_APP_PAYPAL_PROXY, 'id="inputProxy"');

      $result = <<<EOT
<div>
  <p>
    <label for="inputProxy">{$this->title}</label>

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
