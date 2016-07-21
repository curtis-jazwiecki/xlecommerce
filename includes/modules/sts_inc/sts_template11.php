<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$template11_blocks = array(
	'header_logo', 
    'search_box', 
    'quick_links',
    'navigation_menu_top', 
    'slider', 
    'box_01', 
    'box_02', 
    'box_03', 
    'box_04', 
    'featured_products' => array( 
		'block_name' => 'featured_products', 
		'type'=>'featured', 
		//'enabled'=> ENABLE_POPULAR_PRODUCTS=='True' ? true : false, 
                'enabled' => true, 
		'parameters'=>array('custom'=>true, 'on_home_page'=>true, 'max_count'=>'4'),  
	),  
    'footer_box_01', 
    'footer_box_02', 
    'footer_box_03', 
    'footer_box_04', 
    'footer_box_05', 
    'footer',
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
                                case 'featured':
                                    $custom = false;
                                    $on_home_page = false;
                                    $max_count = null;
                                    $items_per_row = null;
                                    if (!empty($block['parameters']['custom'])){
                                        $custom = true;
                                    }
                                    if (!empty($block['parameters']['on_home_page'])){
                                        $on_home_page = true;
                                    }
                                    if (!empty($block['parameters']['max_count'])){
                                        $max_count = $block['parameters']['max_count'];
                                    }
                                    if (!empty($block['parameters']['items_per_row'])){
                                        $items_per_row = $block['parameters']['items_per_row'];
                                    }
                                    include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featured.php');
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
		$sts->template[$block] = get_block_content('template11', $block);
	}
}
?>
