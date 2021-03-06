<?

/*
$Id: new_products.php, v1  2002/09/11
// adapted for Separate Pricing Per Customer v4 2005/02/24

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2002 osCommerce

Released under the GNU General Public License
*/
if (ENABLE_POPULAR_PRODUCTS=='True') {
//require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_XSELL_PRODUCTS);
        
//$popular_query = tep_db_query("select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where DATEDIFF( now( ) , p.products_date_added ) < 31 and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY datedif desc  limit " . MAX_DISPLAY_RECOMMEND_PRODUCT); //ORDER BY p.reviews_rating DESC
//$popular_query = tep_db_query("select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY pd.products_viewed limit ".MAX_DISPLAY_POPULAR_PRODUCT); //ORDER BY p.reviews_rating DESC
$popular_query = tep_db_query("select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, p.products_mediumimage, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY pd.products_viewed desc  limit " . (!empty($max_count) ? $max_count : MAX_DISPLAY_POPULAR_PRODUCT) ); //ORDER BY p.reviews_rating DESC

//echo "select DATEDIFF( now( ) , p.products_date_added ) AS datedif, p.products_id, p.products_image, pd.products_name, p.products_tax_class_id, p.products_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where DATEDIFF( now( ) , p.products_date_added ) < 31 and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' ORDER BY datedif desc ";

// EOF Separate Pricing Per Customer
$num_popular_products = tep_db_num_rows($popular_query); 

// EOF Separate Pricing Per Customer
//$num_products_xsell = tep_db_num_rows($popular_products_query);

if ($num_popular_products > 0) {	
    //if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && ( ( ( !isset($on_home_page) || !$on_home_page ) && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_06.php.html') ) || ( $on_home_page && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/popular_products.php.html')  ) ) ) {	
    if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_06.php.html') ) {	
        //if (!isset($on_home_page) || !$on_home_page){
		$content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_06.php.html');
        		
        $output = '';
        $header_bof = stripos($content, '<!--header_bof-->');		
        $header_eof = stripos($content, '<!--header_eof-->');		
        if ($header_bof!==false && $header_eof!==false){			
            $header_exists = true;			
            $header_content = substr( $content,  $header_bof, $header_eof - $header_bof );			
            $header_content = substr( $header_content,  stripos( $header_content, '>' ) + 1 );			
            $header_content = str_ireplace('$header', 'Popular Products', $header_content);		
        } else {			
            $header_exists = false;			
            $header = '';			
            $header_content = '';		
        }		
        $output .= $header_content;				
        $block_bof = stripos($content, '<!--block_bof-->');		
        $block_eof = stripos($content, '<!--block_eof-->');		
        if ($block_bof!==false && $block_eof!==false){			
            $block_exists = true;			
            $block_content = substr( $content,  $block_bof, $block_eof - $block_bof );			
            $block_content = substr( $block_content,  stripos( $block_content, '>' ) + 1 );		
        } else {			
            $block_exists = false;			
            $block_content = '';		
        }
		$count = 1;
        while ($popular_products = tep_db_fetch_array($popular_query)) {
            $popular_products['products_image'] = (tep_not_null($popular_products['products_mediumimage']) ?$popular_products['products_mediumimage'] : $popular_products['products_image']);
            $popular_products['specials_new_products_price'] = tep_get_products_special_price($popular_products['products_id']);			
            if ($popular_products['specials_new_products_price']) {				
                $price =  '<s>' . $currencies->display_price($popular_products['products_price'], tep_get_tax_rate($popular_products['products_tax_class_id'])) . '</s><br>';				
                $price .= '<span class="productSpecialPrice">' . $currencies->display_price($popular_products['specials_new_products_price'], tep_get_tax_rate($popular_products['products_tax_class_id'])) . '</span>';			
            } else {				
                $price =  $currencies->display_price($popular_products['products_price'], tep_get_tax_rate($popular_products['products_tax_class_id']));			
            }   			
            if (tep_not_null($popular_products['products_image'])) {				
                $feed_status = is_xml_feed_product($popular_products['products_id']);				
                if ($feed_status) 					
                    $image = tep_small_image($popular_products['products_image'], $popular_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');				
                else 					
                    $image = tep_image(DIR_WS_IMAGES . $popular_products['products_image'], $popular_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');			
            }						
            $link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $popular_products['products_id']);			
            $name = $popular_products['products_name'];
			$entry = str_ireplace( array('$image', '$link', '$name', '$price'), array($image, $link, $name, $price), $block_content);			
			$output .= $entry;
        }				
        $footer_bof = stripos($content, '<!--footer_bof-->');		
        $footer_eof = stripos($content, '<!--footer_eof-->');		
        if ($footer_bof!==false && $footer_eof!==false){			
            $footer_exists = true;			
            $footer_content = substr( $content,  $footer_bof, $footer_eof - $footer_bof );			
            $footer_content = substr( $footer_content,  stripos( $footer_content, '>' ) + 1 );			
            $footer_content = str_ireplace('$footer', '', $footer_content);			
            $footer = '';		
        } else {			
            $footer_exists = false;			
            $footer = '';		
        }		
        $output .= $footer_content;		
        echo $output;	
    } else {?>
<!-- xsell_products //-->
<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
<tr><td class="xcell">
<?
     $info_box_contents = array();
     $info_box_contents[] = array('align' => 'left', 'text' => 'Popular Products');
     new contentBoxHeading($info_box_contents);

     $row = 0;
     $col = 0;
     $info_box_contents = array();
     while ($popular_products = tep_db_fetch_array($popular_query)) {
        
       $popular_products['specials_new_products_price'] = tep_get_products_special_price($popular_products['products_id']);

if ($popular_products['specials_new_products_price']) {
     $popular_products_price =  '<s>' . $currencies->display_price($popular_products['products_price'], tep_get_tax_rate($popular_products['products_tax_class_id'])) . '</s><br>';
     $popular_products_price .= '<span class="productSpecialPrice">' . $currencies->display_price($popular_products['specials_new_products_price'], tep_get_tax_rate($popular_products['products_tax_class_id'])) . '</span>';
   } else {
     $popular_products_price =  $currencies->display_price($popular_products['products_price'], tep_get_tax_rate($popular_products['products_tax_class_id']));
   }
   
   if (tep_not_null($popular_products['products_image'])) {
    	$feed_status = is_xml_feed_product($popular_products['products_id']);
  if ($feed_status) 
   $image = tep_small_image($popular_products['products_image'], $popular_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $popular_products['products_image'], $popular_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
   }
       $info_box_contents5[$row][$col] = array('align' => 'center',
                                              'params' => 'class="main" valign="top"',
                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $popular_products['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $popular_products['products_id']) . '"class="xcell"><b>' . $popular_products['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $popular_products_price. '</td></tr></table></td></tr></table>');
       $col ++;
       if (0 < $col) {
         $col = 0;
         $row ++;
       }
       
     }
     new contentBox($info_box_contents5);
?>
</td></tr></table><?php	}    ?>
<!-- xsell_products_eof //-->
<?

   }   
}
?>
