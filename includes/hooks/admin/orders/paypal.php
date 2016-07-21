<?php
/*
  $Id$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class hook_admin_orders_paypal {
    function listen_orderAction() {
      if ( !class_exists('paypal_hook_admin_orders_action') ) {
        include(DIR_FS_CATALOG . 'includes/apps/paypal/hooks/admin/orders/action.php');
      }

      $hook = new paypal_hook_admin_orders_action();

      return $hook->execute();
    }

    function listen_orderTab() {
      if ( !class_exists('paypal_hook_admin_orders_tab') ) {
        include(DIR_FS_CATALOG . 'includes/apps/paypal/hooks/admin/orders/tab.php');
      }

      $hook = new paypal_hook_admin_orders_tab();

      return $hook->execute();
    }
  }
?>
