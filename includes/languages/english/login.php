<?php
/*
  $Id: login.php,v 1.14 2003/06/09 22:46:46 hpdl Exp $

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

define('NAVBAR_TITLE', 'Login');
define('TEXT_GUEST_INTRODUCTION', 'To continue checkout without creating an account or logging in, click below.');
// PWA BOF
define('TEXT_GUEST_INTRODUCTION', '<b>Do you want to go straight to the checkout process?</b><br><br>Would like like to check out without creating a customer account? Please note that all of our services will not be available to customers that do not wish to create an account. Also, you cannot view the status of your order, and each time you shop with us you will have to re-enter all of your data.<br><br>Creating an account is free. If you still wish to continue to checkout please click the checkout button to your right.');
// PWA BOF
define('HEADING_TITLE', 'Welcome, Please Sign In');
define('HEADING_GUEST_CUSTOMER', 'Guest Checkout');

define('HEADING_NEW_CUSTOMER', 'New Customer');
define('TEXT_NEW_CUSTOMER', 'I am a new customer.');
define('TEXT_NEW_CUSTOMER_INTRODUCTION', 'By creating an account at ' . STORE_NAME . ' you will be able to shop faster, be up to date on an orders status, and keep track of the orders you have previously made.');

define('HEADING_RETURNING_CUSTOMER', 'Returning Customer');
define('TEXT_RETURNING_CUSTOMER', 'I am a returning customer.');

define('TEXT_PASSWORD_FORGOTTEN', 'Password forgotten? Click here.');

define('TEXT_LOGIN_ERROR', 'Error: No match for E-Mail Address and/or Password.');
define('TEXT_VISITORS_CART', '<font color="#ff0000"><b>Note:</b></font> Your &quot;Visitors Cart&quot; contents will be merged with your &quot;Members Cart&quot; contents once you have logged on. <a href="javascript:session_win();">[More Info]</a>');


?>
