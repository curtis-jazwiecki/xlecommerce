<?
/*
$Id: new_products.php, v1  2002/09/11
// adapted for Separate Pricing Per Customer v4 2005/02/24

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License
*/
if (ENABLE_NEW_PRODUCTS=='True') {
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_XSELL_PRODUCTS);
        
//$newproduct_query = tep_db_query("select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where DATEDIFF( now( ) , p.products_date_added ) < 31 and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY datedif desc  limit " . MAX_DISPLAY_RECOMMEND_PRODUCT); //ORDER BY p.reviews_rating DESC
$newproduct_query = tep_db_query("select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where DATEDIFF( now( ) , p.products_date_added ) < 31 and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY datedif desc limit ".MAX_DISPLAY_NEW_PRODUCT); //ORDER BY p.reviews_rating DESC

//echo "select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where DATEDIFF( now( ) , p.products_date_added ) < 31 and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY datedif desc ";

// EOF Separate Pricing Per Customer
$num_new_products = tep_db_num_rows($newproduct_query); 

// EOF Separate Pricing Per Customer
//$num_products_xsell = tep_db_num_rows($new_products_query);

if ($num_new_products > 0) {

?>
<!-- xsell_products //-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
<tr><td class="xcell">
<?
     $info_box_contents = array();
     $info_box_contents[] = array('align' => 'left', 'text' => 'New Products');
     new contentBoxHeading($info_box_contents);

     $row = 0;
     $col = 0;
     $info_box_contents = array();
     while ($new_products = tep_db_fetch_array($newproduct_query)) {
        
       $new_products['specials_new_products_price'] = tep_get_products_special_price($new_products['products_id']);

if ($new_products['specials_new_products_price']) {
     $new_products_price =  '<s>' . $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) . '</s><br>';
     $new_products_price .= '<span class="productSpecialPrice">' . $currencies->display_price($new_products['specials_new_products_price'], tep_get_tax_rate($new_products['products_tax_class_id'])) . '</span>';
   } else {
     $new_products_price =  $currencies->display_price($new_products['products_price'], tep_get_tax_rate($new_products['products_tax_class_id']));
   }
   
   if (tep_not_null($new_products['products_image'])) {
    	$feed_status = is_xml_feed_product($new_products['products_id']);
  if ($feed_status) 
   $image = tep_small_image($new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $new_products['products_image'], $new_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
   }
       $info_box_contents3[$row][$col] = array('align' => 'center',
                                              'params' => 'class="main" valign="top"',
                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $new_products['products_id']) . '"class="xcell"><b>' . $new_products['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $new_products_price. '</td></tr></table></td></tr></table>');
       $col ++;
       if (0 < $col) {
         $col = 0;
         $row ++;
       }
       
     }
     new contentBox($info_box_contents3);
?>
</td></tr></table>
<!-- xsell_products_eof //-->
<?
}
   }   
?>