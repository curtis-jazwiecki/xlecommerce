<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$template14_blocks = array(
'best_Sellers',
'featured_deal',
'footer_main',
'footer_box_01',
'footer_box_02',
'footer_box_03',
'guide',
'new_product',
'header_logo',
'navigation_menu_top',
'newsletter',
'quick_links',
'search_box',
'shop_now_sale',
'contact_us',
'on_sale',
'popular_brands',
'shopping_cart',
'slider',
'social_bookmarks',
'subscribe',
'top_rated',
);

$sts->blocks = array();
foreach($template14_blocks as $block){
	$sts->template[$block] = get_block_content('template14', $block);
    //$sts->blocks[$block] = get_block_content('template11', $block);
}
?>
