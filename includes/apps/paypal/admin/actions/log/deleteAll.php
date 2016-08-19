<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  tep_db_query('delete from oscom_app_paypal_log');

  $OSCOM_PayPal->addAlert($OSCOM_PayPal->getDef('alert_delete_success'), 'success');

  tep_redirect(tep_href_link('paypal.php', 'action=log'));
?>
