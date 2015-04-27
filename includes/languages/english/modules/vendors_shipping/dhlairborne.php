<?php
/*
  $Id: dhlairborne.php,v 1.310 2004-10-29 Josh Dechant Exp $

  Copyright (c) 2003-2004 Joshua Dechant

  Portions Copyright (c) 2002 osCommerce
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  
  Released under the GNU General Public License
*/

define('MODULE_SHIPPING_AIRBORNE_TEXT_TITLE', 'DHL/Airborne');
define('MODULE_SHIPPING_AIRBORNE_TEXT_DESCRIPTION', 'DHL/Airborne Domestic Shipping Rates<br><em>(Using the DHL/Airborne ShipIT XML API)</em><p><a href="javascript:window.open(\'dhlairborne_docs.html\',\'dhlairborne_docs\',\'height=375,width=550,toolbar=no,statusbar=no,scrollbars=yes,screenX=150,screenY=150,top=150,left=150\').focus();"><u>Documentation & Help</u> [?]</a>' . ((MODULE_SHIPPING_AIRBORNE_STATUS == 'True') ? '<p>DHL/Airborne expects the use of American pounds when calculating the rate for a shipment.' : ''));

define('MODULE_SHIPPING_AIRBORNE_ICON', 'shipping_dhlairborne.gif');

define('MODULE_SHIPPING_AIRBORNE_TEXT_GROUND', 'Ground');
define('MODULE_SHIPPING_AIRBORNE_TEXT_SECOND_DAY', 'Second Day Service');
define('MODULE_SHIPPING_AIRBORNE_TEXT_NEXT_AFTERNOON', 'Next Afternoon');
define('MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS', 'Express');
define('MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS_1030', 'Express 10:30 AM');
define('MODULE_SHIPPING_AIRBORNE_TEXT_EXPRESS_SAT', 'Express Saturday');

define('MODULE_SHIPPING_AIRBORNE_TEXT_ERROR', 'An error occured with the DHL/Airborne shipping calculations.<br>If you prefer to use DHL/Airborne as your shipping method, please contact the store owner.');
?>