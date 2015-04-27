<?php
require('includes/application_top.php');
$sts->template['link'] = tep_href_link('information.php', 'info_id=' . $_GET['info_id']);
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>