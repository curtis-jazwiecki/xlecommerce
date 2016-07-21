<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  $ppGetNewsResult = array('rpcStatus' => -1);

  if ( function_exists('json_encode') ) {
    $ppGetNewsResponse = @json_decode($OSCOM_PayPal->makeApiCall('http://www.oscommerce.com/index.php?RPC&Website&Index&GetPartnerBanner&forumid=105&onlyjson=true'), true);

    if ( is_array($ppGetNewsResponse) && isset($ppGetNewsResponse['title']) ) {
      $ppGetNewsResult = $ppGetNewsResponse;

      $ppGetNewsResult['rpcStatus'] = 1;
    }

    echo json_encode($ppGetNewsResult);
  }

  exit;
?>
