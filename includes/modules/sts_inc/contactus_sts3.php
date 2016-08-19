<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
include (DIR_WS_MODULES.'sts_inc/contact_us.php');	// Get product info variables
$sts->template= array_merge($sts->template,$template_contactus); // Insert product info variables into global array
$sts->template['sysmsgs']= $messageStack->output('header');
?>