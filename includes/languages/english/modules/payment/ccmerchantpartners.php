<?php
/*
  $Id: merchantpartners_cc.php,v 1.2 2005/06/15 18:43:57 lane Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

----- support@fastcharge.com ------

	Updated by: IFD OSC-MODSQUAD.COM
*/

  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_TITLE', 'Credit Card (Fast Charge)');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CUSTOMER_TITLE', 'Visa, MasterCard, American Express or Discover');	//IFD OSC-MODSQUAD.COM
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_DESCRIPTION', 'Credit Card Test Info:<br><br>CC#: 5454545454545454<br>Expiry: Any<br>CVV2/CID: any');
  define('MERCHANTPARTNERS_ERROR_HEADING', 'There has been an error processing your credit card');
  define('MERCHANTPARTNERS_ERROR_MESSAGE', 'Please check your credit card details!');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_CVV2', 'CVV2/CCV2/CID number:');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_CVV2_LOCATION', '(3 or 4 digit # on card)');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_CREDIT_CARD_CVV2_EXPLAIN', 'What is this?');		//IFD OSC-MODSQUAD.COM
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_MERCHANTPARTNERS_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
?>