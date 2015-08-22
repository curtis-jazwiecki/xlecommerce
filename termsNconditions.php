<?php

require('includes/application_top.php');

require_once(DIR_WS_FUNCTIONS . 'information.php');

$information_id = 84;

$information_query = tep_db_query("select information_title, information_description from " . TABLE_INFORMATION . " where language_id = '" . (int)$languages_id . "' and information_title like '%Terms%' and information_title like '%conditions%'");

$info = tep_db_fetch_array($information_query);

ob_start();

echo $info['information_description'];

$terms = ob_get_contents();

ob_end_clean;

$sts->template['terms'] = $terms;

require(DIR_WS_INCLUDES . 'application_bottom.php');

?>