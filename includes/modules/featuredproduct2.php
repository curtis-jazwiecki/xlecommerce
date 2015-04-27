<?
/*
$Id: xsell_products.php, v1  2002/09/11
// adapted for Separate Pricing Per Customer v4 2005/02/24

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License
*/
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_XSELL_PRODUCTS);

if ($HTTP_GET_VARS['products_id'] && ENABLE_FEATURE_PRODUCTS_TWO=='True') {
    
$featured_query_2 = tep_db_query("select p.products_id,p.products_image,p.products_price,p.products_tax_class_id, pd.products_name, s.featured_id, s.expires_date, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = s.products_id and s.featured_group = '2' and s.status = '1' and s.expires_date > now() order by pd.products_name LIMIT 0 , ".FEATURED_PRODUCT_2); //ORDER BY p.reviews_rating DESC

// EOF Separate Pricing Per Customer
$num_featured_product_2 = tep_db_num_rows($featured_query_2);

// EOF Separate Pricing Per Customer
//$num_products_xsell = tep_db_num_rows($featured_query_1);

if ($num_featured_product_2 > 0) {

?>
<!-- xsell_products //-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
<tr><td class="xcell">
<?
     $info_box_contents = array();
     $info_box_contents[] = array('align' => 'left', 'text' => TWO_FEATURED_PRODUCT_TITLE);
     new contentBoxHeading($info_box_contents);

     $row = 0;
     $col = 0;
     $info_box_contents = array();
     while ($featured2 = tep_db_fetch_array($featured_query_2)) {
         
       $featured2['specials_new_products_price'] = tep_get_products_special_price($featured2['products_id']);

if ($featured2['specials_new_products_price']) {
     $featured2_price =  '<s>' . $currencies->display_price($featured2['products_price'], tep_get_tax_rate($featured2['products_tax_class_id'])) . '</s><br>';
     $featured2_price .= '<span class="productSpecialPrice">' . $currencies->display_price($featured2['specials_new_products_price'], tep_get_tax_rate($featured2['products_tax_class_id'])) . '</span>';
   } else {
     $featured2_price =  $currencies->display_price($featured2['products_price'], tep_get_tax_rate($featured2['products_tax_class_id']));
   }
   
   if (tep_not_null($featured2['products_image'])) {
    	$feed_status = is_xml_feed_product($featured2['products_id']);
  if ($feed_status) 
   $image = tep_small_image($featured2['products_image'], $featured2['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $featured2['products_image'], $featured2['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
   }
       $info_box_contents7[$row][$col] = array('align' => 'center',
                                              'params' => 'class="main" valign="top"',
                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured2['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured2['products_id']) . '"class="xcell"><b>' . $featured2['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $featured2_price. '</td></tr></table></td></tr></table>');
       $col ++;
       if (0 < $col) {
         $col = 0;
         $row ++;
       }
     }
     new contentBox($info_box_contents7);
?>
</td></tr></table>
<!-- xsell_products_eof //-->
<?

   }
   
 }
?>