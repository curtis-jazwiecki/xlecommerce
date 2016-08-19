<?php
/*
  $Id: mzmt.php,v 1.000 2004-10-29 Josh Dechant Exp $

  Copyright (c) 2004 Josh Dechant

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*//*
  Create text & icons for geo zones and their tables following template below where
    $n = geo zone number (in the shipping module) and
    $j = table number

  MODULE_SHIPPING_MZMT_GEOZONE_$n_TEXT_TITLE
  MODULE_SHIPPING_MZMT_GEOZONE_$n_ICON
  MODULE_SHIPPING_MZMT_GEOZONE_$n_TABLE_$j_TEXT_WAY

  Sample is setup for a 3x3 table (3 Geo Zones with 3 Tables each)
*/

define('MODULE_SHIPPING_MZMT_TEXT_TITLE', 'MultiGeoZone MultiTable');
define('MODULE_SHIPPING_MZMT_TEXT_DESCRIPTION', 'Multiple geo zone shipping with multiple tables to each geo zone.');

define('MODULE_SHIPPING_MZMT_GEOZONE_1_TEXT_TITLE', 'Shipping Fees (United States)');
define('MODULE_SHIPPING_MZMT_GEOZONE_1_ICON', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_1_TABLE_1_TEXT_WAY', 'Standard');
define('MODULE_SHIPPING_MZMT_GEOZONE_1_TABLE_2_TEXT_WAY', 'Express');
define('MODULE_SHIPPING_MZMT_GEOZONE_1_TABLE_3_TEXT_WAY', 'Overnight');

define('MODULE_SHIPPING_MZMT_GEOZONE_2_TEXT_TITLE', 'Shipping Fees (Canada)');
define('MODULE_SHIPPING_MZMT_GEOZONE_2_ICON', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_2_TABLE_1_TEXT_WAY', 'Standard');
define('MODULE_SHIPPING_MZMT_GEOZONE_2_TABLE_2_TEXT_WAY', 'Express');
define('MODULE_SHIPPING_MZMT_GEOZONE_2_TABLE_3_TEXT_WAY', '');

define('MODULE_SHIPPING_MZMT_GEOZONE_3_TEXT_TITLE', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_3_ICON', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_3_TABLE_1_TEXT_WAY', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_3_TABLE_2_TEXT_WAY', '');
define('MODULE_SHIPPING_MZMT_GEOZONE_3_TABLE_3_TEXT_WAY', '');
define('TEXT_SHIPPING_DAYS', 'Your products will arrive at their destination within 1-6 working days (Standard 3-6 days, Express 2-3 days, Overnight 1 day).');

?>
