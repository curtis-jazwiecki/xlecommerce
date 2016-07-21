<?php
/*
  $Id: ot_memberdiscount.php,v 1.11 2003/02/14 06:03:32 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  class ot_memberdiscount {
    var $title, $output;

    function ot_memberdiscount() {
      $this->code = 'ot_memberdiscount';
      $this->title = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_TITLE;
      $this->description = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_DESCRIPTION;
      $this->enabled = ((MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS == 'true') ? true : false);
      $this->sort_order = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_SORT_ORDER;
      $this->include_tax = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_INC_TAX;
      $this->calculate_tax = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_CALC_TAX;
      $this->include_shipping = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_INC_SHIPPING;
      $this->member_class = true;

      $this->output = array();
    }

    function process() {
    	global $order, $ot_subtotal, $currencies;
	    $od_amount = $this->calculate_credit($this->get_order_total());

// round discount to nearest cent. Discount of less than .5 cent will not be deducted from amount payable.
	$od_amount = round($od_amount, 2);
	if ($od_amount>0) { // deduct discount from amount payable
		$this->deduction = $od_amount;
		$this->output[] = array('title' => $this->title  . ' ' . MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT . '%:',
								'text' => '<b>-' . $currencies->format($od_amount) .'</b>' ,
								'value' => $od_amount);
		$order->info['total'] = $order->info['total'] - $od_amount;
		if ($this->sort_order < $ot_subtotal->sort_order) {
			$order->info['subtotal'] = $order->info['subtotal'] - $od_amount;
			}
		}
	} // end of function process()
function membership_selection() {
global $customer_id, $currencies, $language, $PHP_SELF;

        $selection_string  = tep_draw_form('membership', tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'), 'post');
        $selection_string .= '<tr><td><table width="90%"><tr><td width="40%">';
        $selection_string .= TEXT_ENTER_MEMBERSHIP_CODE ;
		  $selection_string .='</td><td align="left" width="25%">';
		 $selection_string .=tep_draw_input_field('membership_code', '') ;
		
		$selection_string .='</td><td align="center">';
        $selection_string .= tep_image_submit('button_redeem.gif', IMAGE_REDEEM_VOUCHER, 'onclick="return submitFunction()"') ;
        $selection_string .= '</td></tr></table></td></tr></form>';
		
  if (MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT > 0) {
$member_product = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT;    
$check_query = tep_db_query("select products_id, products_price, products_tax_class_id from products where products_id = '" . (int)$member_product . "'");
if (tep_db_num_rows($check_query)) {    
	$member = tep_db_fetch_array($check_query);
	$product_price = $currencies->display_price($member['products_price'], tep_get_tax_rate($member['products_tax_class_id']));
	$discount = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT . '%';
	$product_url = tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $member['products_id']);
		$selection_string .= '<tr><td>' . sprintf(TEXT_PRODUCT_PURCHASE, $product_url,$product_price, $discount).'</td></tr>' ;
		}
	}	
return $selection_string;
}	
	
function calculate_credit($amount_order) {
    global $order, $cart, $member_discount;
    $od_amount=0;
    $discount = false;
 if (tep_session_is_registered('member_discount')) {  
 	$discount = true;
 } else {
	$products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {	
    	if ($products[$i]['id'] == MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT) {
			$discount = true;
			break;
		}
	} 
 }
 
 if ($discount) {	
    $od_pc = MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT;
	$this->od_pc = $od_pc;
// Calculate tax reduction if necessary
    if($this->calculate_tax == 'true') {
// Calculate main tax reduction
      $tod_amount = $order->info['tax']*$od_pc/100;
      $order->info['tax'] = $order->info['tax'] - $tod_amount;
// Calculate tax group deductions
      reset($order->info['tax_groups']);
      while (list($key, $value) = each($order->info['tax_groups'])) {
        $god_amount = $value*$od_pc/100;
        $order->info['tax_groups'][$key] = $order->info['tax_groups'][$key] - $god_amount;
      }  
    }
    $od_amount = $amount_order*$od_pc/100;
   // $od_amount = $od_amount + $tod_amount;
     }
    return $od_amount;
  }	
  
function get_order_total() {
    global  $order, $cart;
    $order_total = $order->info['total'];    
    if ($this->include_tax == 'false') $order_total=$order_total-$order->info['tax'];
    if ($this->include_shipping == 'false') $order_total=$order_total-$order->info['shipping_cost'];
    return $order_total;
  }   
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }
    
  function get_error() {
      global $HTTP_GET_VARS;

      $error = array('title' => MODULE_ORDER_TOTAL_MEMBERDISCOUNT_TEXT_ERROR,
                     'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));

      return $error;
    }   

    function keys() {
      return array('MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_SORT_ORDER', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT','MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT','MODULE_LOYALTY_MEMBERDISCOUNT_INC_SHIPPING', 'MODULE_LOYALTY_MEMBERDISCOUNT_INC_TAX', 'MODULE_LOYALTY_MEMBERDISCOUNT_CALC_TAX');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display Membership discount', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_STATUS', 'true', 'Do you want to display membership discount?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_SORT_ORDER', '4', 'Sort order of display.', '6', '2', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Membership Discount %', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_AMOUNT', '1', 'Percentage of membership discount to be applied', '6', '3',  now())");
       tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Membership Product ID', 'MODULE_ORDER_TOTAL_MEMBERDISCOUNT_PRODUCT', '138993', 'Product Id of membership product in the catalog', '6', '3', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Shipping', 'MODULE_LOYALTY_MEMBERDISCOUNT_INC_SHIPPING', 'true', 'Include Shipping in calculation', '6', '3', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Include Tax', 'MODULE_LOYALTY_MEMBERDISCOUNT_INC_TAX', 'true', 'Include Tax in calculation.', '6', '4','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function ,date_added) values ('Calculate Tax', 'MODULE_LOYALTY_MEMBERDISCOUNT_CALC_TAX', 'false', 'Re-calculate Tax on discounted amount.', '6', '5','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }
  }
?>
