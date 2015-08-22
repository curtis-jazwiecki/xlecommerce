<?php

/*

  $Id: vendor_shipping.php,v 1.5 2009/02/28 kymation Exp $

  $Modified_from: shipping.php,v 1.23 2003/06/29 11:22:05 hpdl Exp $

  $Loc: /catalog/includes/classes/ $

  $Mod: MVS V1.2 2009/02/28 JCK/CWG $

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License

*/

class shipping {

	var $modules;

	////

	// Find all of the modules and instantiate the module classes

    function shipping($module = '') {

		global $language, $PHP_SELF, $order, $cart;

		$installed_modules_array = array();

		$store_pickup = false;

		$products = $cart->get_products();

        for ($i=0, $n=sizeof($products); $i<$n; $i++) {

        	if ($products[$i]['in_store_pickup'] == '1') {

				$store_pickup = true;

				break;

			}

		}

		if ($store_pickup) {

			$vendors_data_query = tep_db_query("select vendors_id from " . TABLE_VENDORS);

			while ($vendors_data = tep_db_fetch_array($vendors_data_query)){

				$vendors_id = $vendors_data['vendors_id'];

				$this->modules[$vendors_id][] =	 'spu.php';

			}

		} else {

			// BOF Separate Pricing Per Customer, next line original code

			global $customer_id;

			if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {

				$customer_group_id = $_SESSION['sppc_customer_group_id'];

			} else {

				$customer_group_id = '0';

			}

			$customer_shipment_query = tep_db_query("select IF(c.customers_shipment_allowed <> '', c.customers_shipment_allowed, cg.group_shipment_allowed) as shipment_allowed from " . TABLE_CUSTOMERS . " c, " . TABLE_CUSTOMERS_GROUPS . " cg where c.customers_id = '" . $customer_id . "' and cg.customers_group_id =  '" . $customer_group_id . "'");

			if ($customer_shipment = tep_db_fetch_array($customer_shipment_query)  ) {

				if (tep_not_null($customer_shipment['shipment_allowed']) ) {

					$temp_shipment_array = explode(';', $customer_shipment['shipment_allowed']);

					$vendors_data_query = tep_db_query("select vendors_id from " . TABLE_VENDORS);

					while ($vendors_data = tep_db_fetch_array($vendors_data_query)){

						$vendors_id = $vendors_data['vendors_id'];

						$installed_modules = @constant ('MODULE_VENDOR_SHIPPING_INSTALLED_' . $vendors_id);

						for ($n = 0; $n < sizeof($installed_modules) ; $n++) {

							// check to see if a shipping module is not de-installed

							if ( in_array($installed_modules[$n], $temp_shipment_array ) ) {

								$shipment_array[] = $installed_modules[$n];

							}

						} // end for loop

						$this->modules[$vendors_id] = $shipment_array;

					}

				} else {

					$vendors_data_query = tep_db_query("select vendors_id from " . TABLE_VENDORS);

					while ($vendors_data = tep_db_fetch_array($vendors_data_query)){

						$vendors_id = $vendors_data['vendors_id'];

						$installed_modules = @constant ('MODULE_VENDOR_SHIPPING_INSTALLED_' . $vendors_id);

						if (isset ($installed_modules) && tep_not_null ($installed_modules)) {

							$modules_array = explode(';', $installed_modules);

							$this->modules[$vendors_id] = $modules_array;

							foreach ($modules_array as $module_name) {

								//if the module is not already in the array, add it in

								if (!in_array ($module_name, $installed_modules_array)) {  

									$installed_modules_array[] = $module_name;

								}//if !in_array

							}//foreach

						}

					}

				}

			} else {

				$vendors_data_query = tep_db_query("select vendors_id from " . TABLE_VENDORS);

				while ($vendors_data = tep_db_fetch_array($vendors_data_query)){

					$vendors_id = $vendors_data['vendors_id'];

					$installed_modules = @constant ('MODULE_VENDOR_SHIPPING_INSTALLED_' . $vendors_id);

					if (isset ($installed_modules) && tep_not_null ($installed_modules)) {

						$modules_array = explode(';', $installed_modules);

						$this->modules[$vendors_id] = $modules_array;

						foreach ($modules_array as $module_name) {

							//if the module is not already in the array, add it in

							if (!in_array ($module_name, $installed_modules_array)) {  

								$installed_modules_array[] = $module_name;

							}//if !in_array

						}//foreach

					}

				}

			}

			// EOF Separate Pricing Per Customer

		}

		$include_modules = array();

		//if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $modules_array)) ) {

                if ( (tep_not_null($module)) && (in_array(substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1)), $installed_modules_array)) ) {

			$include_modules[] = array(

				'class' => substr($module['id'], 0, strpos($module['id'], '_')), 

				'file' => substr($module['id'], 0, strpos($module['id'], '_')) . '.' . substr($PHP_SELF, (strrpos($PHP_SELF, '.')+1))

			);

		} else {

			//reset($modules_array);

                    reset($installed_modules_array);

			foreach ($installed_modules_array as $value) {

				$class = substr($value, 0, strrpos($value, '.'));

				$include_modules[] = array(

					'class' => $class, 

					'file' => $value

				);

			}//foreach

		}//if tep_not_null

		for ($i=0, $n=sizeof($include_modules); $i<$n; $i++) {

			include(DIR_WS_LANGUAGES . $language . '/modules/vendors_shipping/' . $include_modules[$i]['file']);

			include(DIR_WS_MODULES . 'vendors_shipping/' . $include_modules[$i]['file']);

			$GLOBALS[$include_modules[$i]['class']] = new $include_modules[$i]['class'];

		}//for

	}//function





////

// Get a quote for one or many shipping methods, for a specific vendor

	function quote($module = '', $method = '', $vendors_id='1', $vendor_data = array()) {

		/********************PRODUCT SIZE MOD BY FIW**************************/ 	

		global $total_weight, $shipping_weight, $shipping_quoted, $shipping_num_boxes, $cart;
         $total_weight = $vendor_data['weight'];

         $shipping_weight = $total_weight;

         $cost = $vendor_data['cost'];

         $total_count = $vendor_data['qty'];

		/********************PRODUCT SIZE MOD BY FIW**************************/ 

		global $order, $shiptotal;

		$quotes_array = array();

		/********************PRODUCT SIZE MOD BY FIW**************************/ 

		$oversized_shipping = $cart->oversized_shipping($vendors_id);

		/********************PRODUCT SIZE MOD BY FIW**************************/ 

                $cart->vendor_shipping();

		if (is_array($this->modules[$vendors_id])) {

			$shipping_quoted = '';

			$shipping_num_boxes = 1;

			$shipping_weight = $cart->vendor_shipping[$vendors_id]['weight'];

			if (SHIPPING_BOX_WEIGHT >= $shipping_weight*SHIPPING_BOX_PADDING/100) {

				$shipping_weight = $shipping_weight+SHIPPING_BOX_WEIGHT;

			} else {

				$shipping_weight = $shipping_weight + ($shipping_weight*SHIPPING_BOX_PADDING/100);

			}

			if ($shipping_weight > SHIPPING_MAX_WEIGHT) { // Split into many boxes

				$shipping_num_boxes = ceil($shipping_weight/SHIPPING_MAX_WEIGHT);

				$shipping_weight = $shipping_weight/$shipping_num_boxes;

			}

			$include_quotes = array();

			reset($this->modules[$vendors_id]);
            
			foreach ($this->modules[$vendors_id] as $value) {

				$class = substr($value, 0, strrpos($value, '.'));  // $class is the filename without the .php

				if (tep_not_null($module)) {

					if ( ($module == $class) && ($GLOBALS[$class]->enabled($vendors_id)) ) {

						$include_quotes[] = $class;

					}

				} elseif ($GLOBALS[$class]->enabled($vendors_id)) {  //Module is enabled for this vendor

					$include_quotes[] = $class;

				}

			}



			reset($include_quotes);

			$size = sizeof($include_quotes);


			for ($i=0; $i<$size; $i++) {

				//$quotes = $GLOBALS[$include_quotes[$i]]->quote($method, '', $vendors_id);

                                //$quotes = $GLOBALS[$include_quotes[$i]]->quote($method, '', $vendors_id, $cart->vendor_shipping[$vendors_id]['qty']);

                                $quotes = $GLOBALS[$include_quotes[$i]]->quote($method, '', $vendors_id, $total_count);

                            

				/********************PRODUCT SIZE MOD BY FIW**************************/ 

				if ($oversized_shipping > 0 && sizeof($quotes['methods']) > 0) {

					for ($j=0, $n2=sizeof($quotes['methods']); $j<$n2; $j++) {

						$quotes['methods'][$j]['cost'] = $quotes['methods'][$j]['cost'] + $oversized_shipping;

					}

				}

				/********************PRODUCT SIZE MOD BY FIW**************************/ 

				if (is_array($quotes)) $quotes_array[] = $quotes;

			}

		}

		return $quotes_array;

	}







////

//Find the cheapest shipping method for a specific vendor

	//function cheapest($vendors_id='1') {

	function cheapest($vendors_id='1', $products_qty_for_vendor = null) {

		if (is_array($this->modules[$vendors_id])) {

			$rates = array();

			reset($this->modules[$vendors_id]);

			foreach ($this->modules[$vendors_id] as $value) {

				$class = substr($value, 0, strrpos($value, '.'));

				if ($GLOBALS[$class]->enabled($vendors_id)) {

					//$quotes = $GLOBALS[$class]->quote('', '', $vendors_id);

					$quotes = $GLOBALS[$class]->quote('', '', $vendors_id, $products_qty_for_vendor);

					for ($i=0, $n=sizeof($quotes['methods']); $i<$n; $i++) {

						if (isset($quotes['methods'][$i]['cost']) && tep_not_null($quotes['methods'][$i]['cost'])) {

							$rates[] = array(

								'id' => $quotes['id'] . '_' . $quotes['methods'][$i]['id'],

								'title' => $quotes['module'] . ' (' . $quotes['methods'][$i]['title'] . ')',

								'cost' => $quotes['methods'][$i]['cost']

							);

						}

					}

				}

			}

			$cheapest = false;

			for ($i=0, $n=sizeof($rates); $i<$n; $i++) {

				if (is_array($cheapest)) {

					if ($rates[$i]['cost'] < $cheapest['cost']) {

						$cheapest = $rates[$i];

					}

				} else {

					$cheapest = $rates[$i];

				}

			}

			return $cheapest;

		}

	}

}

?>