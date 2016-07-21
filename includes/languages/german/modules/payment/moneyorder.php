<?php
/*
  $Id: moneyorder.php,v 1.9 2003/07/11 09:04:23 jan0815 Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', 'Scheck/Vorkasse');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'Zahlbar an:&nbsp;' . MODULE_PAYMENT_MONEYORDER_PAYTO . '<br>Adressat:<br><br>' . nl2br(STORE_NAME_ADDRESS) . '<br><br>' . 'Ihre Bestellung wird nicht versandt, bis wir das Geld erhalten haben!');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', "Zahlbar an: ". MODULE_PAYMENT_MONEYORDER_PAYTO . "\n\nAdressat:\n" . STORE_NAME_ADDRESS . "\n\n" . 'Ihre Bestellung wir nicht versandt, bis wird das Geld erhalten haben!');
?>
