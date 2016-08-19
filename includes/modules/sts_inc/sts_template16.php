<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$template16_blocks = array(
	'footer_main',
	'footer_box_01',
	'footer_box_02',
	'footer_box_03',
	'footer_box_04',
	'my_cart',
	'header_logo',
	'navigation_menu_top',
	'quick_links',
	'search_box',
	'shop_now',
	'shop_now_box_1',
	'shop_now_box_2',
	'shop_now_box_3',
	'sign_in',
	'slider',
	'infobox_01' => array( 
		'block_name' => 'infobox_01', 
		'type'=>'related', 
		'enabled'=>true, 'parameters'=>array() 
	), 
	'infobox_02' => array( 
		'block_name' => 'infobox_02', 
		'type'=>'featured_categories', 
		'enabled'=> ENABLE_FEATURE_CATEGORY=='True' ? true : false, 
		'parameters'=>array() 
	), 
	'infobox_03' => array( 
		'block_name' => 'infobox_03', 
		'type'=>'featured_manufacturers', 
		'enabled'=> ENABLE_FEATURE_MANUFACTURERS=='True' ? true : false, 
		'parameters'=>array() 
	), 
	'infobox_04' => array( 
		'block_name' => 'infobox_04', 
		'type'=>'hot', 
		'enabled'=> ENABLE_HOT_PRODUCTS=='True' ? true : false, 
		'parameters'=>array() 
	), 
	'infobox_05' => array( 
		'block_name' => 'infobox_05', 
		'type'=>'recommended', 
		'enabled'=> ENABLE_RECOMENDED_PRODUCTS=='True' ? true : false, 
		'parameters'=>array() 
	), 
	'infobox_06' => array( 
		'block_name' => 'infobox_06', 
		'type'=>'popular', 
		'enabled'=> ENABLE_POPULAR_PRODUCTS=='True' ? true : false, 
		'parameters'=>array() 
	), 
);

$sts->blocks = array();
foreach($template11_blocks as $block){
	if (is_array($block)){
		$is_enabled = $block['enabled'];
		if (!$is_enabled){
			$sts->template[$block['block_name']] = '';
		} else {
			ob_start();
			switch( strtolower( $block['type'] ) ){
				case 'related':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'xsell_products.php');
					$val = ob_get_contents();
					break;
				case 'featured_categories':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredCategory.php');
					$val = ob_get_contents();
					break;
				case 'featured_manufacturers':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredManufacturers.php');
					$val = ob_get_contents();
					break;
				case 'hot':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'hot_products.php');
					$val = ob_get_contents();
					break;
				case 'recommended':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'recomended.php');
					$val = ob_get_contents();
					break;
				case 'popular':
					include(DIR_FS_CATALOG . DIR_WS_MODULES .  'popular_products.php');
					$val = ob_get_contents();
					break;
			}
			ob_end_clean();
			$sts->template[$block['block_name']] = $val;
		}
	} else {
		$sts->template[$block] = get_block_content('template16', $block);
	}
}	
?>
