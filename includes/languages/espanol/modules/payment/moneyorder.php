<?php
/*
  $Id: moneyorder.php,v 1.7 2003/01/24 21:36:05 thomasamoulton Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  define('MODULE_PAYMENT_MONEYORDER_TEXT_TITLE', 'Cheque/Transferencia Bancaria');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_DESCRIPTION', 'Pagadero a:&nbsp;' . MODULE_PAYMENT_MONEYORDER_PAYTO . '<br><br>Enviar a<br>' . nl2br(STORE_NAME_ADDRESS) . '<br><br>' . '&nbsp;Su pedido se enviar� en cuanto se reciba el pago.');
  define('MODULE_PAYMENT_MONEYORDER_TEXT_EMAIL_FOOTER', "Pagadero a: ". MODULE_PAYMENT_MONEYORDER_PAYTO . "\n\nEnviar a\n" . STORE_NAME_ADDRESS . "\n\n" . 'Su pedido se enviar� en cuanto se reciba el pago.');
?>
