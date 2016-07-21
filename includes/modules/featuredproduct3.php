<?
/*
$Id: xsell_products.php, v1  2002/09/11
// adapted for Separate Pricing Per Customer v4 2005/02/24

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_XSELL_PRODUCTS);

//if ($HTTP_GET_VARS['products_id'] && ENABLE_FEATURE_PRODUCTS_THREE=='True') {
	
    
$featured_query_3 = tep_db_query("select p.products_id,p.products_image,p.products_price,p.products_tax_class_id, pd.products_name, s.featured_id, s.expires_date, s.status from " . TABLE_PRODUCTS . " p, " . TABLE_FEATURED . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_id = s.products_id and s.featured_group = '3' and s.status = '1' and (s.expires_date > now() or s.expires_date = '0000-00-00 00:00:00') order by pd.products_name LIMIT 0 , ".FEATURED_PRODUCT_3); //ORDER BY p.reviews_rating DESC

// EOF Separate Pricing Per Customer
$num_featured_product_3 = tep_db_num_rows($featured_query_3);

// EOF Separate Pricing Per Customer
//$num_products_xsell = tep_db_num_rows($featured_query_3);

if ($num_featured_product_3 > 0) {

?>
<!-- xsell_products //-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
<tr><td class="xcell">
<?
     $info_box_contents = array();
     $info_box_contents[] = array('align' => 'left', 'text' => THREE_FEATURED_PRODUCT_TITLE);
     new contentBoxHeading($info_box_contents);

     $row = 0;
     $col = 0;
     $info_box_contents = array();
     while ($featured3 = tep_db_fetch_array($featured_query_3)) {
         
       $featured3['specials_new_products_price'] = tep_get_products_special_price($featured3['products_id']);

if ($featured3['specials_new_products_price']) {
     $featured3_price =  '<s>' . $currencies->display_price($featured3['products_price'], tep_get_tax_rate($featured3['products_tax_class_id'])) . '</s><br>';
     $featured3_price .= '<span class="productSpecialPrice">' . $currencies->display_price($featured3['specials_new_products_price'], tep_get_tax_rate($featured3['products_tax_class_id'])) . '</span>';
   } else {
     $featured3_price =  $currencies->display_price($featured3['products_price'], tep_get_tax_rate($featured3['products_tax_class_id']));
   }
   
   if (tep_not_null($featured3['products_image'])) {
    	$feed_status = is_xml_feed_product($featured3['products_id']);
  if ($feed_status) 
   $image = tep_small_image($featured3['products_image'], $featured3['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $featured3['products_image'], $featured3['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
   }
       $info_box_contents8[$row][$col] = array('align' => 'center',
                                              'params' => 'class="main" valign="top"',
                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured3['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured3['products_id']) . '"class="xcell"><b>' . $featured3['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $featured3_price. '</td></tr></table></td></tr></table>');
       $col ++;
       if (0 < $col) {
         $col = 0;
         $row ++;
       }
     }
     new contentBox($info_box_contents8);
?>
</td></tr></table>
<!-- xsell_products_eof //-->
<?

   }
   
 
//}
?>