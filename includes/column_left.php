<?php
/*
  $Id: column_left.php,v 1.15 2003/07/01 14:34:54 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

// START STS 4.1

if ($sts->display_template_output) {
  include DIR_WS_MODULES.'sts_inc/sts_column_left.php';
} else {
//END STS 4.1
  if ((USE_CACHE == 'true') && empty($SID)) {
    echo tep_cache_categories_box();
  } else {
    include(DIR_WS_BOXES . 'categories.php');
  }

  if ((USE_CACHE == 'true') && empty($SID)) {
    echo tep_cache_manufacturers_box();
  } else {
    include(DIR_WS_BOXES . 'manufacturers.php');
  }

  require(DIR_WS_BOXES . 'whats_new.php');
  require(DIR_WS_BOXES . 'search.php');
  require(DIR_WS_BOXES . 'information.php');
  require(DIR_WS_BOXES . 'featured.php');
// START STS 4.1
}
// END STS 4.1
?>