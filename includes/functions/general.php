<?php
/*
  $Id: general.php,v 1.231 2003/07/09 01:15:48 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

////
// Stop from parsing any further PHP code
  function tep_exit() {
   tep_session_close();
   exit();
  }

////
// Redirect to another page or site
  function tep_redirect($url) {
    if ( (strstr($url, "\n") != false) || (strstr($url, "\r") != false) ) { 
      tep_redirect(tep_href_link(FILENAME_DEFAULT, '', 'NONSSL', false));
    }

    if ( (ENABLE_SSL == true) && (getenv('HTTPS') == 'on') ) { // We are loading an SSL page
      if (substr($url, 0, strlen(HTTP_SERVER)) == HTTP_SERVER) { // NONSSL url
        $url = HTTPS_SERVER . substr($url, strlen(HTTP_SERVER)); // Change it to SSL
      }
    }

    header('Location: ' . $url);

    tep_exit();
  }

////
// Parse the data used in the html tags to ensure the tags will not break
  function tep_parse_input_field_data($data, $parse) {
    return strtr(trim($data), $parse);
  }

  function tep_output_string($string, $translate = false, $protected = false) {
    if ($protected == true) {
      return htmlspecialchars($string);
    } else {
      if ($translate == false) {
        return tep_parse_input_field_data($string, array('"' => '&quot;'));
      } else {
        return tep_parse_input_field_data($string, $translate);
      }
    }
  }

  function tep_output_string_protected($string) {
    return tep_output_string($string, false, true);
  }

  function tep_sanitize_string($string) {
    $string = preg_replace('/ +/', ' ', trim($string));

    return preg_replace("/[<>]/", '_', $string);
  }

////
// Return a random row from a database query
  function tep_random_select($query) {
    $random_product = '';
    $random_query = tep_db_query($query);
    $num_rows = tep_db_num_rows($random_query);
    if ($num_rows > 0) {
      $random_row = tep_rand(0, ($num_rows - 1));
      tep_db_data_seek($random_query, $random_row);
      $random_product = tep_db_fetch_array($random_query);
    }

    return $random_product;
  }

////
// Return a product's name
// TABLES: products
  function tep_get_products_name($product_id, $language = '') {
    global $languages_id;

    if (empty($language)) $language = $languages_id;

    $product_query = tep_db_query("select products_name from " . TABLE_PRODUCTS_DESCRIPTION . " where products_id = '" . (int)$product_id . "' and language_id = '" . (int)$language . "'");
    $product = tep_db_fetch_array($product_query);

    return $product['products_name'];
  }

////
// Return a product's special price (returns nothing if there is no offer)
// TABLES: products
  function tep_get_products_special_price($product_id) {
    $product_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status");
      // BOF Separate Pricing Per Customer
 /* if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
    $customer_group_id = $_SESSION['sppc_customer_group_id'];
  } else {
    $customer_group_id = '0';
  }*/

      //  $product_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$product_id . "' and status and customers_group_id = '" . (int)$customer_group_id . "'");
// EOF Separate Pricing Per Customer

    $product = tep_db_fetch_array($product_query);

    return $product['specials_new_products_price'];
  }

////
// Return a product's stock
// TABLES: products
  /*function tep_get_products_stock($products_id) {
    $products_id = tep_get_prid($products_id);
    $stock_query = tep_db_query("select products_quantity from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
    $stock_values = tep_db_fetch_array($stock_query);

    return $stock_values['products_quantity'];
  }*/
  
  // BOF Bundled Products
////
// Return a product's stock
// TABLES: products
  function tep_get_products_stock($products_id) {
    $products_id = tep_get_prid($products_id);
    $stock_query = tep_db_query("select products_quantity, products_bundle from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
    $stock_values = tep_db_fetch_array($stock_query);
    if ($stock_values['products_bundle'] == 'yes') {
      $bundle_query = tep_db_query("select subproduct_id, subproduct_qty from " . TABLE_PRODUCTS_BUNDLES . " where bundle_id = " . (int)$products_id);
      $bundle_stock = array();
      while ($bundle_data = tep_db_fetch_array($bundle_query)) {
        $bundle_stock[] = intval(tep_get_products_stock($bundle_data['subproduct_id']) / $bundle_data['subproduct_qty']);
      }
      return min($bundle_stock); // return quantity of least plentiful subproduct
    } else {
      return $stock_values['products_quantity'];
    }
  }
// EOF Bundled Products

// begin Bundled Products
  // returns an array of all non-bundle products in the bundle with their quantities including products contained in nested bundles
  function get_all_bundle_products($bundle_id) {
    $bundle_query = $bundle_query = tep_db_query('select pb.subproduct_id, pb.subproduct_qty, p.products_bundle from ' . TABLE_PRODUCTS_BUNDLES . ' pb, ' . TABLE_PRODUCTS . ' p where p.products_id = pb.subproduct_id and bundle_id = ' . (int)$bundle_id);
    $product_list = array();
    while ($bundle = tep_db_fetch_array($bundle_query)) {
      if ($bundle['products_bundle'] == 'yes') {
        $bundle_list = get_all_bundle_products($bundle['subproduct_id']);
        foreach ($bundle_list as $id => $qty) {
          $product_list[$id] += $qty;
        }
      } else {
        $product_list[$bundle['subproduct_id']] += $bundle['subproduct_qty'];
      }
    }
    return $product_list;
  }
  // end Bundled Products


////
// Check if the required stock is available
// If insufficent stock is available return an out of stock message
  function tep_check_stock($products_id, $products_quantity) {
    $stock_left = tep_get_products_stock($products_id) - $products_quantity;
    $out_of_stock = '';

    if ($stock_left < 0) {
      $out_of_stock = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
    }

    return $out_of_stock;
  }

////
// Break a word in a string if it is longer than a specified length ($len)
  function tep_break_string($string, $len, $break_char = '-') {
    $l = 0;
    $output = '';
    for ($i=0, $n=strlen($string); $i<$n; $i++) {
      $char = substr($string, $i, 1);
      if ($char != ' ') {
        $l++;
      } else {
        $l = 0;
      }
      if ($l > $len) {
        $l = 1;
        $output .= $break_char;
      }
      $output .= $char;
    }

    return $output;
  }

////
// Return all HTTP GET variables, except those passed as a parameter
  function tep_get_all_get_params($exclude_array = '') {
    global $HTTP_GET_VARS;

    if (!is_array($exclude_array)) $exclude_array = array();

    $get_url = '';
    if (is_array($HTTP_GET_VARS) && (sizeof($HTTP_GET_VARS) > 0)) {
      reset($HTTP_GET_VARS);
      while (list($key, $value) = each($HTTP_GET_VARS)) {
        if ( (strlen($value) > 0) && ($key != tep_session_name()) && ($key != 'error') && (!in_array($key, $exclude_array)) && ($key != 'x') && ($key != 'y') ) {
          $get_url .= $key . '=' . rawurlencode(stripslashes($value)) . '&';
        }
      }
    }

    return $get_url;
  }

////
// Returns an array with countries
// TABLES: countries
  function tep_get_countries($countries_id = '', $with_iso_codes = false) {
    $countries_array = array();
    if (tep_not_null($countries_id)) {
      if ($with_iso_codes == true) {
        $countries = tep_db_query("select countries_name, countries_iso_code_2, countries_iso_code_3 from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "' order by countries_name");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name'],
                                 'countries_iso_code_2' => $countries_values['countries_iso_code_2'],
                                 'countries_iso_code_3' => $countries_values['countries_iso_code_3']);
      } else {
        $countries = tep_db_query("select countries_name from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$countries_id . "'");
        $countries_values = tep_db_fetch_array($countries);
        $countries_array = array('countries_name' => $countries_values['countries_name']);
      }
    } else {
      $countries = tep_db_query("select countries_id, countries_name from " . TABLE_COUNTRIES . " order by countries_name");
      while ($countries_values = tep_db_fetch_array($countries)) {
        $countries_array[] = array('countries_id' => $countries_values['countries_id'],
                                   'countries_name' => $countries_values['countries_name']);
      }
    }

    return $countries_array;
  }

////
// Alias function to tep_get_countries, which also returns the countries iso codes
  function tep_get_countries_with_iso_codes($countries_id) {
    return tep_get_countries($countries_id, true);
  }

////
// Generate a path to categories
  function tep_get_path($current_category_id = '') {
    global $cPath_array;

    if (tep_not_null($current_category_id)) {
      $cp_size = sizeof($cPath_array);
      if ($cp_size == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$cPath_array[($cp_size-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

        $current_category_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i=0; $i<($cp_size-1); $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i=0; $i<$cp_size; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }
        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    } else {
      $cPath_new = implode('_', $cPath_array);
    }

    return 'cPath=' . $cPath_new;
  }

////
// Returns the clients browser
  function tep_browser_detect($component) {
    //global $HTTP_USER_AGENT;
    //return stristr($HTTP_USER_AGENT, $component);
	return stristr($_SERVER['HTTP_USER_AGENT'], $component);
  }

////
// Alias function to tep_get_countries()
  function tep_get_country_name($country_id) {
    $country_array = tep_get_countries($country_id);

    return $country_array['countries_name'];
  }

////
// Returns the zone (State/Province) name
// TABLES: zones
  function tep_get_zone_name($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_name'];
    } else {
      return $default_zone;
    }
  }

////
// Returns the zone (State/Province) code
// TABLES: zones
  function tep_get_zone_code($country_id, $zone_id, $default_zone) {
    $zone_query = tep_db_query("select zone_code from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country_id . "' and zone_id = '" . (int)$zone_id . "'");
    if (tep_db_num_rows($zone_query)) {
      $zone = tep_db_fetch_array($zone_query);
      return $zone['zone_code'];
    } else {
      return $default_zone;
    }
  }

////
// Wrapper function for round()
  function tep_round($number, $precision) {
    if (strpos($number, '.') && (strlen(substr($number, strpos($number, '.')+1)) > $precision)) {
      $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

      if (substr($number, -1) >= 5) {
        if ($precision > 1) {
          $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
        } elseif ($precision == 1) {
          $number = substr($number, 0, -1) + 0.1;
        } else {
          $number = substr($number, 0, -1) + 1;
        }
      } else {
        $number = substr($number, 0, -1);
      }
    }

    return $number;
  }

////
// Returns the tax rate for a zone / class
// TABLES: tax_rates, zones_to_geo_zones
  function tep_get_tax_rate($class_id, $country_id = -1, $zone_id = -1) {
    global $customer_zone_id, $customer_country_id;
// BOF Separate Pricing Per Customer, tax exempt modifications

     if (!isset($_SESSION['sppc_customer_group_tax_exempt'])) {
       $customer_group_tax_exempt = '0';
     } else {
       $customer_group_tax_exempt = $_SESSION['sppc_customer_group_tax_exempt'];
     }

     if ($customer_group_tax_exempt == '1') {
	     return 0;
     }
		 
	 if ( isset($_SESSION['sppc_customer_specific_taxes_exempt']) && tep_not_null($_SESSION['sppc_customer_specific_taxes_exempt']) ) {
	    $additional_for_specific_taxes = "AND tax_rates_id NOT IN ( ". $_SESSION['sppc_customer_specific_taxes_exempt'] ." )";   
      } else {
	    $additional_for_specific_taxes = '';
	 }
// EOF Separate Pricing Per Customer, tax exempt modifications

    if ( ($country_id == -1) && ($zone_id == -1) ) {
      if (!tep_session_is_registered('customer_id')) {
        $country_id = STORE_COUNTRY;
        $zone_id = STORE_ZONE;
      } else {
        $country_id = $customer_country_id;
        $zone_id = $customer_zone_id;
      }
    }

  //  $tax_query = tep_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' group by tr.tax_priority");
    // BOF Separate Pricing Per Customer, specific taxes exempt modification
    $tax_query = tep_db_query("select sum(tax_rate) as tax_rate from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' " . $additional_for_specific_taxes . " group by tr.tax_priority");
// EOF Separate Pricing Per Customer, specific taxes exempt modification

    if (tep_db_num_rows($tax_query)) {
      $tax_multiplier = 1.0;
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_multiplier *= 1.0 + ($tax['tax_rate'] / 100);
      }
      return ($tax_multiplier - 1.0) * 100;
    } else {
      return 0;
    }
  }

////
// Return the tax description for a zone / class
// TABLES: tax_rates;
  function tep_get_tax_description($class_id, $country_id, $zone_id) {
   // $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' order by tr.tax_priority");
      		
// BOF Separate Pricing Per Customer, specific taxes exempt modification 
	if (isset($_SESSION['sppc_customer_specific_taxes_exempt']) && tep_not_null($_SESSION['sppc_customer_specific_taxes_exempt']) ) {
	   $additional_for_specific_taxes = "AND tax_rates_id NOT IN ( ". $_SESSION['sppc_customer_specific_taxes_exempt'] ." )";   
     } else {
	   $additional_for_specific_taxes = '';
     }

    $tax_query = tep_db_query("select tax_description from " . TABLE_TAX_RATES . " tr left join " . TABLE_ZONES_TO_GEO_ZONES . " za on (tr.tax_zone_id = za.geo_zone_id) left join " . TABLE_GEO_ZONES . " tz on (tz.geo_zone_id = tr.tax_zone_id) where (za.zone_country_id is null or za.zone_country_id = '0' or za.zone_country_id = '" . (int)$country_id . "') and (za.zone_id is null or za.zone_id = '0' or za.zone_id = '" . (int)$zone_id . "') and tr.tax_class_id = '" . (int)$class_id . "' " . $additional_for_specific_taxes . " order by tr.tax_priority");
// EOF Separate Pricing Per Customer, specific taxes exempt modification	

    if (tep_db_num_rows($tax_query)) {
      $tax_description = '';
      while ($tax = tep_db_fetch_array($tax_query)) {
        $tax_description .= $tax['tax_description'] . ' + ';
      }
      $tax_description = substr($tax_description, 0, -3);

      return $tax_description;
    } else {
      return TEXT_UNKNOWN_TAX_RATE;
    }
  }

////
// Add tax to a products price
  function tep_add_tax($price, $tax) {
    global $currencies;

   // if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
    // BOF Separate Pricing Per Customer, show_tax modification
// next line was original code
//    if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) ) {
      if (!isset($_SESSION['sppc_customer_group_show_tax'])) {
        $customer_group_show_tax = '1';
      } else {
        $customer_group_show_tax = $_SESSION['sppc_customer_group_show_tax'];
      }

     if ( (DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0) && ($customer_group_show_tax == '1')) {
// EOF Separate Pricing Per Customer, show_tax modification

      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']) + tep_calculate_tax($price, $tax);
    } else {
      return tep_round($price, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
    }
  }

// Calculates Tax rounding the result
  function tep_calculate_tax($price, $tax) {
    global $currencies;

    return tep_round($price * $tax / 100, $currencies->currencies[DEFAULT_CURRENCY]['decimal_places']);
  }

////
// Return the number of products in a category
// TABLES: products, products_to_categories, categories
  function tep_count_products_in_category($category_id, $include_inactive = false) {
    $products_count = 0;
    if ($include_inactive == true) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$category_id . "'");
    } else {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$category_id . "'");
    }
    $products = tep_db_fetch_array($products_query);
    $products_count += $products['total'];

    $child_categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    if (tep_db_num_rows($child_categories_query)) {
      while ($child_categories = tep_db_fetch_array($child_categories_query)) {
        $products_count += tep_count_products_in_category($child_categories['categories_id'], $include_inactive);
      }
    }

    return $products_count;
  }

////
// Return true if the category has subcategories
// TABLES: categories
  function tep_has_category_subcategories($category_id) {
    $child_category_query = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$category_id . "'");
    $child_category = tep_db_fetch_array($child_category_query);

    if ($child_category['count'] > 0) {
      return true;
    } else {
      return false;
    }
  }

////
// Returns the address_format_id for the given country
// TABLES: countries;
  function tep_get_address_format_id($country_id) {
    $address_format_query = tep_db_query("select address_format_id as format_id from " . TABLE_COUNTRIES . " where countries_id = '" . (int)$country_id . "'");
    if (tep_db_num_rows($address_format_query)) {
      $address_format = tep_db_fetch_array($address_format_query);
      return $address_format['format_id'];
    } else {
      return '1';
    }
  }

////
// Return a formatted address
// TABLES: address_format
  function tep_address_format($address_format_id, $address, $html, $boln, $eoln) {
    $address_format_query = tep_db_query("select address_format as format from " . TABLE_ADDRESS_FORMAT . " where address_format_id = '" . (int)$address_format_id . "'");
    $address_format = tep_db_fetch_array($address_format_query);

    $company = tep_output_string_protected($address['company']);
    if (isset($address['firstname']) && tep_not_null($address['firstname'])) {
      $firstname = tep_output_string_protected($address['firstname']);
      $lastname = tep_output_string_protected($address['lastname']);
    } elseif (isset($address['name']) && tep_not_null($address['name'])) {
      $firstname = tep_output_string_protected($address['name']);
      $lastname = '';
    } else {
      $firstname = '';
      $lastname = '';
    }
    $street = tep_output_string_protected($address['street_address']);
    $suburb = tep_output_string_protected($address['suburb']);
    $city = tep_output_string_protected($address['city']);
    $state = tep_output_string_protected($address['state']);
    if (isset($address['country_id']) && tep_not_null($address['country_id'])) {
      $country = tep_get_country_name($address['country_id']);

      if (isset($address['zone_id']) && tep_not_null($address['zone_id'])) {
        $state = tep_get_zone_code($address['country_id'], $address['zone_id'], $state);
      }
    //} elseif (isset($address['country']) && tep_not_null($address['country'])) {
      } elseif (isset($address['country']) && tep_not_null($address['country']) && !empty($address['country']['title'])) {
      $country = tep_output_string_protected($address['country']['title']);
    } else {
      $country = '';
    }
    $postcode = tep_output_string_protected($address['postcode']);
    $zip = $postcode;

    if ($html) {
// HTML Mode
      $HR = '<hr>';
      $hr = '<hr>';
      if ( ($boln == '') && ($eoln == "\n") ) { // Values not specified, use rational defaults
        $CR = '<br>';
        $cr = '<br>';
        $eoln = $cr;
      } else { // Use values supplied
        $CR = $eoln . $boln;
        $cr = $CR;
      }
    } else {
// Text Mode
      $CR = $eoln;
      $cr = $CR;
      $HR = '----------------------------------------';
      $hr = '----------------------------------------';
    }

    $statecomma = '';
    $streets = $street;
    if ($suburb != '') $streets = $street . $cr . $suburb;
    if ($state != '') $statecomma = $state . ', ';

    $fmt = $address_format['format'];
    eval("\$address = \"$fmt\";");

    if ( (ACCOUNT_COMPANY == 'true') && (tep_not_null($company)) ) {
      $address = $company . $cr . $address;
    }

    return $address;
  }

////
// Return a formatted address
// TABLES: customers, address_book
  function tep_address_label($customers_id, $address_id = 1, $html = false, $boln = '', $eoln = "\n") {
  	// Ingo PWA Beginn
    if ($customers_id == 0) {
      global $order;
      if ($address_id == 1) {
        $address = $order->pwa_label_shipping;
      } else {
        $address = $order->pwa_label_customer;
      }
    } else {
    $address_query = tep_db_query("select entry_firstname as firstname, entry_lastname as lastname, entry_company as company, entry_street_address as street_address, entry_suburb as suburb, entry_city as city, entry_postcode as postcode, entry_state as state, entry_zone_id as zone_id, entry_country_id as country_id from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customers_id . "' and address_book_id = '" . (int)$address_id . "'");
    $address = tep_db_fetch_array($address_query);
} // Ingo PWA Ende
    $format_id = tep_get_address_format_id($address['country_id']);

    return tep_address_format($format_id, $address, $html, $boln, $eoln);
  }

  function tep_row_number_format($number) {
    if ( ($number < 10) && (substr($number, 0, 1) != '0') ) $number = '0' . $number;

    return $number;
  }

  function tep_get_categories($categories_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_array[] = array('id' => $categories['categories_id'],
                                  'text' => $indent . $categories['categories_name']);

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

  function tep_get_categories_enabled($categories_array = '', $parent_id = '0', $indent = '') {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    $categories_query = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where parent_id = '" . (int)$parent_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and categories_status = '1' order by sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_array[] = array('id' => $categories['categories_id'],
                                  'text' => $indent . $categories['categories_name']);

      if ($categories['categories_id'] != $parent_id) {
        $categories_array = tep_get_categories($categories_array, $categories['categories_id'], $indent . '&nbsp;&nbsp;');
      }
    }

    return $categories_array;
  }

  function tep_get_manufacturers($manufacturers_array = '') {
    if (!is_array($manufacturers_array)) $manufacturers_array = array();

    $manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from " . TABLE_MANUFACTURERS . " order by manufacturers_name");
    while ($manufacturers = tep_db_fetch_array($manufacturers_query)) {
      $manufacturers_array[] = array('id' => $manufacturers['manufacturers_id'], 'text' => $manufacturers['manufacturers_name']);
    }

    return $manufacturers_array;
  }

////
// Return all subcategory IDs
// TABLES: categories
  function tep_get_subcategories(&$subcategories_array, $parent_id = 0) {
    $subcategories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "'");
    while ($subcategories = tep_db_fetch_array($subcategories_query)) {
      $subcategories_array[sizeof($subcategories_array)] = $subcategories['categories_id'];
      if ($subcategories['categories_id'] != $parent_id) {
        tep_get_subcategories($subcategories_array, $subcategories['categories_id']);
      }
    }
  }

// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
  function tep_date_long($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || ($raw_date == '') ) return false;

    $year = (int)substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    return strftime(DATE_FORMAT_LONG, mktime($hour,$minute,$second,$month,$day,$year));
  }

////
// Output a raw date string in the selected locale date format
// $raw_date needs to be in this format: YYYY-MM-DD HH:MM:SS
// NOTE: Includes a workaround for dates before 01/01/1970 that fail on windows servers
  function tep_date_short($raw_date) {
    if ( ($raw_date == '0000-00-00 00:00:00') || empty($raw_date) ) return false;

    $year = substr($raw_date, 0, 4);
    $month = (int)substr($raw_date, 5, 2);
    $day = (int)substr($raw_date, 8, 2);
    $hour = (int)substr($raw_date, 11, 2);
    $minute = (int)substr($raw_date, 14, 2);
    $second = (int)substr($raw_date, 17, 2);

    if (@date('Y', mktime($hour, $minute, $second, $month, $day, $year)) == $year) {
      return date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, $year));
    } else {
      return preg_replace('/2037' . '$/', $year, date(DATE_FORMAT, mktime($hour, $minute, $second, $month, $day, 2037)));
    }
  }

////
// Parse search string into indivual objects
  function tep_parse_search_string($search_str = '', &$objects) {
    $search_str = trim(strtolower($search_str));

// Break up $search_str on whitespace; quoted string will be reconstructed later
    //$pieces = split('[[:space:]]+', $search_str);
    $pieces = explode('[[:space:]]+', $search_str);
    $objects = array();
    $tmpstring = '';
    $flag = '';

    for ($k=0; $k<count($pieces); $k++) {
      while (substr($pieces[$k], 0, 1) == '(') {
        $objects[] = '(';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 1);
        } else {
          $pieces[$k] = '';
        }
      }

      $post_objects = array();

      while (substr($pieces[$k], -1) == ')')  {
        $post_objects[] = ')';
        if (strlen($pieces[$k]) > 1) {
          $pieces[$k] = substr($pieces[$k], 0, -1);
        } else {
          $pieces[$k] = '';
        }
      }

// Check individual words

      if ( (substr($pieces[$k], -1) != '"') && (substr($pieces[$k], 0, 1) != '"') ) {
        $objects[] = trim($pieces[$k]);

        for ($j=0; $j<count($post_objects); $j++) {
          $objects[] = $post_objects[$j];
        }
      } else {
/* This means that the $piece is either the beginning or the end of a string.
   So, we'll slurp up the $pieces and stick them together until we get to the
   end of the string or run out of pieces.
*/

// Add this word to the $tmpstring, starting the $tmpstring
        $tmpstring = trim(preg_replace('/"/', ' ', $pieces[$k]));

// Check for one possible exception to the rule. That there is a single quoted word.
        if (substr($pieces[$k], -1 ) == '"') {
// Turn the flag off for future iterations
          $flag = 'off';

          $objects[] = trim($pieces[$k]);

          for ($j=0; $j<count($post_objects); $j++) {
            $objects[] = $post_objects[$j];
          }

          unset($tmpstring);

// Stop looking for the end of the string and move onto the next word.
          continue;
        }

// Otherwise, turn on the flag to indicate no quotes have been found attached to this word in the string.
        $flag = 'on';

// Move on to the next word
        $k++;

// Keep reading until the end of the string as long as the $flag is on

        while ( ($flag == 'on') && ($k < count($pieces)) ) {
          while (substr($pieces[$k], -1) == ')') {
            $post_objects[] = ')';
            if (strlen($pieces[$k]) > 1) {
              $pieces[$k] = substr($pieces[$k], 0, -1);
            } else {
              $pieces[$k] = '';
            }
          }

// If the word doesn't end in double quotes, append it to the $tmpstring.
          if (substr($pieces[$k], -1) != '"') {
// Tack this word onto the current string entity
            $tmpstring .= ' ' . $pieces[$k];

// Move on to the next word
            $k++;
            continue;
          } else {
/* If the $piece ends in double quotes, strip the double quotes, tack the
   $piece onto the tail of the string, push the $tmpstring onto the $haves,
   kill the $tmpstring, turn the $flag "off", and return.
*/
            $tmpstring .= ' ' . trim(preg_replace('/"/', ' ', $pieces[$k]));

// Push the $tmpstring onto the array of stuff to search for
            $objects[] = trim($tmpstring);

            for ($j=0; $j<count($post_objects); $j++) {
              $objects[] = $post_objects[$j];
            }

            unset($tmpstring);

// Turn off the flag to exit the loop
            $flag = 'off';
          }
        }
      }
    }

// add default logical operators if needed
    $temp = array();
    for($i=0; $i<(count($objects)-1); $i++) {
      $temp[] = $objects[$i];
      if ( ($objects[$i] != 'and') &&
           ($objects[$i] != 'or') &&
           ($objects[$i] != '(') &&
           ($objects[$i+1] != 'and') &&
           ($objects[$i+1] != 'or') &&
           ($objects[$i+1] != ')') ) {
        $temp[] = ADVANCED_SEARCH_DEFAULT_OPERATOR;
      }
    }
    $temp[] = $objects[$i];
    $objects = $temp;

    $keyword_count = 0;
    $operator_count = 0;
    $balance = 0;
    for($i=0; $i<count($objects); $i++) {
      if ($objects[$i] == '(') $balance --;
      if ($objects[$i] == ')') $balance ++;
      if ( ($objects[$i] == 'and') || ($objects[$i] == 'or') ) {
        $operator_count ++;
      } elseif ( ($objects[$i]) && ($objects[$i] != '(') && ($objects[$i] != ')') ) {
        $keyword_count ++;
      }
    }

    if ( ($operator_count < $keyword_count) && ($balance == 0) ) {
      return true;
    } else {
      return false;
    }
  }

////
// Check date
  function tep_checkdate($date_to_check, $format_string, &$date_array) {
    $separator_idx = -1;

    $separators = array('-', ' ', '/', '.');
    $month_abbr = array('jan','feb','mar','apr','may','jun','jul','aug','sep','oct','nov','dec');
    $no_of_days = array(31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);

    $format_string = strtolower($format_string);

    if (strlen($date_to_check) != strlen($format_string)) {
      return false;
    }

    $size = sizeof($separators);
    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($date_to_check, $separators[$i]);
      if ($pos_separator != false) {
        $date_separator_idx = $i;
        break;
      }
    }

    for ($i=0; $i<$size; $i++) {
      $pos_separator = strpos($format_string, $separators[$i]);
      if ($pos_separator != false) {
        $format_separator_idx = $i;
        break;
      }
    }

    if ($date_separator_idx != $format_separator_idx) {
      return false;
    }

    if ($date_separator_idx != -1) {
      $format_string_array = explode( $separators[$date_separator_idx], $format_string );
      if (sizeof($format_string_array) != 3) {
        return false;
      }

      $date_to_check_array = explode( $separators[$date_separator_idx], $date_to_check );
      if (sizeof($date_to_check_array) != 3) {
        return false;
      }

      $size = sizeof($format_string_array);
      for ($i=0; $i<$size; $i++) {
        if ($format_string_array[$i] == 'mm' || $format_string_array[$i] == 'mmm') $month = $date_to_check_array[$i];
        if ($format_string_array[$i] == 'dd') $day = $date_to_check_array[$i];
        if ( ($format_string_array[$i] == 'yyyy') || ($format_string_array[$i] == 'aaaa') ) $year = $date_to_check_array[$i];
      }
    } else {
      if (strlen($format_string) == 8 || strlen($format_string) == 9) {
        $pos_month = strpos($format_string, 'mmm');
        if ($pos_month != false) {
          $month = substr( $date_to_check, $pos_month, 3 );
          $size = sizeof($month_abbr);
          for ($i=0; $i<$size; $i++) {
            if ($month == $month_abbr[$i]) {
              $month = $i;
              break;
            }
          }
        } else {
          $month = substr($date_to_check, strpos($format_string, 'mm'), 2);
        }
      } else {
        return false;
      }

      $day = substr($date_to_check, strpos($format_string, 'dd'), 2);
      $year = substr($date_to_check, strpos($format_string, 'yyyy'), 4);
    }

    if (strlen($year) != 4) {
      return false;
    }

    if (!settype($year, 'integer') || !settype($month, 'integer') || !settype($day, 'integer')) {
      return false;
    }

    if ($month > 12 || $month < 1) {
      return false;
    }

    if ($day < 1) {
      return false;
    }

    if (tep_is_leap_year($year)) {
      $no_of_days[1] = 29;
    }

    if ($day > $no_of_days[$month - 1]) {
      return false;
    }

    $date_array = array($year, $month, $day);

    return true;
  }

////
// Check if year is a leap year
  function tep_is_leap_year($year) {
    if ($year % 100 == 0) {
      if ($year % 400 == 0) return true;
    } else {
      if (($year % 4) == 0) return true;
    }

    return false;
  }

////
// Return table heading with sorting capabilities
  function tep_create_sort_heading($sortby, $colnum, $heading) {
    global $PHP_SELF;

    $sort_prefix = '';
    $sort_suffix = '';

    if ($sortby) {
      $sort_prefix = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('page', 'info', 'sort')) . 'page=1&sort=' . $colnum . ($sortby == $colnum . 'a' ? 'd' : 'a')) . '" title="' . tep_output_string(TEXT_SORT_PRODUCTS . ($sortby == $colnum . 'd' || substr($sortby, 0, 1) != $colnum ? TEXT_ASCENDINGLY : TEXT_DESCENDINGLY) . TEXT_BY . $heading) . '" class="productListing-heading">' ;
      $sort_suffix = (substr($sortby, 0, 1) == $colnum ? (substr($sortby, 1, 1) == 'a' ? '+' : '-') : '') . '</a>';
    }

    return $sort_prefix . $heading . $sort_suffix;
  }

////
// Recursively go through the categories and retreive all parent categories IDs
// TABLES: categories
  function tep_get_parent_categories(&$categories, $categories_id) {
    $parent_categories_query = tep_db_query("select parent_id from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$categories_id . "'");
    while ($parent_categories = tep_db_fetch_array($parent_categories_query)) {
      if ($parent_categories['parent_id'] == 0) return true;
      $categories[sizeof($categories)] = $parent_categories['parent_id'];
      if ($parent_categories['parent_id'] != $categories_id) {
        tep_get_parent_categories($categories, $parent_categories['parent_id']);
      }
    }
  }

////
// Construct a category path to the product
// TABLES: products_to_categories
  function tep_get_product_path($products_id) {
    $cPath = '';

    $category_query = tep_db_query("select p2c.categories_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_id = '" . (int)$products_id . "' and p.products_status = '1' and p.products_id = p2c.products_id limit 1");
    if (tep_db_num_rows($category_query)) {
      $category = tep_db_fetch_array($category_query);

      $categories = array();
      tep_get_parent_categories($categories, $category['categories_id']);

      $categories = array_reverse($categories);

      $cPath = implode('_', $categories);

      if (tep_not_null($cPath)) $cPath .= '_';
      $cPath .= $category['categories_id'];
    }

    return $cPath;
  }

////
// Return a product ID with attributes
  function tep_get_uprid($prid, $params) {
    if (is_numeric($prid)) {
      $uprid = $prid;

      if (is_array($params) && (sizeof($params) > 0)) {
        $attributes_check = true;
        $attributes_ids = '';

        reset($params);
        while (list($option, $value) = each($params)) {
          if (is_numeric($option) && is_numeric($value)) {
            $attributes_ids .= '{' . (int)$option . '}' . (int)$value;
          } else {
            $attributes_check = false;
            break;
          }
        }

        if ($attributes_check == true) {
          $uprid .= $attributes_ids;
        }
      }
    } else {
      $uprid = tep_get_prid($prid);

      if (is_numeric($uprid)) {
        if (strpos($prid, '{') !== false) {
          $attributes_check = true;
          $attributes_ids = '';

// strpos()+1 to remove up to and including the first { which would create an empty array element in explode()
          $attributes = explode('{', substr($prid, strpos($prid, '{')+1));

          for ($i=0, $n=sizeof($attributes); $i<$n; $i++) {
            $pair = explode('}', $attributes[$i]);

            if (is_numeric($pair[0]) && is_numeric($pair[1])) {
              $attributes_ids .= '{' . (int)$pair[0] . '}' . (int)$pair[1];
            } else {
              $attributes_check = false;
              break;
            }
          }

          if ($attributes_check == true) {
            $uprid .= $attributes_ids;
          }
        }
      } else {
        return false;
      }
    }

    return $uprid;
  }

////
// Return a product ID from a product ID with attributes
  function tep_get_prid($uprid) {
    $pieces = explode('{', $uprid);

    if (is_numeric($pieces[0])) {
      return $pieces[0];
    } else {
      return false;
    }
  }

////
// Return a customer greeting
  function tep_customer_greeting() {
    global $customer_id, $customer_first_name;

    if (tep_session_is_registered('customer_first_name') && tep_session_is_registered('customer_id')) {
      $greeting_string = sprintf(TEXT_GREETING_PERSONAL, tep_output_string_protected($customer_first_name), tep_href_link(FILENAME_PRODUCTS_NEW));
    } else {
      $greeting_string = sprintf(TEXT_GREETING_GUEST, tep_href_link(FILENAME_LOGIN, '', 'SSL'), tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));
    }

    return $greeting_string;
  }

////
//! Send email (text/html) using MIME
// This is the central mail function. The SMTP Server should be configured
// correct in php.ini
// Parameters:
// $to_name           The name of the recipient, e.g. "Jan Wildeboer"
// $to_email_address  The eMail address of the recipient,
//                    e.g. jan.wildeboer@gmx.de
// $email_subject     The subject of the eMail
// $email_text        The text of the eMail, may contain HTML entities
// $from_email_name   The name of the sender, e.g. Shop Administration
// $from_email_adress The eMail address of the sender,
//                    e.g. info@mytepshop.com

  function tep_mail($to_name, $to_email_address, $email_subject, $email_text, $from_email_name, $from_email_address) {
    if (SEND_EMAILS != 'true') return false;

    // Instantiate a new mail object
    $message = new email(array('X-Mailer: osCommerce Mailer'));

    // Build the text version
    $text = strip_tags($email_text);
    if (EMAIL_USE_HTML == 'true') {
      $message->add_html($email_text, $text);
    } else {
      $message->add_text($text);
    }

    // Send message
    $message->build_message();
    $message->send($to_name, $to_email_address, $from_email_name, $from_email_address, $email_subject);
  }

////
// Check if product has attributes
  function tep_has_product_attributes($products_id) {
    $attributes_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "'");
    $attributes = tep_db_fetch_array($attributes_query);

    if ($attributes['count'] > 0) {
      return true;
    } else {
      return false;
    }
  } /*
  function tep_has_product_attributes($products_id) {
// BOF Hide attributes from customer groups (SPPC 4.2 and higher)
  global $sppc_customer_group_id;
 
  if(!tep_session_is_registered('sppc_customer_group_id')) { 
  $customer_group_id = '0';
  } else {
     $customer_group_id = $sppc_customer_group_id;
  }
    $attributes_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and find_in_set('".$customer_group_id."', attributes_hide_from_groups) = 0 ");
// EOF Hide attributes from customer groups (SPPC 4.2 and higher)    
    $attributes = tep_db_fetch_array($attributes_query);

    if ($attributes['count'] > 0) {
      return true;
    } else {
      return false;
    }
  } */

////
// Get the number of times a word/character is present in a string
  function tep_word_count($string, $needle) {
    $temp_array = split($needle, $string);

    return sizeof($temp_array);
  }

  function tep_count_modules($modules = '') {
    $count = 0;

    if (empty($modules)) return $count;

    //$modules_array = split(';', $modules);
    $modules_array = explode(';', $modules);

    for ($i=0, $n=sizeof($modules_array); $i<$n; $i++) {
      $class = substr($modules_array[$i], 0, strrpos($modules_array[$i], '.'));

      if (is_object($GLOBALS[$class])) {
        if ($GLOBALS[$class]->enabled) {
          $count++;
        }
      }
    }

    return $count;
  }

  function tep_count_payment_modules() {
    return tep_count_modules(MODULE_PAYMENT_INSTALLED);
  }

  function tep_count_shipping_modules() {
    return tep_count_modules(MODULE_SHIPPING_INSTALLED);
  }

  function tep_create_random_value($length, $type = 'mixed') {
    if ( ($type != 'mixed') && ($type != 'chars') && ($type != 'digits')) return false;

    $rand_value = '';
    while (strlen($rand_value) < $length) {
      if ($type == 'digits') {
        $char = tep_rand(0,9);
      } else {
        $char = chr(tep_rand(0,255));
      }
      if ($type == 'mixed') {
        if (preg_match('/^[a-z0-9]$/i', $char)) $rand_value .= $char;
      } elseif ($type == 'chars') {
        if (preg_match('/^[a-z]$/i', $char)) $rand_value .= $char;
      } elseif ($type == 'digits') {
        if (preg_match('/^[0-9]$/', $char)) $rand_value .= $char;
      }
    }

    return $rand_value;
  }

  function tep_array_to_string($array, $exclude = '', $equals = '=', $separator = '&') {
    if (!is_array($exclude)) $exclude = array();

    $get_string = '';
    if (sizeof($array) > 0) {
      while (list($key, $value) = each($array)) {
        if ( (!in_array($key, $exclude)) && ($key != 'x') && ($key != 'y') ) {
          $get_string .= $key . $equals . $value . $separator;
        }
      }
      $remove_chars = strlen($separator);
      $get_string = substr($get_string, 0, -$remove_chars);
    }

    return $get_string;
  }

  function tep_not_null($value) {
    if (is_array($value)) {
      if (sizeof($value) > 0) {
        return true;
      } else {
        return false;
      }
    } else {
      if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
        return true;
      } else {
        return false;
      }
    }
  }

////
// Output the tax percentage with optional padded decimals
  function tep_display_tax_value($value, $padding = TAX_DECIMAL_PLACES) {
    if (strpos($value, '.')) {
      $loop = true;
      while ($loop) {
        if (substr($value, -1) == '0') {
          $value = substr($value, 0, -1);
        } else {
          $loop = false;
          if (substr($value, -1) == '.') {
            $value = substr($value, 0, -1);
          }
        }
      }
    }

    if ($padding > 0) {
      if ($decimal_pos = strpos($value, '.')) {
        $decimals = strlen(substr($value, ($decimal_pos+1)));
        for ($i=$decimals; $i<$padding; $i++) {
          $value .= '0';
        }
      } else {
        $value .= '.';
        for ($i=0; $i<$padding; $i++) {
          $value .= '0';
        }
      }
    }

    return $value;
  }

////
// Checks to see if the currency code exists as a currency
// TABLES: currencies
  function tep_currency_exists($code) {
    $code = tep_db_prepare_input($code);

    $currency_code = tep_db_query("select currencies_id from " . TABLE_CURRENCIES . " where code = '" . tep_db_input($code) . "'");
    if (tep_db_num_rows($currency_code)) {
      return $code;
    } else {
      return false;
    }
  }

  function tep_string_to_int($string) {
    return (int)$string;
  }

////
// Parse and secure the cPath parameter values
  function tep_parse_category_path($cPath) {
// make sure the category IDs are integers
    $cPath_array = array_map('tep_string_to_int', explode('_', $cPath));

// make sure no duplicate category IDs exist which could lock the server in a loop
    $tmp_array = array();
    $n = sizeof($cPath_array);
    for ($i=0; $i<$n; $i++) {
      if (!in_array($cPath_array[$i], $tmp_array)) {
        $tmp_array[] = $cPath_array[$i];
      }
    }

    return $tmp_array;
  }

////
// Return a random value
  function tep_rand($min = null, $max = null) {
    static $seeded;

    if (!isset($seeded)) {
      mt_srand((double)microtime()*1000000);
      $seeded = true;
    }

    if (isset($min) && isset($max)) {
      if ($min >= $max) {
        return $min;
      } else {
        return mt_rand($min, $max);
      }
    } else {
      return mt_rand();
    }
  }

  function tep_setcookie($name, $value = '', $expire = 0, $path = '/', $domain = '', $secure = 0) {
    setcookie($name, $value, $expire, $path, (tep_not_null($domain) ? $domain : ''), $secure);
  }

  function tep_get_ip_address() {
    if (isset($_SERVER)) {
      if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
      } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
      } else {
        $ip = $_SERVER['REMOTE_ADDR'];
      }
    } else {
      if (getenv('HTTP_X_FORWARDED_FOR')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
      } elseif (getenv('HTTP_CLIENT_IP')) {
        $ip = getenv('HTTP_CLIENT_IP');
      } else {
        $ip = getenv('REMOTE_ADDR');
      }
    }

    return $ip;
  }

  function tep_count_customer_orders($id = '', $check_session = true) {
    global $customer_id;

    if (is_numeric($id) == false) {
      if (tep_session_is_registered('customer_id')) {
        $id = $customer_id;
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( (tep_session_is_registered('customer_id') == false) || ($id != $customer_id) ) {
        return 0;
      }
    }

    $orders_check_query = tep_db_query("select count(*) as total from " . TABLE_ORDERS . " where customers_id = '" . (int)$id . "'");
    $orders_check = tep_db_fetch_array($orders_check_query);

    return $orders_check['total'];
  }

  function tep_count_customer_address_book_entries($id = '', $check_session = true) {
    global $customer_id;

    if (is_numeric($id) == false) {
      if (tep_session_is_registered('customer_id')) {
        $id = $customer_id;
      } else {
        return 0;
      }
    }

    if ($check_session == true) {
      if ( (tep_session_is_registered('customer_id') == false) || ($id != $customer_id) ) {
        return 0;
      }
    }

    $addresses_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$id . "'");
    $addresses = tep_db_fetch_array($addresses_query);

    return $addresses['total'];
  }

// nl2br() prior PHP 4.2.0 did not convert linefeeds on all OSs (it only converted \n)
  function tep_convert_linefeeds($from, $to, $string) {
    if ((PHP_VERSION < "4.0.5") && is_array($from)) {
      return preg_replace('/(' . implode('|', $from) . ')/', $to, $string);
    } else {
      return str_replace($from, $to, $string);
    }
  }
  function tep_array_values_to_string($array, $separator = ',') {
	$get_string = '';
	if (sizeof($array) > 0) {
		while (list($key, $value) = each($array)) {
				$get_string .= $value . $separator;
		}
		$remove_chars = strlen($separator);
		$get_string = substr($get_string, 0, -$remove_chars);
	}
	return $get_string;
}

function tep_required_disclaimer($product_id) {
	$product_query = tep_db_query("select disclaimer_needed from products where products_id = '" . (int)$product_id . "'");
	if (tep_db_num_rows($product_query) > 0) {
		$product = tep_db_fetch_array($product_query);
		return $product['disclaimer_needed'];
	}
	return false;
}

function is_xml_feed_product($product_id) {
	$check_query = tep_db_query("select count(*) as total from products_extended where osc_products_id = '" . (int)$product_id . "'");
    $check = tep_db_fetch_array($check_query);
     if ($check['total'] > 0) 
       return true;
      else 
	    return false; 
}

  function link_get_variable($var_name)
  {
    // Map global to GET variable
    if (isset($_GET[$var_name]))
    {
      $GLOBALS[$var_name] =& $_GET[$var_name];
    }
  }

  function link_post_variable($var_name)
  {
    // Map global to POST variable
    if (isset($_POST[$var_name]))
    {
      $GLOBALS[$var_name] =& $_POST[$var_name];
    }
  }
  
  function manufacturer_is_active($id, $is_product_id = true, $is_manufacturer_id = false){
	$response = true;
	if ($is_manufacturer_id){
		$sql = tep_db_query("select manufacturers_status from manufacturers where manufacturers_id='" . (int)$id . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			if ($info['manufacturers_status']!='1'){
				$response = false;
			}
		}
	} elseif ($is_product_id){
		$sql = tep_db_query("select m.manufacturers_status from manufacturers m inner join products p on m.manufacturers_id=p.manufacturers_id where p.products_id='" . (int)$id . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			if ($info['manufacturers_status']!='1'){
				$response = false;
			}
		}		
	}

	return $response;
  }
  

  function get_comparison_with_msrp_map_or_other_response($products_id){
    global $currencies;
    $response = array();
    $price_level_to_compare = null;
    if (ENABLE_PRODUCTS_PRICE_COMPARISON=='True'){
        $compare_with_price_level = PRODUCTS_PRICE_COMPARISON;
        if (!empty($compare_with_price_level)){
            $show_in_percent = PRODUCTS_PRICE_COMPARISON_BY_PERCENTAGE=='true' ? true : false;
            
            $product_query = tep_db_query("select products_price from products where products_id='" . (int)$products_id . "'");
            if (tep_db_num_rows($product_query)){
                $info = tep_db_fetch_array($product_query);
                $products_price = $info['products_price'];
                if ($products_price>0){
                    $price_level_query = tep_db_query("select unit_msrp as msrp, min_acceptable_price as map from products_extended where osc_products_id='" . (int)$products_id . "'");
                    if (tep_db_num_rows($price_level_query)){
                        $price_level_info = tep_db_fetch_array($price_level_query);
                        switch($compare_with_price_level){
                            case 'map':
                                if (!empty($price_level_info['map']) && $price_level_info['map']>0){
                                   $price_level_to_compare = $price_level_info['map']; 
                                }
                                break;
                            case 'msrp':
                                if (!empty($price_level_info['msrp']) && $price_level_info['msrp']>0){
                                   $price_level_to_compare = $price_level_info['msrp']; 
                                }
                                break;
                        }
                    }
                    
                    if (!empty($price_level_to_compare)){
                        if ($products_price < $price_level_to_compare){
                            if ($show_in_percent){
                                $savings = ( ($price_level_to_compare - $products_price) / ( ($products_price + $price_level_to_compare) / 2 ) ) * 100;
                            } else {
                                $savings = $price_level_to_compare - $products_price;
                            }
                            
                            if (!empty($savings)){
                                $savings = number_format($savings, 2);
                                $response[] = $savings;
                                $response[] = '<span style="font-weight:12px;color:red;"><del>' . $currencies->display_price($price_level_to_compare, 0)  . '</del>&nbsp;' . str_replace('{saving}', ($show_in_percent ? $savings : $currencies->display_price($savings, 0) ), PRODUCTS_PRICE_COMPARISON_MESSAGE) . '</span>';
                            }
                        }
                    }
                    
                }
            }
        }
    }
    return $response;
  }
  
	function fire_order_feed_to_obn($orders_id){
		if (AUTO_ORDER_SUBMIT_STATUS=='true'){
			include_once(DIR_FS_ADMIN . 'OBN_order_feed_manager.php');
			
			$suppliers = array();
			$order_product_ids = array();
			$suppliers_prefixes = get_suppliers_prefixes();
			
			$products_query = tep_db_query("select products_id as id, products_model as model from orders_products where orders_id='" . (int)(int)$orders_id . "'");
			while ($entry = tep_db_fetch_array($products_query)){
				$pos = strpos($entry['model'], '-');
				if ($pos!==false){
					$prefix = substr($entry['model'], 0, $pos+1);
					if (in_array($prefix, $suppliers_prefixes)){
						$order_product_ids[] = $entry['id'];
					}
				}
			}
			
			if (!empty($order_product_ids)){
				$order_feed = new order_feed($orders_id, $order_product_ids);
				$order_feed->get_order_feed();
				//test block start
				$file_path = DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/outgoing/';
				$file_name = time() . '.xml';
				$handle = fopen($file_path . $file_name , 'x');
				chmod($file_path . $file_name, 0777);
				fwrite($handle, $order_feed->xml);
				fclose($handle);
				//test block end
				//above block will be commented on going to production mode
				//production block start
				//$order_feed->move_order_feed_to_obn();
				//production block end
				unset($order_feed);
			}
		}
	}
	
	function get_suppliers_prefixes(){
		$suppliers_prefixes = array();
		$retailer_dir = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/';
		if (file_exists($retailer_dir)){
			if ($dh = opendir($retailer_dir)){
				while (($file = readdir($dh)) !== false){
					if ($file=='suppliers.txt'){
						$handle = fopen($retailer_dir . $file, 'r');
						break;
					}
				}
				closedir($dh);
			}

			if ($handle){
				while($supplier_prefix = fgets($handle)){
					$supplier_prefix = trim($supplier_prefix);
					if (!empty($supplier_prefix) && substr($supplier_prefix, -1, 1)=='-'){
						$suppliers_prefixes[] = $supplier_prefix;
					}
				}
				fclose($handle);
			}
		}
		return $suppliers_prefixes;
	}
	
//BOF:fraud_prevention
/*similar functions showing under back-end's functions/general.php */
function fraud_prevention_is_negative($order_id){
    global $like_string;
    if (FRAUD_PREVENTION_FUNCTIONALITY_STATUS=='1'){
        $order_query = tep_db_query("select o.customers_name as name, concat(o.customers_street_address, o.customers_suburb) as customer_address, o.customers_telephone as telephone, concat(o.billing_street_address, o.billing_suburb) as billing_address, concat(o.delivery_street_address, o.delivery_suburb) as shipping_address, o.customers_country as country, o.customers_email_address as email, o.shipping_module, o.ip_address, ot.value as subtotal from orders o left join orders_total ot on (o.orders_id=ot.orders_id and ot.class='ot_subtotal') where o.orders_id='" . (int)$order_id . "'");
        if (tep_db_num_rows($order_query)){
            $order = tep_db_fetch_array($order_query);
            
            $like_string = '';
            $fraud_ship_methods_string = FRAUD_PREVENTION_SHIPPING_METHODS;
            if (!empty($fraud_ship_methods_string)){
                if (!empty($order['shipping_module'])){
                    list($ship_method, ) = explode('_', $order['shipping_module']);
                    if (!empty($ship_method)){
                        $fraud_ship_methods = explode(',', $fraud_ship_methods_string);
                        if (in_array($ship_method, $fraud_ship_methods)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $fraud_countries_string = FRAUD_PREVENTION_COUNTRIES;
            if (!empty($fraud_countries_string)){
                if (!empty($order['country'])){
                    $country_query = tep_db_query("select countries_id as id from countries where countries_name='" . tep_db_input($order['country']) . "'");
                    if (tep_db_num_rows($country_query)){
                        $country = tep_db_fetch_array($country_query);
                        $fraud_countries = explode(',', $fraud_countries_string);
                        if (in_array($country['id'], $fraud_countries)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $fraud_ip_addresses_string = FRAUD_PREVENTION_IP_ADDRESSES;
            if (!empty($fraud_ip_addresses_string)){
                if (!empty($order['ip_address'])){
                    list($op1, $op2, $op3, $op4) = explode('.', $order['ip_address']); 
                    $fraud_ip_addresses = explode(',', $fraud_ip_addresses_string);
                    foreach($fraud_ip_addresses as $ip){
                        list($p1, $p2, $p3, $p4) = explode('.', $ip);
                        if ($p1==$op1 || $p1=='*'){
                            if ($p2==$op2 || $p2=='*'){
                                if ($p3==$op3 || $p3=='*'){
                                    if ($p4==$op4 || $p4=='*'){
                                        return false;
                                        break;
                                    }
                                }
                            }
                        } 
                    }
                }
            }
            
            $like_string = '';
            $fraud_customer_names_string = FRAUD_PREVENTION_CUSTOMER_NAMES;
            if (!empty($fraud_customer_names_string)){
                if (!empty($order['name'])){
                    $like_string = '';

                    
                    $fraud_customer_names = explode(',', $fraud_customer_names_string);
                    array_walk($fraud_customer_names, 'set_query_names_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $custmer_names_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($custmer_names_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
            $like_string = '';
            $fraud_address_string = FRAUD_PREVENTION_ADDRESSES;
            if (!empty($fraud_address_string)){
                if (!empty($order['customer_address'])){
                    $like_string = '';

                    $fraud_adresses = explode(',', $fraud_address_string);
                    array_walk($fraud_adresses, 'set_query_addresses_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $addresses_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($addresses_query)){
                            return false;
                        }
                    }
                }
            }
            
            $like_string = '';
            $dollar_value = FRAUD_PREVENTION_DOLLAR_VALUE;
            if (!empty($dollar_value)){
                if (!empty($order['subtotal'])){
                    if ($order['subtotal']>$dollar_value){
                        return false;
                    }
                }
            }
            
            $like_string = '';
            $fraud_email_addresses_string = FRAUD_PREVENTION_EMAIL_ADDRESSES;
            if (!empty($fraud_email_addresses_string)){
                if (!empty($order['email'])){
                    $fraud_email_addresses = explode(',', $fraud_email_addresses_string);
                    array_walk($fraud_email_addresses, 'set_query_emails_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $emails_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($emails_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
            $like_string = '';
            $check_address_mismatch = FRAUD_PREVENTION_SHIPPING_BILLING_MISMATCH;
            if ($check_address_mismatch=='1'){
                if ($order['billing_address']!=$order['shipping_address']){
                    return false;
                }
            }
            
            $like_string = '';
            $fraud_telephone_numbers_string = FRAUD_PREVENTION_PHONE_NUMBERS;
            if (!empty($fraud_telephone_numbers_string)){
                if (!empty($order['telephone'])){
                    $like_string = '';

                    
                    $fraud_telephone_numbers = explode(',', $fraud_telephone_numbers_string);
                    array_walk($fraud_telephone_numbers, 'set_query_telephone_numbers_compatible');
                    if (!empty($like_string)){
                        $like_string = substr($like_string, 0, -4);
                        $custmer_names_query = tep_db_query("select orders_id from orders where orders_id='" . (int)$order_id . "' and (" . $like_string . ")");
                        if (tep_db_num_rows($custmer_names_query)){
                            return false;
                        }
                    }
                    
                }
            }
            
        }
    }
    return true;
}

function set_query_names_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_name like '%" . $val . "%' or ";
}

function set_query_addresses_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_street_address like '%" . $val . "%' or customers_suburb like '%" . $val . "%' or ";
}

function set_query_emails_compatible(&$val){
	global $like_string;
	$like_string .= " customers_email_address='" . $val . "' or ";
}

function set_query_telephone_numbers_compatible(&$val){
	global $like_string;
	
	$like_string .= " customers_telephone='" . $val . "' or ";
}
//EOF:fraud_prevention

  function getDistinctSpecifications($category_filter = '', $manufacturers_id = ''){
	$response = array();
	if ($category_filter == '' && $manufacturers_id == '') return false;
	//$specs_query = tep_db_query("select pa.options_id as specification_id, po.products_options_name as specification from products_attributes pa inner join products_options po on (pa.options_id=po.products_options_id and po.language_id='1') where (pa.options_values_id is null or pa.options_values_id <=0) group by pa.options_id order by po.products_options_name");
	if (strrpos($category_filter, '_')!==false){
	   $category_filter = substr($category_filter, strrpos($category_filter, '_')+1 );
	}
	//$specs_query = tep_db_query("select pa.options_id as specification_id, po.products_options_name as specification from products_attributes pa inner join products_options po on (pa.options_id=po.products_options_id and po.language_id='1') " . (!empty($category_filter) ? " inner join products_to_categories p2c on pa.products_id=p2c.products_id " : "") . " where (pa.options_values_id is null or pa.options_values_id <=0) " . (!empty($category_filter) ? " and p2c.categories_id='" . (int)$category_filter . "' " : "") . " group by pa.options_id order by po.products_options_name");
        $specs_query = tep_db_query("select pa.options_id as specification_id, po.products_options_name as specification from products_attributes pa inner join products_options po on (pa.options_id=po.products_options_id) " . (!empty($category_filter) ? " inner join products_to_categories p2c on pa.products_id=p2c.products_id " : "") . (!empty($manufacturers_id) ? " inner join products p on pa.products_id=p.products_id inner join manufacturers m on p.manufacturers_id=m.manufacturers_id " : "") . " where " . (!empty($category_filter) ? "  p2c.categories_id='" . (int)$category_filter . "' " : "") . (!empty($manufacturers_id) ? " and m.manufacturers_id='" . (int)$manufacturers_id . "' " : "") . " group by pa.options_id order by po.products_options_name");
       
	while($entry = tep_db_fetch_array($specs_query)){
	
		//$values_query = tep_db_query("select pa.options_values_id as value_id, pov.products_options_values_name as value_name from products_attributes pa inner join products_options_values pov on (pa.options_values_id=pov.products_options_values_id and pov.language_id='1' ) where pa.options_id='" . (int)$entry['specification_id'] . "' group by pa.options_values_id limit 0, 10");
            //$values_query = tep_db_query("select pa.options_values_id as value_id, pov.products_options_values_name as value_name from products_attributes pa inner join products_options_values pov on (pa.options_values_id=pov.products_options_values_id and pov.language_id='1') " . (!empty($category_filter) ? " inner join products_to_categories p2c on pa.products_id=p2c.products_id " : "") . " where pa.options_id='" . (int)$entry['specification_id'] . "' " . (!empty($category_filter) ? " and p2c.categories_id='" . (int)$category_filter . "' " : "") . " group by pa.options_values_id");
            $values_query = tep_db_query("select pa.options_values_id as value_id, pov.products_options_values_name as value_name from products_attributes pa inner join products_options_values pov on (pa.options_values_id=pov.products_options_values_id and pov.language_id='1') " . (!empty($category_filter) ? " inner join products_to_categories p2c on pa.products_id=p2c.products_id " : "") . (!empty($manufacturers_id) ? " inner join products p on pa.products_id=p.products_id inner join manufacturers m on p.manufacturers_id=m.manufacturers_id " : "") . " where pa.options_id='" . (int)$entry['specification_id'] . "' " . (!empty($category_filter) ? " and p2c.categories_id='" . (int)$category_filter . "' " : "") . (!empty($manufacturers_id) ? " and m.manufacturers_id='" . (int)$manufacturers_id . "' " : "") . " group by pa.options_values_id");
		
		if (tep_db_num_rows($values_query)){
			$values = array();
			
			while($value = tep_db_fetch_array($values_query)){
				$values[$value['value_name']] = $value['value_id'];
			}
			
			$response[$entry['specification']] = array(
				'id' => $entry['specification_id'], 
				'values' => $values,
			);
		}
	}
	return $response;
  }

//function created for registering authorization output for authorize.net and fastcharge. after successfull integration comment function's code
function debug_register_order_authorization_details($order_id, $details){
	tep_db_query("insert into debug_order_authorization (orders_id, transaction_details) values ('" . (int)$order_id . "', '" . tep_db_prepare_input($details) . "')");
}  

function get_parent_product_id($child_product_id = ''){
	$parent_product_id = null;
	if ($child_product_id){
		$sql = tep_db_query("select p1.products_id from products p1 inner join products p2 on p1.products_model=p2.parent_products_model where p2.products_id='" . (int)$child_product_id . "'");
		if (tep_db_num_rows($sql)){
			$info = tep_db_fetch_array($sql);
			$parent_product_id = (int)$info['products_id'];
		}
	}
	return $parent_product_id;
}

function print_array ($array, $exit = false) {
    //print "<pre>";
    //print_r ($array);
    //print "</pre>";
    //if ($exit) exit();
}

?>
