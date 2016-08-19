<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  $ppUpdateLogResult = array('rpcStatus' => -1);

  if ( isset($HTTP_GET_VARS['v']) && is_numeric($HTTP_GET_VARS['v']) && file_exists(DIR_FS_CATALOG . 'includes/apps/paypal/work/update_log-' . basename($HTTP_GET_VARS['v']) . '.php') ) {
    $ppUpdateLogResult['rpcStatus'] = 1;
    $ppUpdateLogResult['log'] = file_get_contents(DIR_FS_CATALOG . 'includes/apps/paypal/work/update_log-' . basename($HTTP_GET_VARS['v']) . '.php');
  }

  echo json_encode($ppUpdateLogResult);

  exit;
?>
