<?php
/*
  $Id: pinfo_sts3.php,v 4.1 2005/11/04 23:55:58 rigadin Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

Include Module for STS v4.1 by Rigadin (rigadin@osc-help.net)
This module is useful for compatibility with product_info templates made with STS v2 or v3.
*/

include (DIR_WS_MODULES.'sts_inc/product_info.php');	// Get product info variables
$sts->template= array_merge($sts->template,$template_pinfo ); // Insert product info variables into global array
$sts->template['sysmsgs']= $messageStack->output('header');
?>