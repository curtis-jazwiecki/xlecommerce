<?php
/*
  $Id$

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  function OSCOM_PayPal_LOGIN_Api_UserInfo($OSCOM_PayPal, $server, $extra_params) {
    if ( $server == 'live' ) {
      $api_url = 'https://api.paypal.com/v1/identity/openidconnect/userinfo/?schema=openid&access_token=' . $extra_params['access_token'];
    } else {
      $api_url = 'https://api.sandbox.paypal.com/v1/identity/openidconnect/userinfo/?schema=openid&access_token=' . $extra_params['access_token'];
    }

    $response = $OSCOM_PayPal->makeApiCall($api_url);
    $response_array = json_decode($response, true);

    return array('res' => $response_array,
                 'success' => (is_array($response_array) && !isset($response_array['error'])),
                 'req' => $params);
  }
?>
