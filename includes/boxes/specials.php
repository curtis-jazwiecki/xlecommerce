<?php
/*
  $Id: specials.php,v 1.31 2003/06/09 22:21:03 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
 //Categories Status MOD BEGIN by FIW
 // if ($random_product = tep_random_select("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_image, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where  c.categories_status = '1' and p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.products_id = p2c.products_id and p2c.categories_id = c.categories_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS)) {
 // 
// BOF Separate Pricing Per Customer

//  global variable (session): $sppc_customers_group_id -> local variable $customer_group_id

  if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
    $customer_group_id = $_SESSION['sppc_customer_group_id'];
  } else {
    $customer_group_id = '0';
  }

  if ($customer_group_id == '0')  {
      $random_product = tep_random_select("select p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.products_largeimage, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' and s.customers_group_id = '0' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS);
  } else { // $sppc_customer_group_id is in the session variables, so must be set
      $random_product = tep_random_select("select p.products_id, pd.products_name, IF(pg.customers_group_price IS NOT NULL,pg.customers_group_price, p.products_price) as products_price, p.products_tax_class_id, p.products_largeimage, s.specials_new_products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_SPECIALS . " s LEFT JOIN " . TABLE_PRODUCTS_GROUPS . " pg using (products_id, customers_group_id) where p.products_status = '1' and p.products_id = s.products_id and pd.products_id = s.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' and s.customers_group_id= '".$customer_group_id."' order by s.specials_date_added desc limit " . MAX_RANDOM_SELECT_SPECIALS);
    }

  if (tep_not_null($random_product)) {
// EOF Separate Pricing Per Customer

 //Categories Status MOD END by FIW
  	?>
<!-- specials //-->
          <tr>
            <td align="center">
<?php
    $info_box_contents = array();
    $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'specials.jpg', BOX_HEADING_CATEGORIES));
	
    new infoBoxHeading($info_box_contents, false, false, tep_href_link(FILENAME_SPECIALS));
    	$feed_status = is_xml_feed_product($random_product['products_id']);

// if ssl then hide picture - OBN
if ($_SERVER['HTTPS'] != "on")
  {
    if ($feed_status)
	  { $image = tep_small_image($random_product['products_largeimage'], $random_product['products_name'], '', '','class="boximages"'); }
	else
	  { $image = tep_image(DIR_WS_IMAGES . $random_product['products_largeimage'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); }   
  }
    $info_box_contents = array();
    $info_box_contents[] = array('align' => 'center',
	                             'width' => '100',
                                 'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product["products_id"]) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product['products_id']) . '">' . $random_product['products_name'] . '</a><br><s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>');

    new columnBox($info_box_contents);
?>

            </td>
          </tr>
<!-- specials_eof //-->
<?php
  }
?>
