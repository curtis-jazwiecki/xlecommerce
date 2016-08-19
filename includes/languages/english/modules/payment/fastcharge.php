<?php
/*
  $Id: authorizenet.php 08/03/2006 23:51:00 Rhea Anthony Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

// Admin Configuration Items

  define('MODULE_PAYMENT_FASTCHARGE_TEXT_ADMIN_TITLE', 'Fast Charge'); // Payment option title as displayed in the admin
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_DESCRIPTION', '<b>Automatic Approval Credit Card Numbers:</b><br />MC#: 5454545454545454<br />Exp date: “1/12/2019"<br/>CVV/CVV2: “193"<br/><br /><b>Note:</b> The credit card numbers above will return a decline in Live mode, and an
approval in Test mode.  Any future date can be used for the expiry date and any 3 digit number can be used for the CVV Code (4 digit number for AMEX)<br /><br /><b>Automatic Decline Credit Card Number:</b><br /><br />Card #: 4111111111111111<br />Exp Date: “3/11/2018"<br />CVV/CVV2: “123"<br/><br />Use the number above to test declined cards.<br /><br />');

  // Catalog Items

  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CATALOG_TITLE', 'Fast Charge');  // Payment option title as displayed to the customer
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CREDIT_CARD_TYPE', 'Credit Card Type:');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
  //define('MODULE_PAYMENT_FASTCHARGE_TEXT_CVV', 'CVV Number <a href="javascript:newwindow()"><u>More Info</u></a>');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_CVV', 'CVV Number <a onClick="javascript:window.open(\'cvv_help.php\',\'jav\',\'width=500,height=550,resizable=no,toolbar=no,menubar=no,status=no\');"><u>More Info</u></a>');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_JS_CC_CVV', '* The 3 or 4 digit CVV number must be entered from the back of the credit card.\n');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_DECLINED_MESSAGE', 'Your credit card could not be authorized for this reason. Please correct any information and try again or contact us for further assistance.');
  define('MODULE_PAYMENT_FASTCHARGE_TEXT_ERROR', 'Credit Card Error!');
?>