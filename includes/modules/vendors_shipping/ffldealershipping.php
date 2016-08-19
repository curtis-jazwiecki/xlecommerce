<?php
/*
  $Id: ffl_dealer_shipping.php,v 1.27 2003/02/05 22:41:52 hpdl Exp $
  Modified for MVS V1.0 2016/03/18 EMM
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class ffldealershipping {

    var $code, $title, $description, $icon, $enabled, $vendors_id, $sort_order; //multi vendor

    // class constructor

    function __construct() {

      global $order, $vendors_id;

      //MVS

      $this->code = 'ffldealershipping';

      $this->title = MODULE_SHIPPING_FFL_DEALER_TEXT_TITLE;

      if($this->enabled($vendors_id)){
	  	$this->description = MODULE_SHIPPING_FFL_DEALER_TEXT_DESCRIPTION.' <br><br> <a href="ffl_dealers.php?vendors_id='.$vendors_id.'"><b>'.MODULE_SHIPPING_FFL_DEALER_TEXT_UPLOAD.'</b></a>';
	  }
	  

      $this->icon = '';

      $this->delivery_country_id = $order->delivery['country']['id'];

      $this->delivery_zone_id = $order->delivery['zone_id'];

    }



    //MVS start

    function sort_order($vendors_id = '1') {

      $sort_order = @ constant('MODULE_SHIPPING_FFL_DEALER_SORT_ORDER_' . $vendors_id);

      if (isset ($sort_order)) {

        $this->sort_order = $sort_order;

      } else {

        $this->sort_order = '-';

      }

      return $this->sort_order;

    }

    function tax_class($vendors_id = '1') {

      $this->tax_class = constant('MODULE_SHIPPING_FFL_DEALER_TAX_CLASS_' . $vendors_id);

      return $this->tax_class;

    }

    function enabled($vendors_id = '1') {

      $this->enabled = false;

      $status = @ constant('MODULE_SHIPPING_FFL_DEALER_STATUS_' . $vendors_id);

      if (isset ($status) && $status != '') {

        $this->enabled = (($status == 'True') ? true : false);

      }

      if (($this->enabled == true) && ((int) constant('MODULE_SHIPPING_FFL_DEALER_ZONE_' . $vendors_id) > 0)) {

        $check_flag = false;

        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . (int) constant('MODULE_SHIPPING_FFL_DEALER_ZONE_' . $vendors_id) . "' and zone_country_id = '" . $this->delivery_country_id . "' order by zone_id");

        while ($check = tep_db_fetch_array($check_query)) {

          if ($check['zone_id'] < 1) {

            $check_flag = true;

            break;

          }

          elseif ($check['zone_id'] == $this->delivery_zone_id) {

            $check_flag = true;

            break;

          }

        }

        if ($check_flag == false) {

          $this->enabled = false;

        } //if

      } //if

      return $this->enabled;

    }



    function zones($vendors_id = '1') {

      if (($this->enabled == true) && ((int) @ constant('MODULE_SHIPPING_FFL_DEALER_ZONE_' . $vendors_id) > 0)) {

        $check_flag = false;

        $check_query = tep_db_query("select zone_id from " . TABLE_ZONES_TO_GEO_ZONES . " where geo_zone_id = '" . MODULE_SHIPPING_ITEM_ZONE . "' and zone_country_id = '" . $this->delivery_zone_id . "' order by zone_id");

        while ($check = tep_db_fetch_array($check_query)) {

          if ($check['zone_id'] < 1) {

            $check_flag = true;

            break;

          }elseif ($check['zone_id'] == $this->delivery_zone_id) {

            $check_flag = true;

            break;

          } //if

        } //while

        if ($check_flag == false) {

          $this->enabled = false;

        } //if

      } //if

      return $this->enabled;

    } //function

	//MVS End

    //Get a quote

    function quote($method = '', $module = '', $vendors_id = '1', $products_qty_for_vendor = null) {

      global $HTTP_POST_VARS, $shipping_weight, $order, $cart, $shipping_num_boxes;

      switch (@constant('MODULE_SHIPPING_FFL_DEALER_MODE_' . $vendors_id) ) {

        case 'price':

          $order_total = $cart->vendor_shipping[$vendors_id]['cost'];

          break;

        case 'weight':

          $order_total = $shipping_weight;//$cart->vendor_shipping[$vendors_id]['weight'];

          break;

        case 'quantity':

          $order_total = $cart->vendor_shipping[$vendors_id]['qty'];
		  
		  break;

      }

      //$table_cost = explode("[:,]", @ constant('MODULE_SHIPPING_FFL_DEALER_COST_' . $vendors_id));
      //$table_cost = explode(",", @ constant('MODULE_SHIPPING_FFL_DEALER_COST_' . $vendors_id));
	  
	  $table_cost = preg_split("/[:,]/" , @ constant('MODULE_SHIPPING_FFL_DEALER_COST_' . $vendors_id));

      $size = sizeof($table_cost);
		
      for ($i = 0, $n = $size; $i < $n; $i += 2) {
      //for ( $i=0; $i < $size; $i ++ ) {
		
        if ($order_total <= $table_cost[$i]) {

          $pos = strpos($table_cost[$i +1], '%');

          if ($pos === false) {

            $shipping = $table_cost[$i +1];

          } else {

            $shipping_cost_temp = split("%", $table_cost[$i +1]);

            $shipping = $order_total * $shipping_cost_temp[0] / 100;

          }

          break;

        }

      }

      if (@ constant('MODULE_SHIPPING_FFL_DEALER_MODE_' . $vendors_id) == 'weight') {

        $shipping = $shipping * $shipping_num_boxes;

      }

      $handling = @constant('MODULE_SHIPPING_FFL_DEALER_HANDLING_' . $vendors_id);

      $this->quotes = array (

        'id' => $this->code,

        'module' => MODULE_SHIPPING_FFL_DEALER_TEXT_TITLE,

        'methods' => array (

          array (

            'id' => $this->code,

            'title' => MODULE_SHIPPING_FFL_DEALER_TEXT_WAY,

            'cost' => $shipping + $handling

          )

        )

      );

      //    $this->tax_class = constant(MODULE_SHIPPING_TABLE_TAX_CLASS_ . $vendors_id);

      if ($this->tax_class($vendors_id) > 0) {

        $this->quotes['tax'] = tep_get_tax_rate($this->tax_class($vendors_id), $order->delivery['country']['id'], $order->delivery['zone_id']);

      }

      if (tep_not_null($this->icon)){

        $this->quotes['icon'] = tep_image($this->icon, $this->title);
	  
	  }

      return $this->quotes;

    }

    function check($vendors_id = '1') {

      if (!isset ($this->_check)) {

        //multi vendor add  "vendors_id = '". $vendors_id ."' and"

        $check_query = tep_db_query("select configuration_value from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key = 'MODULE_SHIPPING_FFL_DEALER_STATUS_" . $vendors_id . "'");

        $this->_check = tep_db_num_rows($check_query);

      }

      return $this->_check;

    } /////VID



    function install($vendors_id) {

      //multi vendor add 'vendors_id' to field names and '" . $vendors_id . "', to values

      // $vendors_id = $vendors_id;

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) VALUES ('Enable FFL DEALER Method', 'MODULE_SHIPPING_FFL_DEALER_STATUS_" . $vendors_id . "', 'True', 'Do you want to offer ffl dealer shipping rate shipping?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now(), '" . $vendors_id . "')");
	  
	  tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Shipping FFL DEALER', 'MODULE_SHIPPING_FFL_DEALER_COST_" . $vendors_id . "', '25:8.50,50:5.50,10000:0.00', 'The shipping cost is based on the total cost or weight of items. Example: 25:8.50,50:5.50,etc.. Up to 25 charge 8.50, from there to 50 charge 5.50, etc', '6', '0', now(), '" . $vendors_id . "')");

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added, vendors_id) values ('FFL DEALER Method', 'MODULE_SHIPPING_FFL_DEALER_MODE_" . $vendors_id . "', 'weight', 'The shipping cost is based on the order total or the total weight of the items ordered.', '6', '0', 'tep_cfg_select_option(array(\'weight\', \'price\', \'quantity\'), ', now(), '" . $vendors_id . "')");

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Handling Fee', 'MODULE_SHIPPING_FFL_DEALER_HANDLING_" . $vendors_id . "', '0', 'Handling fee for this shipping method.', '6', '0', now(), '" . $vendors_id . "')");

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Tax Class', 'MODULE_SHIPPING_FFL_DEALER_TAX_CLASS_" . $vendors_id . "', '0', 'Use the following tax class on the shipping fee.', '6', '0', 'tep_get_tax_class_title', 'tep_cfg_pull_down_tax_classes(', now(), '" . $vendors_id . "')");

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, use_function, set_function, date_added, vendors_id) values ('Shipping Zone', 'MODULE_SHIPPING_FFL_DEALER_ZONE_" . $vendors_id . "', '0', 'If a zone is selected, only enable this shipping method for that zone.', '6', '0', 'tep_get_zone_class_title', 'tep_cfg_pull_down_zone_classes(', now(), '" . $vendors_id . "')");

      tep_db_query("insert into " . TABLE_VENDOR_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added, vendors_id) values ('Sort Order', 'MODULE_SHIPPING_FFL_DEALER_SORT_ORDER_" . $vendors_id . "', '0', 'Sort order of display.', '6', '0', now(), '" . $vendors_id . "')");

    }

    function remove($vendors_id) {

      tep_db_query("delete from " . TABLE_VENDOR_CONFIGURATION . " where vendors_id = '" . $vendors_id . "' and configuration_key in ('" . implode("', '", $this->keys($vendors_id)) . "')");

    }

	function keys($vendors_id) {
	
	  return array (
	
		'MODULE_SHIPPING_FFL_DEALER_STATUS_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_COST_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_MODE_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_HANDLING_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_TAX_CLASS_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_ZONE_' . $vendors_id,
	
		'MODULE_SHIPPING_FFL_DEALER_SORT_ORDER_' . $vendors_id
	
	  );
	
	}
}
?>