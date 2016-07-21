<?php
/*
  $Id$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class OSCOM_PayPal_PS_Cfg_ewp_working_directory {
    var $default = '';
    var $title;
    var $description;
    var $sort_order = 1200;

    function OSCOM_PayPal_PS_Cfg_ewp_working_directory() {
      global $OSCOM_PayPal;

      $this->title = $OSCOM_PayPal->getDef('cfg_ps_ewp_working_directory_title');
      $this->description = $OSCOM_PayPal->getDef('cfg_ps_ewp_working_directory_desc');
    }

    function getSetField() {
      $input = tep_draw_input_field('ewp_working_directory', OSCOM_APP_PAYPAL_PS_EWP_WORKING_DIRECTORY, 'id="inputPsEwpWorkingDirectory"');

      $result = <<<EOT
<div>
  <p>
    <label for="inputPsEwpWorkingDirectory">{$this->title}</label>

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
