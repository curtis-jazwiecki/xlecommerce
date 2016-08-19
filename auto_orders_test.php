<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');
include_once(DIR_FS_ADMIN . 'OBN_order_feed_manager.php');

$retailer_dir = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/';

$suppliers = array();
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
				$suppliers[] = $supplier_prefix;
			}
		}
		fclose($handle);
	}
}

$orders_id='346';
$order_product_ids = array();
$products_query = tep_db_query("select products_id as id, products_model as model from orders_products where orders_id='" . (int)(int)$orders_id . "'");
while ($entry = tep_db_fetch_array($products_query)){
	$pos = strpos($entry['model'], '-');
	if ($pos!==false){
		$prefix = substr($entry['model'], 0, $pos+1);
		if (in_array($prefix, $suppliers)){
			$order_product_ids[] = $entry['id'];
		}
	}
}

if (!empty($order_product_ids)){
	$order_feed = new order_feed($orders_id, $order_product_ids);
	$order_feed->get_order_feed();
	$file_path = DIR_FS_OBN_FEED . RETAILER_TOKEN_ID . '/outgoing/';
	$file_name = time() . '.xml';
	$handle = fopen($file_path . $file_name , 'x');
	chmod($file_path . $file_name, 0777);
	fwrite($handle, $order_feed->xml);
	fclose($handle);
	unset($order_feed);
}
exit;
require(DIR_WS_INCLUDES . 'application_bottom.php');
?>