<?php
  require('includes/application_top.php');
  require(DIR_WS_LANGUAGES . $language .
  '/modules/payment/eProcessingNetwork.php');

  tep_redirect(tep_href_link(
    FILENAME_CHECKOUT_PAYMENT,
    'error_message=' . 
    urlencode(
    MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_ERROR_MESSAGE),
    'SSL', true, false));

