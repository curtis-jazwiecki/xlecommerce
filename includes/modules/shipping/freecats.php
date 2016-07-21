<?php
/*
  $Id$ freeshipping.php 2
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

*/

  class freecats {
    var $code, $title, $description, $icon, $enabled;

// class constructor
    function freecats() {
      global $order, $customer;

      $this->code = 'freecats';
      $this->title = MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE;
      $this->description = MODULE_SHIPPING_FREE_PER_CATS_TEXT_DESCRIPTION;
      $this->sort_order = MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER;
      $this->icon ='';
      $this->enabled = ((MODULE_SHIPPING_FREE_PER_CATS_STATUS == 'True') ? true : false);

			if (!tep_not_null(MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES))
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
     }
}

// class methods
    function quote($method = '') {
      global $cart, $shipping_weight, $order;
      	  $is_free = false;
		  $get_weight = false;
		  $title = '';
      if ($shipping_weight > MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX && MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX > 0) {
		
		$this->enabled = false;
		return false;	
		/*
		if (MODULE_SHIPPING_FREE_PER_CATS_DISPLAY == 'True'){
			    	  	$this->quotes['error'] = '<b>'.MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE.'</b><br />'.sprintf(MODULE_SHIPPING_FREE_PER_CATS_TEXT_TO_WEIGHT, $cat_names);
					} else {
					$this->enabled = false;
				    return false;	
					}
				  */		
			 $get_weight = false;
		  } else {
		   		$get_weight = true;
		  }
	
		  		  
    if (($order->info['total'] >= MODULE_SHIPPING_FREE_SHIPPING_OVER) && MODULE_SHIPPING_FREE_SHIPPING_OVER > 0 ) {
     $is_free = true;	     
	  } else {	
	  	$pID_list = $cart->get_product_id_list();
        $pID_list = explode(',',$pID_list);
        for($i=0, $x=sizeof($pID_list); $i<$x; $i++){
			$pID_list[$i] = (int)$pID_list[$i];
		}
		  $pID_list = implode(',',$pID_list);

	  	if (MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS == 'True') {
			$check_query = tep_db_query("select * from products where free_shipping='0' and products_id in (".$pID_list.")");
		    if (tep_db_num_rows($check_query))
		       $is_free = false;
		    else 
		        $is_free = true;
		  }	
         
         if (!$is_free) {
			$cats_array = explode(',',MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES);
			$cat_names = '';
			
			for($i=0, $x=sizeof($cats_array); $i<$x; $i++){
			     $cats_array[$i] = (int)$cats_array[$i];
           //$cat_names .= $this->tep_get_categories_name($cats_array[$i]).', ';
			}
			
			//$cat_names = substr($cat_names, 0,-2);

			 if ( MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY == 'Only' ){
					$check_query = tep_db_query('select * from '.TABLE_PRODUCTS_TO_CATEGORIES.' where categories_id not in ('.MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES.') and products_id in ('.$pID_list.')');
		      if (tep_db_num_rows($check_query) <= 0)
				$is_free = true;
			 } else {
			$check_query = tep_db_query('select * from '.TABLE_PRODUCTS_TO_CATEGORIES.' where categories_id in ('.MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES.') and products_id in ('.$pID_list.')');
		      if (tep_db_num_rows($check_query) > 0)
				$is_free = true;	
			   }
			 //$method_title = sprintf(MODULE_SHIPPING_FREE_PER_CATS_TEXT_WAY, $cat_names);
           }

		  if (!$is_free) {
		  	$this->enabled = false;
				return false;
				/*
				if (MODULE_SHIPPING_FREE_PER_CATS_DISPLAY == 'True'){
					    if ( MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY == 'Only' )
								$this->quotes['error'] = '<b>'.MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE.'</b><br />'.sprintf(MODULE_SHIPPING_FREE_PER_CATS_TEXT_ERROR_ONE_ONLY, $cat_names);
							else
								$this->quotes['error'] = '<b>'.MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE.'</b><br />'.sprintf(MODULE_SHIPPING_FREE_PER_CATS_TEXT_ERROR_ONE_ANY, $cat_names);
					
			   } else {
				$this->enabled = false;
				return false;
			}
			*/
	     }
      }


	if (($is_free && $get_weight))
	{
		$this->quotes = array('id' => $this->code,
							'module' => MODULE_SHIPPING_FREE_PER_CATS_TEXT_TITLE,
							'methods' => array(array('id' => $this->code,
												'title' => $title,
												'cost' => MODULE_SHIPPING_FREE_PER_CATS_COST)));
	}


	  if (tep_not_null($this->icon)) $this->quotes['icon'] = tep_image($this->icon, $this->title);

	  return $this->quotes;

	}

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_SHIPPING_FREE_PER_CATS_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Free Shipping', 'MODULE_SHIPPING_FREE_PER_CATS_STATUS', 'False', 'Do you want to offer free shipping?', '6', '1', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
       tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Free Shipping for individual products', 'MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS', 'False', 'Do you want to enable free shipping for products marked free shipping?', '6', '2', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
            tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, date_added) values ('Free Shipping For Orders Over', 'MODULE_SHIPPING_FREE_SHIPPING_OVER', '50', 'Provide free shipping for orders over the set amount.(0 to turn off, 0.1 for all orders)', '6', '3', 'currencies->format', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Maximum Weight', 'MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX', '10', 'What is the maximum weight you will ship? (zero to turn off)', '667', '8', now())");
     // tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable Display', 'MODULE_SHIPPING_FREE_PER_CATS_DISPLAY', 'True', 'Do you want to display text if products from needed categories not purchased?', '667', '7', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Categories List', 'MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES', '', 'For what categories do you want to offer free shipping?<br />NOTE! not recurcive - select all subcategories if you need it.', '667', '8', 'tep_cfg_show_multicategories', 'tep_cfg_select_multicategories(', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Only or Any', 'MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY', 'Only', 'Do you want to offer a free shipping for orders with products only from mentioned categories, or with products from any categories (including mentioned)?', '667', '7', 'tep_cfg_select_option(array(\'Only\', \'Any\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER', '0', 'Sort order of display.', '667', '0', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added) values ('Shipping Zone', 'MODULE_SHIPPING_FREE_PER_CATS_ZONE', '0', 'If a zone is selected, only enable this shipping method for that zone.', '667', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now())");

   }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }

   function keys() {
     $keys = array(
           'MODULE_SHIPPING_FREE_PER_CATS_STATUS',
           'MODULE_SHIPPING_FREE_PER_PRODUCTS_STATUS',
           'MODULE_SHIPPING_FREE_SHIPPING_OVER',
           'MODULE_SHIPPING_FREE_PER_CATS_WEIGHT_MAX',
           'MODULE_SHIPPING_FREE_PER_CATS_SORT_ORDER',
         //  'MODULE_SHIPPING_FREE_PER_CATS_DISPLAY',
           'MODULE_SHIPPING_FREE_PER_CATS_ONLY_OR_ANY',
           'MODULE_SHIPPING_FREE_PER_CATS_CATEGORIES',
           'MODULE_SHIPPING_FREE_PER_CATS_ZONE'
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