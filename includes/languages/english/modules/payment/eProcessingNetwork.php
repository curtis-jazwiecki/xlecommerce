<?php
/*
  $Id: eProcessingNetwork.php,v 1.12 2002/11/18 14:45:20 project3000 Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

  eProcessingNetwork.php was developed for eProcessingNetwork

  http://www.eProcessingNetwork.com

  by

  Julian Brown
  julian@jlbprof.com
*/

	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_TITLE', 'eProcessingNetwork');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_DESCRIPTION', 'Credit Card Test Info:<br><br>CC#: 4111111111111111<br>Expiry: Any');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_TYPE', 'Type:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_OWNER', 'Credit Card Owner:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_NUMBER', 'Credit Card Number:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_EXPIRES', 'Credit Card Expiry Date:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_JS_CC_OWNER', '* The owner\'s name of the credit card must be at least ' . CC_OWNER_MIN_LENGTH . ' characters.\n');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_JS_CC_NUMBER', '* The credit card number must be at least ' . CC_NUMBER_MIN_LENGTH . ' characters.\n');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_ERROR_MESSAGE', 'There has been an error processing your credit card. Please try again.');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_ERROR', 'Credit Card Error!');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CARDS_ACCEPTED', 'Credit Cards Accepted:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_0', 'I do not want to utilized the CVV2 for this transaction.');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_1', 'I have entered the CVV2');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_2', 'Cardholder claims cards CVV2 is illegible');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_9', 'Cardholder claims card has no CVV2 imprint');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_TYPE', 'Is CVV2 (Security Code) Present:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2', 'CVV2 (Security Code):');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_HELP', 'What is CVV2 (Security Code)?');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_TYPE_ERROR', '* You have indicated that you have entered the CVV2 value but no value is present\n');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_ERROR_MESSAGE',
	'There has been an error processing your credit card. Please try again.');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_UNABLE_MESSAGE',
	'Our credit card processing is unavailable for some reason.  Please contact our support department');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_DECLINED_MESSAGE',
	'Your credit card was declined. Please try another card or contact your bank for more info.');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_TYPE', '<b>Payment Type:</b>');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_CREDIT', '<b>Credit</b>');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_CHECK',  '<b>Check</b>');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_COMPANYNAME', 'CompanyName:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_FIRSTNAME', 'FirstName:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_LASTNAME',  'LastName:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ADDRESS',   'Address:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CITY',      'City:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_STATE',     'State:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ZIP',       'Zip:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_PHONE',     'Phone:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING',   'Routing #:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT',   'Account #:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK',     'Check #:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME',  'BankName:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNTTYPE', 'AccountType:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNTCLASS', 'AccountClass:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_IMAGE', 'Reference Image:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_HOWTO', 'How to use Checks:');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_HOWTO_TXT', 'Select your Account Type (Personal or Company, Checking or Savings), enter the name of your bank, your banks Routing/Transit Number (location shown below), your Account Number and Check Number (locations also shown below). ');

	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING_ERROR',
		'Routing # is empty or is not 9 digits, or is not valid');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT_ERROR',
		'Checking Account # is empty or is not all digits');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK_ERROR',
		'Check # is empty or is not all digits');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME_ERROR',
		'Bank Name is empty');
	define('MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_BILLING_INFO', 'Billing Info:');
?>
