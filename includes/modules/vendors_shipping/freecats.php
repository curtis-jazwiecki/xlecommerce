<?php
/*
  $Id$ freeshipping.php 2

*/

  class freecats {
    var $code, $title, $description, $icon, $enabled;

// class constructor
    function freecats() {
      global $order, $customer, $vendors_id;

      $this->code = 'freecats';
      $this->title = MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FREE_PER_CATS_TEXT_DESCRIPTION;
      //$this->sort_order = MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER;
      $this->icon ='';
      //$this->enabled = ((MODULE_SHIPPING_FREE_PER_CATS_STATUS == 'True') ? true : false);

		/*	if (!tep_not_null(MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES))
			      $this->enabled = false;

      if ( ($this->enabled == true) && ((int)MODULE_SHIPPING_FREE_PER_CATS_ZONE > 0) ) {

        $check_flag = false;
        $check_query = tep_db_query("select zone_id, zone_country_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_FREE_PER_CATS_ZONE . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
        $order_shipping_country = $order->delivery['country']['id'];

        while ($check = tep_db_fetch_array($check_query)) {

          if ($check['zone_id'] < 1) {
            $check_flag = true;
            break;
          } elseif ($check['zone_country_id'] == $order->delivery['country']['id']) {
            $check_flag = true;
            break;
          }
        }
        if ($check_flag == false) {
          $this->enabled = false;
        }
     }*/
}

    function sort_order($vendors_id){
		$sort_order = @ constant('MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER_' . $vendors_id);
		if (isset ($sort_order)) {
			$this->sort_order = $sort_order;
		} else {
			$this->sort_order = '-';
		}
		return $this->sort_order;
	}

    function enabled($vendors_id){
        global $order;
        $this->enabled = (( @constant('MODULE_SHIPPING_FREE_PER_CATS_STATUS_' . $vendors_id) == 'True') ? true : false);
        
        if (!tep_not_null( @constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id ))) $this->enabled = false;

        if ( ($this->enabled == true) && ((int) @constant('MODULE_SHIPPING_FREE_PER_CATS_ZONE_' . $vendors_id) > 0) ) {

            $check_flag = false;
            $check_query = tep_db_query("select zone_id, zone_country_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int)@constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id ) . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
            $order_shipping_country = $order->delivery['country']['id'];

            while ($check = tep_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_country_id'] == $order->delivery['country']['id']) {
                    $check_flag = true;
                    break;
                }
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
        return $this->enabled;
    }
    
    function zones($vendors_id){
        global $order;

        if ( ($this->enabled == true) && ((int) @constant('MODULE_SHIPPING_FREE_PER_CATS_ZONE_' . $vendors_id) > 0) ) {

            $check_flag = false;
            $check_query = tep_db_query("select zone_id, zone_country_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int)@constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id ) . "' and zone_country_id = '" . $order->delivery['country']['id'] . "' order by zone_id");
            $order_shipping_country = $order->delivery['country']['id'];

            while ($check = tep_db_fetch_array($check_query)) {
                if ($check['zone_id'] < 1) {
                    $check_flag = true;
                    break;
                } elseif ($check['zone_country_id'] == $order->delivery['country']['id']) {
                    $check_flag = true;
                    break;
                }
            }
            if ($check_flag == false) {
                $this->enabled = false;
            }
        }
        return $this->enabled;
    }

    // class methods
    function quote($method = '', $module='', $vendors_id='') {
        global $cart, $shipping_weight, $order;
        
        $is_free = false;
        $get_weight = false;
        $title = '';
        if ($shipping_weight > @constant('MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX_' . $vendors_id) && @constant('MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX_' . $vendors_id) > 0) {
		
            $this->enabled = false;
            return false;	
	
            $get_weight = false;
        } else {
            $get_weight = true;
        }
	
		  		  
        if (($order->info['total'] >= @constant('MODULE_SHIPPING_FREE_SHIPPING_OVER_' . $vendors_id) ) && @constant('MODULE_SHIPPING_FREE_SHIPPING_OVER_' . $vendors_id) > 0 ) {
            $is_free = true;	     
        } else {	
            $pID_list = $cart->get_product_id_list();
            $pID_list = explode(',',$pID_list);
            for($i=0, $x=sizeof($pID_list); $i<$x; $i++){
                $pID_list[$i] = (int)$pID_list[$i];
            }
            $pID_list = implode(',',$pID_list);

            if ( @constant('MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS_' . $vendors_id) == 'True') {
                $check_query = tep_db_query("select * from products where free_shipping='0' and products_id in (".$pID_list.")");
                if (tep_db_num_rows($check_query))
                    $is_free = false;
                else 
                    $is_free = true;
            }	
         
            if (!$is_free) {
                $cats_array = explode(',', @constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id) );
                $cat_names = '';
			
                for($i=0, $x=sizeof($cats_array); $i<$x; $i++){
                    $cats_array[$i] = (int)$cats_array[$i];
                }
			
                if ( @constant('MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY_' . $vendors_id) == 'Only' ){
					$check_query = tep_db_query('select * from '.TABLE_PRODUCTS_TO_CATEGORIES.' where categories_id not in ('. @constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id) . ') and products_id in ('.$pID_list.')');
                    if (tep_db_num_rows($check_query) <= 0)
                        $is_free = true;
                } else {
                    $check_query = tep_db_query('select * from '.TABLE_PRODUCTS_TO_CATEGORIES.' where categories_id in (' . @constant('MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id) . ') and products_id in ('.$pID_list.')');
                    if (tep_db_num_rows($check_query) > 0)
                        $is_free = true;	
                }
            }

            if (!$is_free) {
                $this->enabled = false;
                return false;
            }
        }


        if (($is_free && $get_weight)){
            $this->quotes = array(
                'id' => $this->code,
                'module' => @constant('MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE_' . $vendors_id),
							'methods' => array(
                                array(
                                    'id' => $this->code,
                                    'title' => $this->title,
                                    //'cost' => @constant('MODULE_SHIPPING_FREE_PER_CATS_COST_' . $vendors_id)
                                    'cost' => MODULE_SHIPPING_FREE_PER_CATS_COST
                                )
                            )
            );
        }


        if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);
        return $this->quotes;
    }

    function check($vendors_id='1') {
        if (!isset($this->_check)) {
            $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREE_PER_CATS_STATUS_" . $vendors_id . "'");
        $this->_check = tep_db_num_rows($check_query);
        }
        return $this->_check;
    }

    function install($vendors_id='1') {
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Free Shipping', 'MODULE_SHIPPING_FREE_PER_CATS_STATUS_" . $vendors_id . "', 'False', 'Do you want to offer free shipping?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Enable Free Shipping for individual products', 'MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS_" . $vendors_id . "', 'False', 'Do you want to enable free shipping for products marked free shipping?', '6', '2', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added, vendors_id) values ('Free Shipping For Orders Over', 'MODULE_SHIPPING_FREE_SHIPPING_OVER_" . $vendors_id . "', '50', 'Provide free shipping for orders over the set amount.(0 to turn off, 0.1 for all orders)', '6', '3', 'currencies->format', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Maximum Weight', 'MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX', '10', 'What is the maximum weight you will ship? (zero to turn off)', '667', '8', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Categories List', 'MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_" . $vendors_id . "', '', 'For what categories do you want to offer free shipping?<br />NOTE! not recurcive - select all subcategories if you need it.', '667', '8', 'tep_cfg_show_multicategories', 'tep_cfg_select_multicategories(', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('Only or Any', 'MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY_" . $vendors_id . "', 'Only', 'Do you want to offer a free shipping for orders with products only from mentioned categories, or with products from any categories (including mentioned)?', '667', '7', 'tep_cfg_select_option(array(\'Only\', \'Any\'), ', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '667', '0', now(), '" . $vendors_id . "')");
        tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_FREE_PER_CATS_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '667', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");

   }

    function remove($vendors_id) {
        tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");
    }

    function keys($vendors_id) {
        $keys = array(
           'MODULE_SHIPPING_FREE_PER_CATS_STATUS_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS_' . $vendors_id,
           'MODULE_SHIPPING_FREE_SHIPPING_OVER_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES_' . $vendors_id,
           'MODULE_SHIPPING_FREE_PER_CATS_ZONE_' . $vendors_id,
        );
        return $keys;
    }
    
    function tep_get_categories_name($cID, $language = ''){
        global $languages_id;
        if (!tep_not_null($language))
            $language = $languages_id;

            $cname = tep_db_fetch_array(tep_db_query('select categories_name from '.TABLE_CATEGORIES_DESCRIPTION.' where categories_id="'.$cID.'" and language_id="'.$language.'"'));

            return  $cname['categories_name'];
    }
 }
?>