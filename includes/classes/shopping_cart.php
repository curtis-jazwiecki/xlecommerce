<?php
/*
  $Id: shopping_cart.php,v 1.35 2003/06/25 21:14:33 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class shoppingCart {
    var $contents, $total, $weight, $cartID, $content_type;

    function shoppingCart() {
      $this->reset();
    }

    function restore_contents() {
// ############Added CCGV Contribution ##########
      global $customer_id, $gv_id, $REMOTE_ADDR;
//      global $customer_id;
// ############ End Added CCGV Contribution ##########

      if (!tep_session_is_registered('customer_id')) return false;

// insert current cart contents in database
      if (is_array($this->contents)) {
        reset($this->contents);
        // BOF SPPC attribute hide/invalid check: loop through the shopping cart and check the attributes if they
// are hidden for the now logged-in customer
      $this->cg_id = $this->get_customer_group_id();
        while (list($products_id, ) = each($this->contents)) {
					// only check attributes if they are set for the product in the cart
				   if (isset($this->contents[$products_id]['attributes'])) {
				$check_attributes_query = tep_db_query("select options_id, options_values_id, IF(find_in_set('" . $this->cg_id . "', attributes_hide_from_groups) = 0, '0', '1') as hide_attr_status from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . tep_get_prid($products_id) . "'");
				while ($_check_attributes = tep_db_fetch_array($check_attributes_query)) {
					$check_attributes[] = $_check_attributes;
				} // end while ($_check_attributes = tep_db_fetch_array($check_attributes_query))
				$no_of_check_attributes = count($check_attributes);
				$change_products_id = '0';

				foreach($this->contents[$products_id]['attributes'] as $attr_option => $attr_option_value) {
					$valid_option = '0';
					for ($x = 0; $x < $no_of_check_attributes ; $x++) {
						if ($attr_option == $check_attributes[$x]['options_id'] && $attr_option_value == $check_attributes[$x]['options_values_id']) {
							$valid_option = '1';
							if ($check_attributes[$x]['hide_attr_status'] == '1') {
							// delete hidden attributes from array attributes, change products_id accordingly later
							$change_products_id = '1';
							unset($this->contents[$products_id]['attributes'][$attr_option]);
							}
						} // end if ($attr_option == $check_attributes[$x]['options_id']....
					} // end for ($x = 0; $x < $no_of_check_attributes ; $x++)
					if ($valid_option == '0') {
						// after having gone through the options for this product and not having found a matching one
						// we can conclude that apparently this is not a valid option for this product so remove it
						unset($this->contents[$products_id]['attributes'][$attr_option]);
						// change products_id accordingly later
						$change_products_id = '1';
					}
				} // end foreach($this->contents[$products_id]['attributes'] as $attr_option => $attr_option_value)

          if ($change_products_id == '1') {
	           $original_products_id = $products_id;
	           $products_id = tep_get_prid($original_products_id);
	           $products_id = tep_get_uprid($products_id, $this->contents[$original_products_id]['attributes']);
						 // add the product without the hidden attributes to the cart
	           $this->contents[$products_id] = $this->contents[$original_products_id];
				     // delete the originally added product with the hidden attributes
	           unset($this->contents[$original_products_id]);
            }
				  } // end if (isset($this->contents[$products_id]['attributes']))
				} // end while (list($products_id, ) = each($this->contents))
       reset($this->contents); // reset the array otherwise the cart will be emptied
// EOF SPPC attribute hide/invalid check

        while (list($products_id, ) = each($this->contents)) {
          $qty = $this->contents[$products_id]['qty'];
          $product_query = tep_db_query("select products_id from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
          if (!tep_db_num_rows($product_query)) {
            tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . $qty . "', '" . date('Ymd') . "')");
            if (isset($this->contents[$products_id]['attributes'])) {
              reset($this->contents[$products_id]['attributes']);
              while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
                tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id) . "', '" . (int)$option . "', '" . (int)$value . "')");
              }
            }
          } else {
            tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . $qty . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
          }
        }
// ############ Added CCGV Contribution ##########
        if (tep_session_is_registered('gv_id')) {
          $gv_query = tep_db_query("insert into  " . TABLE_COUPON_REDEEM_TRACK . " (coupon_id, customer_id, redeem_date, redeem_ip) values ('" . $gv_id . "', '" . (int)$customer_id . "', now(),'" . $REMOTE_ADDR . "')");
          $gv_update = tep_db_query("update " . TABLE_COUPONS . " set coupon_active = 'N' where coupon_id = '" . $gv_id . "'");
          tep_gv_account_update($customer_id, $gv_id);
          tep_session_unregister('gv_id');
        }
// ############ End Added CCGV Contribution ##########
      }

// reset per-session cart contents, but not the database contents
      $this->reset(false);

      $products_query = tep_db_query("select products_id, customers_basket_quantity from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
      while ($products = tep_db_fetch_array($products_query)) {
        $this->contents[$products['products_id']] = array('qty' => $products['customers_basket_quantity']);
// attributes
        $attributes_query = tep_db_query("select products_options_id, products_options_value_id from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products['products_id']) . "'");
        while ($attributes = tep_db_fetch_array($attributes_query)) {
          $this->contents[$products['products_id']]['attributes'][$attributes['products_options_id']] = $attributes['products_options_value_id'];
        }
      }

      $this->cleanup();
    }

    function reset($reset_database = false) {
      global $customer_id;

      $this->contents = array();
      $this->total = 0;
      $this->weight = 0;
      $this->content_type = false;

      if (tep_session_is_registered('customer_id') && ($reset_database == true)) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
      }

      unset($this->cartID);
      if (tep_session_is_registered('cartID')) tep_session_unregister('cartID');
    }

    function add_cart($products_id, $qty = '1', $attributes = '', $notify = true) {
      global $new_products_id_in_cart, $customer_id;
      // BOF Separate Pricing Per Customer 
      $this->cg_id = $this->get_customer_group_id();
// EOF Separate Pricing Per Customer

if (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS && MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT == $products_id) {
	$qty = 1;
}
      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);
      $attributes_pass_check = true;

      if (is_array($attributes)) {
        reset($attributes);
        while (list($option, $value) = each($attributes)) {
          if (!is_numeric($option) || !is_numeric($value)) {
            $attributes_pass_check = false;
            break;
          }
        }
      }

      if (is_numeric($products_id) && is_numeric($qty) && ($attributes_pass_check == true)) {
        $check_product_query = tep_db_query("select products_status, sold_in_bundle_only from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
        $check_product = tep_db_fetch_array($check_product_query);

        if (((($check_product !== false) && ($check_product['products_status'] == '1') ) || (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS && MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT == $products_id)) && ($check_product['sold_in_bundle_only'] != 'yes')) {
        
          // BOF SPPC attribute hide check, original query expanded to include attributes
	/*			$check_product_query = tep_db_query("select p.products_status, options_id, options_values_id, IF(find_in_set('" . $this->cg_id . "', attributes_hide_from_groups) = 0, '0', '1') as hide_attr_status from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_ATTRIBUTES . " using(products_id) where p.products_id = '" . (int)$products_id . "'");
				while ($_check_product = tep_db_fetch_array($check_product_query)) {
					$check_product[] = $_check_product;
				} // end while ($_check_product = tep_db_fetch_array($check_product_query))
				$no_of_check_product = count($check_product);

  if (is_array($attributes)) {
				foreach($attributes as $attr_option => $attr_option_value) {
					$valid_option = '0';
					for ($x = 0; $x < $no_of_check_product ; $x++) {
						if ($attr_option == $check_product[$x]['options_id'] && $attr_option_value == $check_product[$x]['options_values_id']) {
							$valid_option = '1';
							if ($check_product[$x]['hide_attr_status'] == '1') {
							// delete hidden attributes from array attributes
							unset($attributes[$attr_option]);
							}
						} // end if ($attr_option == $check_product[$x]['options_id']....
					} // end for ($x = 0; $x < $no_of_check_product ; $x++)
					if ($valid_option == '0') {
						// after having gone through the options for this product and not having found a matching one
						// we can conclude that apparently this is not a valid option for this product so remove it
						unset($attributes[$attr_option]);
					}
				} // end foreach($attributes as $attr_option => $attr_option_value)
	} // end if (is_array($attributes))
// now attributes have been checked and hidden and invalid ones deleted make the $products_id_string again
				$products_id_string = tep_get_uprid($products_id, $attributes);

        if ((isset($check_product) && tep_not_null($check_product)) && ($check_product[0]['products_status'] == '1')) {
// EOF SPPC attribute hide check

            */
          if ($notify == true) {
            $new_products_id_in_cart = $products_id;
            tep_session_register('new_products_id_in_cart');
          }

          if ($this->in_cart($products_id_string)) {
            $this->update_quantity($products_id_string, $qty, $attributes);
          } else {
            $this->contents[$products_id_string] = array('qty' => $qty);
// insert into database
            if (tep_session_is_registered('customer_id') && $customer_id>0) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET . " (customers_id, products_id, customers_basket_quantity, customers_basket_date_added) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$qty . "', '" . date('Ymd') . "')");

            if (is_array($attributes)) {
              reset($attributes);
              while (list($option, $value) = each($attributes)) {
                $this->contents[$products_id_string]['attributes'][$option] = $value;
// insert into database
                if (tep_session_is_registered('customer_id') && $customer_id>0) tep_db_query("insert into " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " (customers_id, products_id, products_options_id, products_options_value_id) values ('" . (int)$customer_id . "', '" . tep_db_input($products_id_string) . "', '" . (int)$option . "', '" . (int)$value . "')");
              }
            }
          }

          $this->cleanup();
// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure          
if (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS && MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT != $products_id) 
          $this->cartID = $this->generate_cart_id();
        }
      }
    }

    function update_quantity($products_id, $quantity = '', $attributes = '') {
      global $customer_id;

      $products_id_string = tep_get_uprid($products_id, $attributes);
      $products_id = tep_get_prid($products_id_string);

      $attributes_pass_check = true;

      if (is_array($attributes)) {
        reset($attributes);
        while (list($option, $value) = each($attributes)) {
          if (!is_numeric($option) || !is_numeric($value)) {
            $attributes_pass_check = false;
            break;
          }
        }
      }

      if (is_numeric($products_id) && isset($this->contents[$products_id_string]) && is_numeric($quantity) && ($attributes_pass_check == true)) {
        $this->contents[$products_id_string] = array('qty' => $quantity);
// update database
        if (tep_session_is_registered('customer_id') && $customer_id>0) tep_db_query("update " . TABLE_CUSTOMERS_BASKET . " set customers_basket_quantity = '" . (int)$quantity . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "'");

        if (is_array($attributes)) {
          reset($attributes);
          while (list($option, $value) = each($attributes)) {
            $this->contents[$products_id_string]['attributes'][$option] = $value;
// update database
            if (tep_session_is_registered('customer_id') && $customer_id>0) tep_db_query("update " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " set products_options_value_id = '" . (int)$value . "' where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id_string) . "' and products_options_id = '" . (int)$option . "'");
          }
        }
      }
    }

    function cleanup() {
      global $customer_id;

      reset($this->contents);
      while (list($key,) = each($this->contents)) {
        if ($this->contents[$key]['qty'] < 1) {
          unset($this->contents[$key]);
// remove from database
          if (tep_session_is_registered('customer_id')) {
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
            tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($key) . "'");
          }
        }
      }
    }

    function count_contents() {  // get total number of items in cart 
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $total_items += $this->get_quantity($products_id);
        }
      }

      return $total_items;
    }

    function get_quantity($products_id) {
      if (isset($this->contents[$products_id])) {
        return $this->contents[$products_id]['qty'];
      } else {
        return 0;
      }
    }

    function in_cart($products_id) {
      if (isset($this->contents[$products_id])) {
        return true;
      } else {
        return false;
      }
    }

    function remove($products_id) {
      global $customer_id;
      unset($this->contents[$products_id]);
// remove from database
      if (tep_session_is_registered('customer_id')) {
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
        tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "' and products_id = '" . tep_db_input($products_id) . "'");
      }

// assign a temporary unique ID to the order contents to prevent hack attempts during the checkout procedure
      $this->cartID = $this->generate_cart_id();
    }

    function remove_all() {
      $this->reset();
    }

    function get_product_id_list() {
      $product_id_list = '';
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $product_id_list .= ', ' . $products_id;
        }
      }

      return substr($product_id_list, 2);
    }

    function calculate() {
       global $currencies;
// ############ Added CCGV Contribution ##########
      $this->total_virtual = 0; // CCGV Contribution
// ############ End Added CCGV Contribution ##########
      $this->total = 0;
      $this->weight = 0;
      if (!is_array($this->contents)) return 0;
      // BOF Separate Pricing Per Customer
// global variable (session) $sppc_customer_group_id -> class variable cg_id
      $this->cg_id = $this->get_customer_group_id();
// EOF Separate Pricing Per Customer


      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        $qty = $this->contents[$products_id]['qty'];

// products price
        $product_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
        if ($product = tep_db_fetch_array($product_query)) {
// ############ Added CCGV Contribution ##########
          $no_count = 1;
          $gv_query = tep_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
          $gv_result = tep_db_fetch_array($gv_query);
          if (preg_match('/^GIFT/', $gv_result['products_model'])) {
            $no_count = 0;
          }
// ############ End Added CCGV Contribution ##########
          $prid = $product['products_id'];
          $products_tax = tep_get_tax_rate($product['products_tax_class_id']);
          $org_products_price = $products_price = $product['products_price'];
          $products_weight = $product['products_weight'];
		  
        /*  $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
          if (tep_db_num_rows ($specials_query)) {
            $specials = tep_db_fetch_array($specials_query);
            $products_price = $specials['specials_new_products_price'];
          } */
          
          // BOF Separate Pricing Per Customer
          
          
      $org_specials_price = $specials_price = tep_get_products_special_price((int)$prid);
      if ($this->cg_id != 0){
		//BOF:mod 16DEC
		$parent_model_query = tep_db_query("select parent_products_model from products where products_id='" . (int)$prid . "'");
		//if (tep_db_num_rows($parent_model_query)){
			$info = tep_db_fetch_array($parent_model_query);
			if (!empty($info['parent_products_model'])){
				$parent_id_query = tep_db_query("select products_id from products where products_model='" . $info['parent_products_model'] . "'");
				$parent = tep_db_fetch_array($parent_id_query);
				if (!empty($parent['products_id'])){
					$specials_price = tep_get_products_special_price((int)$parent['products_id']);
				}
			}
		//}
		/*
		//EOF:mod 16DEC
        $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$prid . "' and customers_group_id =  '" . $this->cg_id . "'");
		//BOF:mod 16DEC
		*/
		$customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (!empty($parent['products_id']) ? (int)$parent['products_id'] : (int)$prid)  . "' and customers_group_id =  '" . $this->cg_id . "'");
		//EOF:mod 16DEC
        if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
        $products_price = $customer_group_price['customers_group_price'];
        }
		if ($products_price > $org_products_price){
			$products_price = $org_products_price;
			$specials_price = $org_specials_price;
		}
		
      } 
          
   //$specials_price = tep_get_products_special_price((int)$prid);
      if (tep_not_null($specials_price)) {
          
          if($products_price > $specials_price )
               $products_price = $specials_price;          
      } 
      
      
      

// EOF Separate Pricing Per Customer


// ############ Added CCGV Contribution ##########
          $this->total_virtual += tep_add_tax($products_price, $products_tax) * $qty * $no_count;// ICW CREDIT CLASS;
          $this->weight_virtual += ($qty * $products_weight) * $no_count;// ICW CREDIT CLASS;
// ############ End Added CCGV Contribution ##########
          $this->total += tep_add_tax($products_price, $products_tax) * $qty;
          $this->weight += ($qty * $products_weight);
        }
/*
// attributes price
        if (isset($this->contents[$products_id]['attributes'])) {
          reset($this->contents[$products_id]['attributes']);
          while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
            $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$prid . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
            $attribute_price = tep_db_fetch_array($attribute_price_query);
            if ($attribute_price['price_prefix'] == '+') {
              $this->total += $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
            } else {
              $this->total -= $qty * tep_add_tax($attribute_price['options_values_price'], $products_tax);
            }
          }
        }
      }
    } */
        
        // attributes price
// BOF SPPC attributes mod
        if (isset($this->contents[$products_id]['attributes'])) {
          reset($this->contents[$products_id]['attributes']);
       $where = " AND ((";
        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
         $where .= "options_id = '" . (int)$option . "' AND options_values_id = '" . (int)$value . "') OR (";
       }
       $where=substr($where, 0, -5) . ')';
    
       $attribute_price_query = tep_db_query("SELECT products_attributes_id, options_values_price, price_prefix FROM " . TABLE_PRODUCTS_ATTRIBUTES . " WHERE products_id = '" . (int)$products_id . "'" . $where ."");

       if (tep_db_num_rows($attribute_price_query)) { 
	       $list_of_prdcts_attributes_id = '';
				 // empty array $attribute_price
				 $attribute_price = array();
	       while ($attributes_price_array = tep_db_fetch_array($attribute_price_query)) { 
		   $attribute_price[] =  $attributes_price_array;
		   $list_of_prdcts_attributes_id .= $attributes_price_array['products_attributes_id'].",";
            }
	       if (tep_not_null($list_of_prdcts_attributes_id) && $this->cg_id != '0') { 
         $select_list_of_prdcts_attributes_ids = "(" . substr($list_of_prdcts_attributes_id, 0 , -1) . ")";
	 $pag_query = tep_db_query("select products_attributes_id, options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES_GROUPS . " where products_attributes_id IN " . $select_list_of_prdcts_attributes_ids . " AND customers_group_id = '" . $this->cg_id . "'");
	 while ($pag_array = tep_db_fetch_array($pag_query)) {
		 $cg_attr_prices[] = $pag_array;
	 }

	 // substitute options_values_price and prefix for those for the customer group (if available)
	 if ($customer_group_id != '0' && tep_not_null($cg_attr_prices)) {
	    for ($n = 0 ; $n < count($attribute_price); $n++) {
		 for ($i = 0; $i < count($cg_attr_prices) ; $i++) {
			 if ($cg_attr_prices[$i]['products_attributes_id'] == $attribute_price[$n]['products_attributes_id']) {
				$attribute_price[$n]['price_prefix'] = $cg_attr_prices[$i]['price_prefix'];
				$attribute_price[$n]['options_values_price'] = $cg_attr_prices[$i]['options_values_price'];
			 }
		 } // end for ($i = 0; $i < count($cg_att_prices) ; $i++)
          }
        } // end if ($customer_group_id != '0' && (tep_not_null($cg_attr_prices))
      } // end if (tep_not_null($list_of_prdcts_attributes_id) && $customer_group_id != '0')
// now loop through array $attribute_price to add up/substract attribute prices
//echo count($attribute_price);

   for ($n = 0 ; $n < count($attribute_price); $n++) {
         /*   if ($attribute_price[$n]['price_prefix'] == '+') {
               
              $this->total += $currencies->calculate_price($attribute_price[$n]['options_values_price'], $products_tax, $qty);
            } else {
             
            
              $this->total -= $currencies->calculate_price($attribute_price[$n]['options_values_price'], $products_tax, $qty);
        } */
       
           if ($attribute_price['price_prefix'] == '+') {
              $this->total += $qty * tep_add_tax($attribute_price[$n]['options_values_price'], $products_tax);
            } else {
              $this->total -= $qty * tep_add_tax($attribute_price[$n]['options_values_price'], $products_tax);
            }
       
       
   } // end for ($n = 0 ; $n < count($attribute_price); $n++)
          } // end if (tep_db_num_rows($attribute_price_query))
        } // end if (isset($this->contents[$products_id]['attributes'])) 
      }
    }
// EOF SPPC attributes mod


    
function attributes_price($products_id) {
      $attributes_price = 0;

      if (isset($this->contents[$products_id]['attributes'])) {
        reset($this->contents[$products_id]['attributes']);
        while (list($option, $value) = each($this->contents[$products_id]['attributes'])) {
          $attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
          $attribute_price = tep_db_fetch_array($attribute_price_query);
          if ($attribute_price['price_prefix'] == '+') {
            $attributes_price += $attribute_price['options_values_price'];
          } else {
            $attributes_price -= $attribute_price['options_values_price'];
          }
        }
      }

      return $attributes_price;
    }

    function get_products() {
      global $languages_id;
      if (!is_array($this->contents)) return false;

      $products_array = array();
      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
        //MVS
        $products_query = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.products_bundle, p.sold_in_bundle_only, p.products_image, p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_tax_class_id, p.in_store_pickup, p.is_ok_for_shipping, v.vendors_id, v.vendors_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_VENDORS . " v where p.products_id = '" . (int)$products_id . "' and pd.products_id = p.products_id and v.vendors_id = p.vendors_id and pd.language_id = '" . (int)$languages_id . "'");
        if ($products = tep_db_fetch_array($products_query)) {
          $prid = $products['products_id'];
          $org_products_price = $products_price = $products['products_price'];
		  
        /*  $specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$prid . "' and status = '1'");
          if (tep_db_num_rows($specials_query)) {
            $specials = tep_db_fetch_array($specials_query);
            $products_price = $specials['specials_new_products_price'];
          }
*/
          // BOF Separate Pricing Per Customer
		  $org_specials_price = $specials_price = tep_get_products_special_price((int)$prid);
   if ($this->cg_id != 0){
		//BOF:mod 16DEC
		$parent_model_query = tep_db_query("select parent_products_model from products where products_id='" . (int)$prid . "'");
		//if (tep_db_num_rows($parent_model_query)){
			$info = tep_db_fetch_array($parent_model_query);
			if (!empty($info['parent_products_model'])){
				$parent_id_query = tep_db_query("select products_id from products where products_model='" . $info['parent_products_model'] . "'");
				$parent = tep_db_fetch_array($parent_id_query);
				if (!empty($parent['products_id'])){
					$specials_price = tep_get_products_special_price((int)$parent['products_id']);
				}
				
			}
		//}
		/*
		//EOF:mod 16DEC
        $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$prid . "' and customers_group_id =  '" . $this->cg_id . "'");
		//BOF:mod 16DEC
		*/
        $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (!empty($parent['products_id']) ? (int)$parent['products_id'] : (int)$prid )  . "' and customers_group_id =  '" . $this->cg_id . "'");
		//EOF:mod 16DEC
        if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
        $products_price = $customer_group_price['customers_group_price'];
        }
		if ($products_price > $org_products_price){
			$products_price = $org_products_price;
			$specials_price = $org_specials_price;
		}
      } 
          
	//$specials_price = tep_get_products_special_price((int)$prid);
      if (tep_not_null($specials_price)) {
          
          if($products_price > $specials_price )
               $products_price = $specials_price;          
      } 
// EOF Separate Pricing Per Customer

          $products_array[] = array('id' => $products_id,
                                    'name' => $products['products_name'],
                                    'model' => $products['products_model'],
                                    'bundle' => $products['products_bundle'],
                                    'sold_in_bundle_only' => $products['sold_in_bundle_only'],
                                    'image' => $products['products_image'],
                                    'price' => $products_price,
                                    'quantity' => $this->contents[$products_id]['qty'],
                                    'weight' => $products['products_weight'],
                                    'length' => $products['products_length'],
                                    'width' => $products['products_width'],
                                    'height' => $products['products_height'],
                                    'ready_to_ship' => $products['products_ready_to_ship'],
                                    'final_price' => ($products_price + $this->attributes_price($products_id)),
                                    'tax_class_id' => $products['products_tax_class_id'],
//MVS start
                                    'vendors_id' => $products['vendors_id'],
                                    'vendors_name' => $products['vendors_name'],
//MVS end
                                    'in_store_pickup' => $products['in_store_pickup'],
                                    'is_ok_for_shipping' => $products['is_ok_for_shipping'],
                                    'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : ''));
        }
      }

      return $products_array;
    }
  /********************PRODUCT SIZE MOD**************************/ 
  //BOF:mvs_internal_mod
  /*
  //EOF:mvs_internal_mod
    function oversized_shipping() {
      $total_surcharge = 0;
      $qty = 0;
      if (OVERSIZED_SHIPPING_ENABLE == 'True') {
      reset($this->contents);
      while (list($products_id, ) = each($this->contents)) {
          $shipping_surcharge_query = tep_db_query("select products_size from products where products_id = '" . (int)$products_id . "'");
          $shipping_surcharge = tep_db_fetch_array($shipping_surcharge_query);
          if ($shipping_surcharge['products_size'] == '1') {
            $qty = $qty + $this->contents[$products_id]['qty'];
           }
         }    
         $charge = OVERSIZED_SHIPPING_CHARGES;
         $total_surcharge = $charge * $qty;
        } 
      return $total_surcharge;
    }
  //BOF:mvs_internal_mod
  */
	function oversized_shipping($vendors_id = '') {
		$total_surcharge = 0;
		$qty = 0;
		if (OVERSIZED_SHIPPING_ENABLE == 'True') {
			if (!empty($vendors_id)){
				$products = $this->get_vendors_products($vendors_id);
				foreach($products as $product){
					$shipping_surcharge_query = tep_db_query("select products_size from products where products_id = '" . (int)$product['id'] . "'");
					$shipping_surcharge = tep_db_fetch_array($shipping_surcharge_query);
					if ($shipping_surcharge['products_size'] == '1') {
						$qty = $qty + $product['quantity'];
					}
				}
				$charge = OVERSIZED_SHIPPING_CHARGES;
				$total_surcharge = $charge * $qty;
			} else {
				reset($this->contents);
				while (list($products_id, ) = each($this->contents)) {
					$shipping_surcharge_query = tep_db_query("select products_size from products where products_id = '" . (int)$products_id . "'");
					$shipping_surcharge = tep_db_fetch_array($shipping_surcharge_query);
					if ($shipping_surcharge['products_size'] == '1') {
						$qty = $qty + $this->contents[$products_id]['qty'];
					}
				}
				$charge = OVERSIZED_SHIPPING_CHARGES;
				$total_surcharge = $charge * $qty;
			}
		}	
		return $total_surcharge;
    }
  //EOF:mvs_internal_mod
 /********************PRODUCT SIZE MOD**************************/ 
 
    function show_total() {
      $this->calculate();

      return $this->total;
    }

    function show_weight() {
      $this->calculate();

      return $this->weight;
    }
// ############ Added CCGV Contribution ##########
    function show_total_virtual() {
      $this->calculate();

      return $this->total_virtual;
    }

    function show_weight_virtual() {
      $this->calculate();

      return $this->weight_virtual;
    }
// ############ End Added CCGV Contribution ##########

    function generate_cart_id($length = 5) {
      return tep_create_random_value($length, 'digits');
    }

    function get_content_type() {
      $this->content_type = false;

      if ( (DOWNLOAD_ENABLED == 'true') && ($this->count_contents() > 0) ) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          if (isset($this->contents[$products_id]['attributes'])) {
            reset($this->contents[$products_id]['attributes']);
            while (list(, $value) = each($this->contents[$products_id]['attributes'])) {
              $virtual_check_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_ATTRIBUTES_DOWNLOAD . " pad where pa.products_id = '" . (int)$products_id . "' and pa.options_values_id = '" . (int)$value . "' and pa.products_attributes_id = pad.products_attributes_id");
              $virtual_check = tep_db_fetch_array($virtual_check_query);

              if ($virtual_check['total'] > 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
// ############ Added CCGV Contribution ##########
          } elseif ($this->show_weight() == 0) {
            reset($this->contents);
            while (list($products_id, ) = each($this->contents)) {
              $virtual_check_query = tep_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
              $virtual_check = tep_db_fetch_array($virtual_check_query);
              if ($virtual_check['products_weight'] == 0) {
                switch ($this->content_type) {
                  case 'physical':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'virtual';
                    break;
                }
              } else {
                switch ($this->content_type) {
                  case 'virtual':
                    $this->content_type = 'mixed';

                    return $this->content_type;
                    break;
                  default:
                    $this->content_type = 'physical';
                    break;
                }
              }
            }
// ############ End Added CCGV Contribution ##########
          } else {
            switch ($this->content_type) {
              case 'virtual':
                $this->content_type = 'mixed';

                return $this->content_type;
                break;
              default:
                $this->content_type = 'physical';
                break;
            }
          }
        }
      } else {
        $this->content_type = 'physical';
      }

      return $this->content_type;
    }

    function unserialize($broken) {
      for(reset($broken);$kv=each($broken);) {
        $key=$kv['key'];
        if (gettype($this->$key)!="user function")
        $this->$key=$kv['value'];
      }
    }

// ############ Added CCGV Contribution ##########
   // amend count_contents to show nil contents for shipping
   // as we don't want to quote for 'virtual' item
   // GLOBAL CONSTANTS if NO_COUNT_ZERO_WEIGHT is true then we don't count any product with a weight
   // which is less than or equal to MINIMUM_WEIGHT
   // otherwise we just don't count gift certificates

     
// added for Separate Pricing Per Customer, returns customer_group_id
    function get_customer_group_id() {
      if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
        $_cg_id = $_SESSION['sppc_customer_group_id'];
      } else {
         $_cg_id = 0;
      }
      return $_cg_id;
    }
    function count_contents_virtual() {  // get total number of items in cart disregard gift vouchers
      $total_items = 0;
      if (is_array($this->contents)) {
        reset($this->contents);
        while (list($products_id, ) = each($this->contents)) {
          $no_count = false;
          $gv_query = tep_db_query("select products_model from " . TABLE_PRODUCTS . " where products_id = '" . $products_id . "'");
          $gv_result = tep_db_fetch_array($gv_query);
          if (preg_match('/^GIFT/', $gv_result['products_model'])) {
            $no_count=true;
          }
          if (NO_COUNT_ZERO_WEIGHT == 1) {
            $gv_query = tep_db_query("select products_weight from " . TABLE_PRODUCTS . " where products_id = '" . tep_get_prid($products_id) . "'");
            $gv_result=tep_db_fetch_array($gv_query);
            if ($gv_result['products_weight']<=MINIMUM_WEIGHT) {
              $no_count=true;
            }
          }
          if (!$no_count) $total_items += $this->get_quantity($products_id);
        }
      }
      return $total_items;
    }

//////
//MVS Start
//  New method to provide cost, weight, quantity, and product IDs by vendor
//////
//Output array structure (example):
//shoppingcart Object
//(
//  [vendor_shipping] => array
//    (
//      [0] => array   //Number is the vendor_id
//        (
//          [weight] => 22.59
//          [cost] => 12.95
//          [qty] => 2
//          [products_id] => array
//            (
//              [0] => 12
//              [1] => 47
//            )
//        )
//      [12] => array
//        (
//          [weight] => 32.74
//          [cost] => 109.59
//          [qty] => 5
//          [products_id] => array
//            (
//              [0] => 2
//              [1] => 3
//              [2] => 37
//              [3] => 49
//            )
//        )
//    )
//)
	function vendor_shipping() {
            $this->vendor_shipping = array();
            if (SELECT_VENDOR_SHIPPING=='false') return true;
		if (!is_array($this->contents)) return 0;  //Cart is empty
		$this->vendor_shipping = array();  //Initialize the output array
		reset($this->contents);            //  and reset the input array
		foreach ($this->contents as $products_id => $value) {  //$value is never used
			$quantity = $this->contents[$products_id]['qty'];
			$products_query = tep_db_query("select products_id, products_price, products_tax_class_id, products_weight, parent_products_model, vendors_id from " . TABLE_PRODUCTS . " where products_id = '" . (int)$products_id . "'");
			if ($products = tep_db_fetch_array($products_query)) {
				$products_price = $products['products_price'];
				$specials_price = tep_get_products_special_price($products_id);
				if ($specials_price>0 && $specials_price<$products_price){
					$products_price = $specials_price;
				}
				if ( !empty($products['parent_products_model']) ){
					$parent_product_query = tep_db_query("select products_id, products_price from products where products_model='" . tep_db_input($products['parent_products_model']) . "'");
					
					if ( $parent_product = tep_db_fetch_array($parent_product_query) ){
						$products_price = $parent_product['products_price'];
						$specials_price = tep_get_products_special_price($parent_product['products_id']);
						if ($specials_price>0 && $specials_price<$products_price){
							$products_price = $specials_price;
						}
					}
				}

				$this->cg_id = $this->get_customer_group_id();
				if ($this->cg_id != 0){
					$customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (!empty($parent_product['products_id']) ? (int)$parent_product['products_id'] : (int)$products_id )  . "' and customers_group_id =  '" . $this->cg_id . "'");
					if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
						$customer_group_products_price = $customer_group_price['customers_group_price'];
						if ($customer_group_products_price < $products_price){
							$products_price = $customer_group_products_price;
						}
					}
				}
				$products_weight = $products['products_weight'];
				$vendors_id = ($products['vendors_id'] <= 0) ? 1 : $products['vendors_id'];
				$products_tax = tep_get_tax_rate($products['products_tax_class_id']);
				//Find special prices (if any)
				$specials_query = tep_db_query("select specials_new_products_price from " . TABLE_SPECIALS . " where products_id = '" . (int)$products_id . "' and status = '1'");
				if (tep_db_num_rows ($specials_query)) {
					$specials = tep_db_fetch_array($specials_query);
					$products_price = $specials['specials_new_products_price'];
				}
				//Add values to the output array
				$this->vendor_shipping[$vendors_id]['weight'] += ($quantity * $products_weight);
				$this->vendor_shipping[$vendors_id]['cost'] += tep_add_tax($products_price, $products_tax) * $quantity;
				$this->vendor_shipping[$vendors_id]['qty'] += $quantity;
				$this->vendor_shipping[$vendors_id]['products_id'][] = $products_id; //There can be more than one product
			}

			// Add/subtract attributes prices (if any)
			if (isset($this->contents[$products_id]['attributes'])) {
				reset($this->contents[$products_id]['attributes']);
				foreach ($this->contents[$products_id]['attributes'] as $option => $value) {
					$attribute_price_query = tep_db_query("select options_values_price, price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id = '" . (int)$products_id . "' and options_id = '" . (int)$option . "' and options_values_id = '" . (int)$value . "'");
					$attribute_price = tep_db_fetch_array($attribute_price_query);
					if ($attribute_price['price_prefix'] == '+') {
						$this->vendor_shipping[$vendors_id]['cost'] += $quantity * tep_add_tax($attribute_price['options_values_price'], $products_tax);
					} else {
						$this->vendor_shipping[$vendors_id]['cost'] -= $quantity * tep_add_tax($attribute_price['options_values_price'], $products_tax);
					}
				}
			}
		}
		//return $this->vendor_shipping;
                return true;
	}
//MVS End

//BOF:mvs_internal_mod
	function get_vendors_products($vendor) {
		global $languages_id;

		if (!is_array($this->contents)) return false;

		$products_array = array();
		reset($this->contents);
			while (list($products_id, ) = each($this->contents)) {
				$products_query = tep_db_query("select p.products_id, pd.products_name, p.products_model, p.products_image, p.products_price, p.products_weight, p.products_length, p.products_width, p.products_height, p.products_ready_to_ship, p.products_tax_class_id, p.parent_products_model,  v.vendors_id, v.vendors_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_VENDORS . " v where p.products_id = '" . (int)$products_id . "' and pd.products_id = p.products_id and v.vendors_id = '" . $vendor . "' and p.vendors_id = '" . $vendor . "' and pd.language_id = '" . (int)$languages_id . "'");
				if ($products = tep_db_fetch_array($products_query)) {
				$prid = $products['products_id'];
				
				$products_price = $products['products_price'];
				$specials_price = tep_get_products_special_price($prid);
				if ($specials_price>0 && $specials_price<$products_price){
					$products_price = $specials_price;
				}
				if ( !empty($products['parent_products_model']) ){
					$parent_product_query = tep_db_query("select products_id, products_price from products where products_model='" . tep_db_input($products['parent_products_model']) . "'");
					
					if ( $parent_product = tep_db_fetch_array($parent_product_query) ){
						$products_price = $parent_product['products_price'];
						$specials_price = tep_get_products_special_price($parent_product['products_id']);
						if ($specials_price>0 && $specials_price<$products_price){
							$products_price = $specials_price;
						}
					}
				}

				$this->cg_id = $this->get_customer_group_id();
				if ($this->cg_id != 0){
					$customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (!empty($parent_product['products_id']) ? (int)$parent_product['products_id'] : (int)$prid )  . "' and customers_group_id =  '" . $this->cg_id . "'");
					if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
						$customer_group_products_price = $customer_group_price['customers_group_price'];
						if ($customer_group_products_price < $products_price){
							$products_price = $customer_group_products_price;
						}
					}
				}

				$products_array[] = array(
					'id' => $products_id,
					'name' => $products['products_name'],
					'model' => $products['products_model'],
					'image' => $products['products_image'],
					'price' => $products_price,
					'quantity' => $this->contents[$products_id]['qty'],
					'weight' => $products['products_weight'],
					//upsxml dimensions start
					'length' => ($products['product_free_shipping'] == '1' ? 0 : $products['products_length']),
					'width' => ($products['product_free_shipping'] == '1' ? 0 : $products['products_width']),
					'height' => ($products['product_free_shipping'] == '1' ? 0 : $products['products_height']),
					'ready_to_ship' => $products['products_ready_to_ship'],
					//upsxml dimensions end
					'final_price' => ($products_price + $this->attributes_price($products_id)),
					'tax_class_id' => $products['products_tax_class_id'],
					'vendors_id' => $products['vendors_id'],
					'vendors_name' => $products['vendors_name'],
					'attributes' => (isset($this->contents[$products_id]['attributes']) ? $this->contents[$products_id]['attributes'] : '')
				);
			}
		}
		return $products_array;
	}
//EOF:mvs_internal_mod
	
   
  }



?>
