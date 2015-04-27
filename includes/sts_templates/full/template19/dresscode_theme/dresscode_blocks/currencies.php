<?php
  if (isset($currencies) && is_object($currencies)) {
    reset($currencies->currencies);
    while (list($key, $value) = each($currencies->currencies)) {
       $currency_string .= '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('language', 'currency')) . 'currency=' . $key, $request_type) . '">' .$key. '</a>';
    }
    echo    $currency_string;
  }

?>





