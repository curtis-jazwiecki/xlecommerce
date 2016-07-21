<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  $OSCOM_PayPal->install($current_module);

  $OSCOM_PayPal->addAlert($OSCOM_PayPal->getDef('alert_module_install_success'), 'success');

  tep_redirect(tep_href_link('paypal.php', 'action=configure&module=' . $current_module));
?>
