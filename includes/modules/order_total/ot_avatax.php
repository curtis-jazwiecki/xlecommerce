<?php
/**
 * ot_avatax order-total module
 *
 * @package orderTotal
 * @copyright Portions Copyright 2003 osCommerce
 * @copyright Portions Copyright (c) 2002 Thomas Pl�nkers http://www.oscommerce.at
 * @version $Id: ot_avatax.php 14820 2012-11-23 16:00:00Z adb $
 */
/**
 * osCommerce Connector for AvaTax Calc Order Totals Module
 *
 */

class ot_avatax {
  var $title, $output;

  function ot_avatax() {
    $this->code = 'ot_avatax';
    $this->title = MODULE_ORDER_TOTAL_AVATAX_TITLE;
    $this->description = MODULE_ORDER_TOTAL_AVATAX_DESCRIPTION;
    $this->enabled = ((MODULE_ORDER_TOTAL_AVATAX_STATUS == 'true') ? true : false);
    $this->sort_order = MODULE_ORDER_TOTAL_AVATAX_SORT_ORDER;

    $this->output = array();
  }

  function process() {

  /**
   * Method used to calculate sales tax using AvaTax Calc and to produce the output<br>
   * shown on the checkout pages
   */

    // Calculate Tax
    require_once DIR_WS_MODULES . 'avatax/func.avatax.php';

    global $order, $currencies;

    // added on 23-06-2016 #start
	// call only once if cart has been altered or else fetch from session
	global $cart,$cartID;
	
	if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
		
		if ($cart->cartID != $cartID) {
			
		  $tax_data = avatax_lookup_tax($order, $order->products);
		  $_SESSION['tax_data'] = $tax_data;
		
		}else if( ($cart->cartID == $cartID) && (empty($_SESSION['tax_data'])) ){
		
		  $tax_data = avatax_lookup_tax($order, $order->products);
		  $_SESSION['tax_data'] = $tax_data;
		
		}
  	
	}	

	$tax_data = $_SESSION['tax_data'];
		

	
	
	// added on 23-06-2016 #start
	
	
    $tax_amt = $tax_data['tax_amount'];
	
	if(empty($tax_amt)){
		return;
	}

    $order->info['tax'] = $tax_amt;

    $loccode = $order->delivery['city'];
    $namesuf = '';
    if (MODULE_ORDER_TOTAL_AVATAX_LOCATION == 'true') {
      $namesuf = ' (' . $loccode . ') ';
    }
    $taxname = MODULE_ORDER_TOTAL_AVATAX_DESCRIPTION . $namesuf . '';

    $order->info['tax_groups'] = array ($taxname => $tax_amt);

    // Produce Sales Tax output for the checkout page
    reset($order->info['tax_groups']);
    $taxDescription = '';
    $taxValue = 0;

    while (list($key, $value) = each($order->info['tax_groups'])) {
      if ($value > 0 ) {
        $taxDescription .= ((is_numeric($key) && $key == 0) ? TEXT_UNKNOWN_TAX_RATE :  $key) . ' + ';
        $taxValue += $value;
      }
    }

    $order->info['total'] += $taxValue;

    
	
	
	$this->output[] = array(
                     'title' => substr($taxDescription, 0 , strlen($taxDescription)-3) . ':' ,
                     'text' => $currencies->format($taxValue, true, $order->info['currency'], $order->info['currency_value']) ,
                     'value' => $taxValue);

    // var_dump($this->output);
    // break;

  }

  function check() {
    global $db;
    if (!isset($this->_check)) {
      $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_ORDER_TOTAL_AVATAX_STATUS'");
      $this->_check = tep_db_num_rows($check_query);
    }
    return $this->_check;
  }

  function keys() {
    return array('MODULE_ORDER_TOTAL_AVATAX_STATUS', 'MODULE_ORDER_TOTAL_AVATAX_SORT_ORDER', 'MODULE_ORDER_TOTAL_AVATAX_VERSION', 'MODULE_ORDER_TOTAL_AVATAX_CODE', 'MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS', 'MODULE_ORDER_TOTAL_AVATAX_DESCRIPTION', 'MODULE_ORDER_TOTAL_AVATAX_LOCATION', 'MODULE_ORDER_TOTAL_AVATAX_VALIDATE', 'MODULE_ORDER_TOTAL_AVATAX_UPDATE', 'MODULE_ORDER_TOTAL_AVATAX_CITY', 'MODULE_ORDER_TOTAL_AVATAX_COUNTY', 'MODULE_ORDER_TOTAL_AVATAX_STATE', 'MODULE_ORDER_TOTAL_AVATAX_ZIPCODE','MODULE_ORDER_TOTAL_AVATAX_STREET_ADDRESS','MODULE_ORDER_TOTAL_AVATAX_SUBURB', 'MODULE_ORDER_TOTAL_AVATAX_EXEMPTION','MODULE_ORDER_TOTAL_AVATAX_COMMIT','MODULE_ORDER_TOTAL_AVATAX_VOID','MODULE_ORDER_TOTAL_AVATAX_FREIGHT_TAX_CODE' );
  }

  function install() {
    global $db;
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Display AvaTax', 'MODULE_ORDER_TOTAL_AVATAX_STATUS', 'true', 'Do you want this module to display?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sort Order', 'MODULE_ORDER_TOTAL_AVATAX_SORT_ORDER', '3', 'Sort order of display', '6', '2', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Avalara Version', 'MODULE_ORDER_TOTAL_AVATAX_VERSION', 'Trial', 'Select AvaTax version', '6', '3','tep_cfg_select_option(array(\'Basic\', \'Pro\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Avalara Company Code', 'MODULE_ORDER_TOTAL_AVATAX_CODE', '', 'Company Code', '6', '4', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Sales Tax Description', 'MODULE_ORDER_TOTAL_AVATAX_DESCRIPTION', 'Sales Tax', 'Sales Tax Description', '6', '4', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Account Type', 'MODULE_ORDER_TOTAL_AVATAX_DEV_STATUS', 'Development', 'Type of account?', '6', '5', 'tep_cfg_select_option(array(\'Production\', \'Development\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Show Location Code', 'MODULE_ORDER_TOTAL_AVATAX_LOCATION', 'true', 'Show location code?', '6', '6', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Avatax Address Validation', 'MODULE_ORDER_TOTAL_AVATAX_VALIDATE', 'false', 'Validate address?', '6', '7', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Automatic updates', 'MODULE_ORDER_TOTAL_AVATAX_UPDATE', 'false', 'Automatically update AvaTax order status?', '6', '8', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Primary City', 'MODULE_ORDER_TOTAL_AVATAX_CITY', '', 'Primary City', '6', '9', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Primary County', 'MODULE_ORDER_TOTAL_AVATAX_COUNTY', '', 'Primary County', '6', '10', now())");
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Primary State', 'MODULE_ORDER_TOTAL_AVATAX_STATE', '', 'Primary State', '6', '11', now())");
    
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Primary Zip Code', 'MODULE_ORDER_TOTAL_AVATAX_ZIPCODE', '', 'Primary Zip Code', '6', '12', now())");
	
	// modified #EMM 23-06-2016 #start
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Street Address', 'MODULE_ORDER_TOTAL_AVATAX_STREET_ADDRESS', '', 'Street Address', '6', '13', now())");
	
	tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Suburb', 'MODULE_ORDER_TOTAL_AVATAX_SUBURB', '', 'Suburb', '6', '14', now())");
	
	
	// modified #EMM 23-06-2016 #ends
	
	
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Sales Tax Exemptions', 'MODULE_ORDER_TOTAL_AVATAX_EXEMPTION', 'false', 'Allow role based sales tax exemption?', '6', '15', 'tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    
    // modified #EMM 06-04-2016 #start
     
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status For Commit', 'MODULE_ORDER_TOTAL_AVATAX_COMMIT', '0', 'Set the status of order to commit tax to avalara', '6', '16', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");
    
    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, use_function, date_added) values ('Set Order Status For Voided', 'MODULE_ORDER_TOTAL_AVATAX_VOID', '0', 'Set the status of order to void tax to avalara', '6', '17', 'tep_cfg_pull_down_order_statuses(', 'tep_get_order_status_name', now())");

    // modified #EMM 06-04-2016 #ends
	
	
	// modified #EMM 23-06-2016 #start
     
   tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Freight Tax Code', 'MODULE_ORDER_TOTAL_AVATAX_FREIGHT_TAX_CODE', '', 'Freight Tax Code', '6', '18', now())");
   
    // modified #EMM 23-06-2016 #start
	
  }

  function remove() {
    global $db;
    $keys = '';
    $keys_array = $this->keys();
    $keys_size = sizeof($keys_array);
    for ($i=0; $i<$keys_size; $i++) {
      $keys .= "'" . $keys_array[$i] . "',";
    }
    $keys = substr($keys, 0, -1);
    tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in (" . $keys . ")");
  }
}