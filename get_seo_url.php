<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');
$sts->template['link'] = tep_href_link('information.php', 'info_id=' . $_GET['info_id']);
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>