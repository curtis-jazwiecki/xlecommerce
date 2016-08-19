<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

*/
$template17_blocks = array(
'Ad_link',
'Gift_cards',
'Offer',
'header_logo',
'search_box',
'Categories',
'sign_in',
'join',
'slider',
'infobox1',
'infobox2',
'infobox3',
'infobox4',
'infobox5',
'infobox6',
'infobox7',
'infobox8',
'infobox9',
'infobox10',
'coupon',
'offer2',
'review',
'footer_box_01',
'footer_box_02',
'footer_box_03',
'footer_box_04',
'sign_up',
'follow_us_links',
'footer_box_links',
'footer_main',
);

$sts->blocks = array();
foreach($template17_blocks as $block){
	$sts->template[$block] = get_block_content('template17', $block);
    //$sts->blocks[$block] = get_block_content('template11', $block);
}
?>
