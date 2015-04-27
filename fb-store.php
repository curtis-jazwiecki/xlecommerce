<?php 
header('Cache-Control: no-cache, must-revalidate');
header('Expires: Mon, 26 Jul 2014 05:00:00 GMT');
header('Content-type: application/json');

require('includes/application_top.php');
// include(DIR_WS_MODULES . FILENAME_FEATURED);
define('DIR_WS_IMAGES', 'images/');
?>

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
<?php
  if(FACEBOOK_STORE=='ON')
  {
  // container for data feed
  $data = array();
  $item = array();

  $featured_products_category_id = $new_products_category_id;
  $cat_name_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $featured_products_category_id . "' limit 1");
  $cat_name_fetch = tep_db_fetch_array($cat_name_query);
  $cat_name = $cat_name_fetch['categories_name'];
  $info_box_contents = array();


if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES. 'featured_header.png')) {
	$featured_header = '<a href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">'  . '<img src="https://'.$_SERVER['HTTP_HOST'].'/'.DIR_WS_IMAGES . 'featured_header.png" /></a>';
} else {
	$heading = '<a class="headerNavigation" href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">' .TABLE_HEADING_FEATURED_PRODUCTS . '</a>';
}
  if ( (!isset($featured_products_category_id)) || ($featured_products_category_id == '0') ) {
    $info_box_contents[] = array('align' => 'left', 'text' => $heading . '</a>');

    list($usec, $sec) = explode(' ', microtime());
    srand( (float) $sec + ((float) $usec * 100000) );
    $mtm= rand();

    $featured_products_query = tep_db_query("select p.products_id, p.products_image, p.products_mediumimage, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_FACEBOOKSTORE . " f on p.products_id = f.products_id where p.products_status = '1' and f.status = '1' order by rand($mtm) DESC limit " . FACEBOOK_STORE_LIMIT);
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
    $featured_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) left join " . TABLE_FACEBOOKSTORE . " f on p.products_id = f.products_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where c.parent_id = '" . $featured_products_category_id . "' and p.products_status = '1' and f.status = '1' order by rand() DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
}
  $row = 0;
  $col = 0; 
  $num = 0;

  $i = 0;
  while ($featured_products = tep_db_fetch_array($featured_products_query)) {

    // this item's container
    $item = array();

    $item = $featured_products;
    // get product name
    $item['products_name'] = strip_tags(tep_get_products_name($featured_products['products_id']));

    // get product image
    if (tep_not_null($featured_products[$i]['products_image'])) {

      $feed_status = is_xml_feed_product($featured_products[$i]['products_id']);

      if ($feed_status) {
      // here is a small image
      $image = tep_image($featured_products[$i]['products_mediumimage'], $featured_products[$i]['products_name'], '', '','class="subcatimages"');
      }
      else {
      // here is a reg image
      $image = tep_image(DIR_WS_IMAGES . $featured_products[$i]['products_image'], $featured_products[$i]['products_name'], '', '','class="subcatimages"');
      }
      $item['image'] = $image;
    }

    $item['products_link'] = tep_get_product_path($featured_products['products_id']);

    // load'er up!
    $data['items'][] = $item;
    $i++;
  }

  // format array into json object and spit out data
  $json_data = json_encode($data);
  echo $json_data;
  }
?>
