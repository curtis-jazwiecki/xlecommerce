<?php
require('includes/application_top.php');

function tep_get_deepest_category($parent_id = '0') {
	$categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "' order by sort_order");
	if (tep_db_num_rows($categories_query) > 0) {
		while ($categories = tep_db_fetch_array($categories_query)) {
			$categories_query2 = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories['categories_id']. "' order by sort_order");
			$categories2 = tep_db_fetch_array($categories_query2);
			if ($categories2['total'] >0) {
				$deepest_category  = tep_get_deepest_category($categories['categories_id']);
				if ($deepest_category >0) break;
			} else {
				$deepest_category = 	$categories['categories_id'];
				break;
			}
		} 
	} else {
		$deepest_category = $parent_id;
	}
    return $deepest_category;
}
$category_image_query = tep_db_query("select categories_id, categories_image from categories where (categories_image='' or categories_image is null)");
//$count = 0;
//$upper_limit = 15;
while ($entry = tep_db_fetch_array($category_image_query)){
	$count++;
	$deespest_category  = tep_get_deepest_category($entry['categories_id']);
	$products_in_category = tep_count_products_in_category($deespest_category);
	if($products_in_category>0){
		$image_located = false;
		$products_skipped = array();
		
		$tableP = 'products';
		//if(USE_FRONTEND_CATEGORIES == 'true'){
		//	$tableP2C = 'frontend_products_to_categories';
		//} else {
			$tableP2C = 'products_to_categories';
		//}
		$placeholders = array('{tableP}', '{tableP2C}');
		$replacements = array($tableP, $tableP2C);
		$image_query = "SELECT {tableP}.products_mediumimage, {tableP}.products_id FROM {tableP2C} JOIN {tableP} ON {tableP}.products_id = {tableP2C}.products_id WHERE {tableP2C}.categories_id = '".(int)$deespest_category . "' {productsToSkip} order by rand() limit 1";
		$image_query = str_replace($placeholders, $replacements, $image_query);
				
		while (!$image_located){
			if (empty($products_skipped)){
				$amended_query = str_replace('{productsToSkip}', '', $image_query);
			} else {
				$query_part = " and {tableP}.products_id not in (" . implode(', ', $products_skipped) . ") ";
				$query_part = str_replace('{tableP}', $tableP, $query_part);
				$amended_query = str_replace('{productsToSkip}', $query_part, $image_query);
			}
			$query = tep_db_query($amended_query);
			$resultset = tep_db_fetch_array($query);
			$image_name = $resultset['products_mediumimage'];
			if (stripos($image_name, 'http')!==false && stripos($image_name, 'http')==0){
				$image_full_path = $image_name;
			} else {
				$image_full_path = DIR_WS_IMAGES . $image_name;
				
			}
			if (!@getimagesize($image_full_path)){
				$products_skipped[] = $resultset['products_id'];
				if (count($products_skipped)==$products_in_category){
					exit();
				}
			} else {
				$image_located = true;
			}
		}
		if ($image_located){
			tep_db_query("update categories set categories_image='" . tep_db_input($image_name) . "' where categories_id='" . (int)$entry['categories_id'] . "'");
			echo $entry['categories_id'] . ': ' . $image_name . "\n";
		} else {
			echo $entry['categories_id'] . ': No mapping' . "\n";
		}
	} else {
		echo $entry['categories_id'] . ': No products' . "\n";
	}
	//if ($count>=$upper_limit){
	//	break;
	//}
}