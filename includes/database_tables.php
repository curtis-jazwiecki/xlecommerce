<?php
/*
  $Id: database_tables.php,v 1.1 2003/03/14 02:10:58 hpdl Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2003 osCommerce
  Released under the GNU General Public License
*/
// define the database table names used in the project
  define('TABLE_ADDRESS_BOOK', 'address_book');
  define('TABLE_ADDRESS_FORMAT', 'address_format');
  define('TABLE_BANNERS', 'banners');
  define('TABLE_BANNERS_HISTORY', 'banners_history');
 // define('TABLE_CATEGORIES', 'categories');
 // define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
  define('TABLE_CONFIGURATION', 'configuration');
  define('TABLE_CONFIGURATION_GROUP', 'configuration_group');
  define('TABLE_COUNTER', 'counter');
  define('TABLE_COUNTER_HISTORY', 'counter_history');
  define('TABLE_COUNTRIES', 'countries');
  define('TABLE_CURRENCIES', 'currencies');
  define('TABLE_CUSTOMERS', 'customers');
  define('TABLE_CUSTOMERS_BASKET', 'customers_basket');
  define('TABLE_CUSTOMERS_BASKET_ATTRIBUTES', 'customers_basket_attributes');
  define('TABLE_CUSTOMERS_INFO', 'customers_info');
  define('TABLE_LANGUAGES', 'languages');
  define('TABLE_MANUFACTURERS', 'manufacturers');
  define('TABLE_MANUFACTURERS_INFO', 'manufacturers_info');
  define('TABLE_ORDERS', 'orders');
  define('TABLE_ORDERS_PRODUCTS', 'orders_products');
  define('TABLE_ORDERS_PRODUCTS_ATTRIBUTES', 'orders_products_attributes');
  define('TABLE_ORDERS_PRODUCTS_DOWNLOAD', 'orders_products_download');
  define('TABLE_ORDERS_STATUS', 'orders_status');
  define('TABLE_ORDERS_STATUS_HISTORY', 'orders_status_history');
  define('TABLE_ORDERS_TOTAL', 'orders_total');
  define('TABLE_PRODUCTS', 'products');
  define('TABLE_PRODUCTS_ATTRIBUTES', 'products_attributes');
  define('TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD', 'products_attributes_download');
  define('TABLE_PRODUCTS_DESCRIPTION', 'products_description');
  define('TABLE_PRODUCTS_NOTIFICATIONS', 'products_notifications');
  define('TABLE_PRODUCTS_OPTIONS', 'products_options');
  define('TABLE_PRODUCTS_OPTIONS_VALUES', 'products_options_values');
  define('TABLE_PRODUCTS_OPTIONS_VALUES_TO_PRODUCTS_OPTIONS', 'products_options_values_to_products_options');
 // define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
  define('TABLE_REVIEWS', 'reviews');
  define('TABLE_REVIEWS_DESCRIPTION', 'reviews_description');
  define('TABLE_SESSIONS', 'sessions');
  define('TABLE_SPECIALS', 'specials');
  define('TABLE_TAX_CLASS', 'tax_class');
  define('TABLE_TAX_RATES', 'tax_rates');
  define('TABLE_GEO_ZONES', 'geo_zones');
  define('TABLE_ZONES_TO_GEO_ZONES', 'zones_to_geo_zones');
  define('TABLE_WHOS_ONLINE', 'whos_online');
  define('TABLE_ZONES', 'zones');
    // Add-on - Information Pages Unlimited
  // BOF Separate Pricing per Customer
  define('TABLE_PRODUCTS_GROUPS', 'products_groups');
  define('TABLE_SPECIALS_RETAIL_PRICES', 'specials_retail_prices');
  define('TABLE_PRODUCTS_GROUP_PRICES', 'products_group_prices_cg_');
  define('TABLE_CUSTOMERS_GROUPS', 'customers_groups');
  define('TABLE_PRODUCTS_ATTRIBUTES_GROUPS', 'products_attributes_groups');
// this will define the maximum time in minutes between updates of a products_group_prices_cg_# table
// changes in table specials will trigger an immediate update if a query needs this particular table
  define('MAXIMUM_DELAY_UPDATE_PG_PRICES_TABLE', '15');
// EOF Separate Pricing per Customer
  define('TABLE_INFORMATION', 'information');
  define('TABLE_FEATURED', 'featured');
 define('TABLE_PRODUCTS_XSELL', 'products_xsell');
 define('TABLE_PACKAGING', 'packaging');
 // #7 12jan2014 (MA) BOF
 define('TABLE_WISHLIST_ATTRIBUTES','customers_wishlist_attributes');
 define('TABLE_WISHLIST','customers_wishlist');
 // #7 12jan2014 (MA) EOF
 // BOF Facebook Store 12_JAN_2014
define('TABLE_FACEBOOKSTORE', 'facebook_products');
// EOF Facebook Store 12_JAN_2014   
// BOF Bundled Products
  define('TABLE_PRODUCTS_BUNDLES', 'products_bundles');
// EOF Bundled Products
  define('TABLE_CUSTOMERS_POINTS_PENDING', 'customers_points_pending'); //Points/Rewards Module V2.1rc2a

//MVS Start
  define('TABLE_ORDERS_SHIPPING','orders_shipping');
  define('TABLE_VENDORS', 'vendors');
  define('TABLE_VENDOR_CONFIGURATION', 'vendor_configuration');
  define('TABLE_VENDOR_CONFIGURATION_GROUP','vendor_configuration_group');
//MVS End

//UPSXML dimensional support (Added for MVS)
  define('TABLE_PACKAGING', 'packaging');
  
  
  define('TABLE_NEWSLETTERS', 'newsletters');
?>