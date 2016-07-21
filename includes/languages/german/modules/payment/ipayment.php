<?php
/*
  $Id: ipayment.php,v 1.7 2003/07/11 09:04:23 jan0815 Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  define('MODULE_PAYMENT_IPAYMENT_TEXT_TITLE', 'iPayment');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_DESCRIPTION', 'Kreditkarten Test Info:<br><br>CC#: 4111111111111111<br>G&uuml;ltig bis: Any');
  define('IPAYMENT_ERROR_HEADING', 'Folgender Fehler wurde von iPayment w&auml;hrend des Prozesses gemeldet:');
  define('IPAYMENT_ERROR_MESSAGE', 'Bitte kontrollieren Sie die Daten Ihrer Kreditkarte!');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_OWNER', 'Kreditkarteninhaber');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_NUMBER', 'Kreditkarten-Nr.:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_EXPIRES', 'G&uuml;ltig bis:');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER', 'Karten-Pr&uuml;fnummer');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_CREDIT_CARD_CHECKNUMBER_LOCATION', '(Auf der Kartenr&uuml;ckseite im Unterschriftsfeld)');

  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_OWNER', '* Der Name des Kreditkarteninhabers mss mindestens aus  ' . CC_OWNER_MIN_LENGTH . ' Zeichen bestehen.\n');
  define('MODULE_PAYMENT_IPAYMENT_TEXT_JS_CC_NUMBER', '* Die \'Kreditkarten-Nr.\' muss mindestens aus ' . CC_NUMBER_MIN_LENGTH . ' Zahlen bestehen.\n');
?>
