<?php
/*
  $Id: merchantpartners_ck.php,v 1.1.1.1 2005/05/31 06:59:57 lane Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
----- support@fastcharge.com ------

	Updated by: IFD OSC-MODSQUAD.COM
*/

  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_TITLE', 'Electronic Check (Fast Charge)');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CUSTOMER_TITLE', 'Electronic Check (Instant Debit)');	//IFD OSC-MODSQUAD.COM
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_DESCRIPTION', 'Electronic Check Number:<br><br>ACCT#: 999999999<br>ABA: 999999999');
  define('CKMERCHANTPARTNERS_ERROR_HEADING', 'There has been an error processing your Electronic Check');
  define('CKMERCHANTPARTNERS_ERROR_MESSAGE', 'Please check your Check details!');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_OWNER', 'Name on Check:');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_NUMBER', 'Account Number:');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_CHECK_ABA', 'Routing Number (ABA):');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_JS_CK_NUMBER', '* The check number must be at least ' . CK_NUMBER_MIN_LENGTH . ' characters.\n');
  define('MODULE_PAYMENT_CKMERCHANTPARTNERS_TEXT_SAMPLE_CHECK', 'Where to find your <br> Routing & Account #s');	//IFD OSC-MODSQUAD.COM
?>