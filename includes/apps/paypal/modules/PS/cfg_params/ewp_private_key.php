<?php
/*
  $Id$

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class OSCOM_PayPal_PS_Cfg_ewp_private_key {
    var $default = '';
    var $title;
    var $description;
    var $sort_order = 800;

    function OSCOM_PayPal_PS_Cfg_ewp_private_key() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ps_ewp_private_key_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ps_ewp_private_key_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('ewp_private_key', OSCOM_APP_PAYPAL_PS_EWP_PRIVATE_KEY, 'id="inputPsEwpPrivateKey"');

      $result = <<<EOT
<div>
  <p>
    <label for="inputPsEwpPrivateKey">{$this->title}</label>

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
