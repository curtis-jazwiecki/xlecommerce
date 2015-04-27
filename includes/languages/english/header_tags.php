<?php
// /catalog/includes/languages/english/header_tags.php
// Add META TAGS and Modify TITLE
//
// DEFINITIONS FOR /includes/languages/english/header_tags.php

// Define your email address to appear on all pages
define('HEAD_REPLY_TAG_ALL',STORE_OWNER_EMAIL_ADDRESS);

// For all pages not defined or left blank, and for products not defined
// These are included unless you set the toggle switch in each section below to OFF ( '0' )
// The HEAD_TITLE_TAG_ALL is included AFTER the specific one for the page
// The HEAD_DESC_TAG_ALL is included AFTER the specific one for the page
// The HEAD_KEY_TAG_ALL is included AFTER the specific one for the page
define('HEAD_TITLE_TAG_ALL','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_ALL','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_ALL','Guns-R-Us | OBN Demonstration Website');

// DEFINE TAGS FOR INDIVIDUAL PAGES

// advanced_search.php
define('HTTA_ADVANCED_SEARCH_ON','1');
define('HTDA_ADVANCED_SEARCH_ON','1');
define('HTKA_ADVANCED_SEARCH_ON','1');
define('HEAD_TITLE_TAG_ADVANCED_SEARCH','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_ADVANCED_SEARCH','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_ADVANCED_SEARCH','Guns-R-Us | OBN Demonstration Website');
// advanced_search_result.php
define('HTTA_ADVANCED_SEARCH_RESULT_ON','0');
define('HTDA_ADVANCED_SEARCH_RESULT_ON','0');
define('HTKA_ADVANCED_SEARCH_RESULT_ON','0');
define('HEAD_TITLE_TAG_ADVANCED_SEARCH_RESULT','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_ADVANCED_SEARCH_RESULT','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_ADVANCED_SEARCH_RESULT','Guns-R-Us | OBN Demonstration Website');

// index.php
define('HTTA_DEFAULT_ON','0'); // Include HEAD_TITLE_TAG_ALL in Title
define('HTKA_DEFAULT_ON','1'); // Include HEAD_KEY_TAG_ALL in Keywords
define('HTDA_DEFAULT_ON','1'); // Include HEAD_DESC_TAG_ALL in Description
define('HTTA_CAT_DEFAULT_ON', '1'); //Include HEADER_TITLE_DEFAULT in CATEGORY DISPLAY
define('HEAD_TITLE_TAG_DEFAULT', 'Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_DEFAULT','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_DEFAULT','Guns-R-Us | OBN Demonstration Website');
// information.php
define('HTTA_INFORMATION_ON','1');
define('HTDA_INFORMATION_ON','1');
define('HTKA_INFORMATION_ON','1');
define('HEAD_TITLE_TAG_INFORMATION','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_INFORMATION','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_INFORMATION','Guns-R-Us | OBN Demonstration Website');

// product_info.php - if left blank in products_description table these values will be used
define('HTTA_PRODUCT_INFO_ON','0');
define('HTKA_PRODUCT_INFO_ON','1');
define('HTDA_PRODUCT_INFO_ON','1');
define('HTTA_CAT_PRODUCT_DEFAULT_ON', '1');
define('HTPA_DEFAULT_ON', '0');
define('HEAD_TITLE_TAG_PRODUCT_INFO','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_PRODUCT_INFO','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_PRODUCT_INFO','Guns-R-Us | OBN Demonstration Website');

// products_new.php - whats_new
define('HTTA_WHATS_NEW_ON','0');
define('HTKA_WHATS_NEW_ON','1');
define('HTDA_WHATS_NEW_ON','1');
define('HEAD_TITLE_TAG_WHATS_NEW','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_WHATS_NEW','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_WHATS_NEW','Guns-R-Us | OBN Demonstration Website');
// shop.php
define('HTTA_SHOP_ON','0');
define('HTDA_SHOP_ON','0');
define('HTKA_SHOP_ON','0');
define('HEAD_TITLE_TAG_SHOP','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_SHOP','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_SHOP','Guns-R-Us | OBN Demonstration Website');
// shop_by_manufacturer.php
define('HTTA_SHOP_BY_MANUFACTURER_ON','0');
define('HTDA_SHOP_BY_MANUFACTURER_ON','0');
define('HTKA_SHOP_BY_MANUFACTURER_ON','0');
define('HEAD_TITLE_TAG_SHOP_BY_MANUFACTURER','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_SHOP_BY_MANUFACTURER','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_SHOP_BY_MANUFACTURER','Guns-R-Us | OBN Demonstration Website');

// specials.php
// If HEAD_KEY_TAG_SPECIALS is left blank, it will build the keywords from the products_names of all products on special
define('HTTA_SPECIALS_ON','0');
define('HTKA_SPECIALS_ON','1');
define('HTDA_SPECIALS_ON','1');
define('HEAD_TITLE_TAG_SPECIALS','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_SPECIALS','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_SPECIALS','Guns-R-Us | OBN Demonstration Website');

// product_reviews_info.php and product_reviews.php - if left blank in products_description table these values will be used
define('HTTA_PRODUCT_REVIEWS_INFO_ON','0');
define('HTKA_PRODUCT_REVIEWS_INFO_ON','1');
define('HTDA_PRODUCT_REVIEWS_INFO_ON','1');
define('HEAD_TITLE_TAG_PRODUCT_REVIEWS_INFO','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_PRODUCT_REVIEWS_INFO','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_PRODUCT_REVIEWS_INFO','Guns-R-Us | OBN Demonstration Website');

// product_reviews_write.php
define('HTTA_PRODUCT_REVIEWS_WRITE_ON','0');
define('HTKA_PRODUCT_REVIEWS_WRITE_ON','1');
define('HTDA_PRODUCT_REVIEWS_WRITE_ON','1');
define('HEAD_TITLE_TAG_PRODUCT_REVIEWS_WRITE','Guns-R-Us | OBN Demonstration Website');
define('HEAD_DESC_TAG_PRODUCT_REVIEWS_WRITE','Guns-R-Us | OBN Demonstration Website');
define('HEAD_KEY_TAG_PRODUCT_REVIEWS_WRITE','Guns-R-Us | OBN Demonstration Website');

?>
