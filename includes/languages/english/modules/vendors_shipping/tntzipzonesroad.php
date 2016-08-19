<?php
/*
  $Id: zones.php,v 1.2 2001/10/15 07:07:45 mabosch Exp $
  The Exchange Project - Community Made Shopping!
  http://www.theexchangeproject.org
  Copyright (c) 2000,2001 The Exchange Project
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
define('MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_TITLE', 'TNT Road Express');
define('MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_DESCRIPTION', '<IMG alt="TNT Road Express " hspace=0 src="images/icons/TNTsm.jpg" align=baseline border=0><br>TNT Postcode Based Road Rates:');  // I have embedded my graphic here in the language file to suit my store but the standard method is to use the ICON call in includes/modules/vendor_shipping/tntzipzonesroad.php. Simply specify the directory and filename where you want the image pulled from. By embedding the image here in the language file it presents the icon in the users order history, on invoices etc , and also in the admin section where the module is installed from (dont forget to put the image in your admin/images directory for it to show here). If not required here simply delete  the <IMG ..........<br> portion and the standard title will show..
define('MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_WAY', 'Shipping to');
define('MODULE_SHIPPING_TNTZIPZONESROAD_TEXT_UNITS', 'kg(s)');
define('MODULE_SHIPPING_TNTZIPZONESROAD_INVALID_ZONE', 'No shipping available to the selected Post Code');
define('MODULE_SHIPPING_TNTZIPZONESROAD_NO_ZIPCODE_FOUND', 'No Post Code available for the customer data');
define('MODULE_SHIPPING_TNTZIPZONESROAD_NO_ZONE_FOUND', 'Could not find a zone for the given Post Code');
define('MODULE_SHIPPING_TNTZIPZONESROAD_UNDEFINED_RATE', 'The shipping rate cannot be determined at this time');
?>