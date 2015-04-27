<?php

/*

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2002 osCommerce



  Released under the GNU General Public License

  

  Featured Products V1.1

  Displays a list of featured products, selected from admin

  For use as an Infobox instead of the "New Products" Infobox  

*/

?>

<!-- featured_products //-->

<?php

 if(FEATURED_PRODUCTS_DISPLAY == 'true')

 {

  $featured_products_category_id = $new_products_category_id;

  $cat_name_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $featured_products_category_id . "' limit 1");

  $cat_name_fetch = tep_db_fetch_array($cat_name_query);

  $cat_name = $cat_name_fetch['categories_name'];

  $info_box_contents = array();



if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES. 'mobile/featured_320.jpg')) {

	$heading = '<div align="center"><a href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">'  . tep_image(DIR_WS_IMAGES . 'mobile/featured_320.jpg', TABLE_HEADING_FEATURED_PRODUCTS). '</a></div>';

} else {

	$heading = '<a class="headerNavigation" href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">' .TABLE_HEADING_FEATURED_PRODUCTS . '</a>';

}

  if ( (!isset($featured_products_category_id)) || ($featured_products_category_id == '0') ) {

    $info_box_contents[] = array('align' => 'left', 'text' => $heading . '</a>');



  list($usec, $sec) = explode(' ', microtime());

  srand( (float) $sec + ((float) $usec * 100000) );

  $mtm= rand();


//BOF:mod
/*
//EOF:mod
    $featured_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_FEATURED . " f on p.products_id = f.products_id where p.products_status = '1' and f.status = '1' order by rand($mtm) DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
//BOF:mod
*/
$featured_products_query = tep_db_query("select p.products_id, p.products_image, p.products_mediumimage, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_FEATURED . " f on p.products_id = f.products_id where p.products_status = '1' and f.status = '1' order by rand($mtm) DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
//EOF:mod
  } else {

    $info_box_contents[] = array('align' => 'left', 'text' => sprintf(TABLE_HEADING_FEATURED_PRODUCTS_CATEGORY, $cat_name));

    $subcategories_array = array();

    tep_get_subcategories($subcategories_array, $featured_products_category_id);

    $featured_products_category_id_list = tep_array_values_to_string($subcategories_array);

    if ($featured_products_category_id_list == '') {

      $featured_products_category_id_list .= $featured_products_category_id;

    } else {

      $featured_products_category_id_list .= ',' . $featured_products_category_id;

    }
//BOF:mod
/*
//EOF:mod
    $featured_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) left join " . TABLE_FEATURED . " f on p.products_id = f.products_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where c.parent_id = '" . $featured_products_category_id . "' and p.products_status = '1' and f.status = '1' order by rand() DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
//BOF:mod
*/
$featured_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_mediumimage, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) left join " . TABLE_FEATURED . " f on p.products_id = f.products_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where c.parent_id = '" . $featured_products_category_id . "' and p.products_status = '1' and f.status = '1' order by rand() DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
//EOF:mod
}



  $row = 0;

  $col = 0; 

  $num = 0;

  while ($featured_products = tep_db_fetch_array($featured_products_query)) {

    $num ++;

	 if ($num == 1) { 

	 	//new contentBoxHeading($info_box_contents); 

	 	echo $heading;

		 }

    $featured_products['products_name'] = tep_get_products_name($featured_products['products_id']);

    if (tep_not_null($featured_products['products_image'])) {

    	$feed_status = is_xml_feed_product($featured_products['products_id']);

  if ($feed_status) 
//BOF:mod
/*
//EOF:mod
   $image = tep_small_image($featured_products['products_image'], $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
//BOF:mod
*/
$image = tep_small_image($featured_products['products_mediumimage'], $featured_products['products_name'], MEDIUM_IMAGE_WIDTH, MEDIUM_IMAGE_HEIGHT,'class="subcatimages"');
//EOF:mod
    else 

   $image = tep_image(DIR_WS_IMAGES . $featured_products['products_image'], $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

   }

    if($featured_products['specstat']) {

      $info_box_contents[$row][$col] = array('align' => 'center',

                                           'params' => 'class="smallText" width="100%" valign="top" style="background-color:#999999;"',

                                           'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $featured_products['products_name'] . '</a><br><s>' . $currencies->display_price($featured_products['products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . 

                                           $currencies->display_price($featured_products['specials_new_products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])) . '</span>');

    } else {

      $info_box_contents[$row][$col] = array('align' => 'center',

                                           'params' => 'class="smallText" style="padding: 10px;" width="100%" valign="middle"',

                                           'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $featured_products['products_name'] . '</a><br>' . $currencies->display_price($featured_products['products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])));

    }

    $col ++;

    $info_box_contents[$row][$col] = array('align' => 'center',

                                           'params' => 'width="1px" valign="top"',

                                           'text' => '&nbsp;');

   $col++;

    if ($col > 0) {

	  $col = 0;

      $row ++;



      $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'style="margin: -3px; font-size: 8px"', 'text' => '&nbsp;');

      $col ++;

	  $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'style=""', 'text' => '');

      $col ++;

	  $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'style=""', 'text' => '');

      $col ++;

	  $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'style=""', 'text' => '');

      $col ++;

	  $info_box_contents[$row][$col] = array('align' => 'center', 'params' => 'style="" width="1px"', 'text' => '');



	  $col = 0;

      $row ++;

    }

  }

  if($num) {

      new contentBox($info_box_contents);

  }

 } else // If it's disabled, then include the original New Products box

 {

   // include (DIR_WS_MODULES . FILENAME_NEW_PRODUCTS); // disable for dont show if desactive the feature module

 }

?>

<!-- featured_products_eof //-->