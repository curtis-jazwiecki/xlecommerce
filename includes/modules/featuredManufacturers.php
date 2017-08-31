<?
/*
$Id: xsell_products.php, v1  2002/09/11
// adapted for Separate Pricing Per Customer v4 2005/02/24

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

if ($HTTP_GET_VARS['products_id'] && ENABLE_FEATURE_MANUFACTURERS=='True') {
$m_array = explode(",",featured_manufacturer);

if(sizeof($m_array) >0){
	
$featuredManufacturers = tep_db_query("select p.products_id,p.products_image,p.products_price,p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.manufacturers_id IN (".featured_manufacturer.") order by pd.products_name LIMIT 0 ,".FEATURED_MANUFACTURER); //ORDER BY p.reviews_rating DESC

// EOF Separate Pricing Per Customer
$num_featuredManufacturers = tep_db_num_rows($featuredManufacturers);

// EOF Separate Pricing Per Customer
//$num_products_xsell = tep_db_num_rows($featuredManufacturers);

if ($num_featuredManufacturers > 0) {	
		if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_03.php.html') ){		
			
			$content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_03.php.html');		
			$output = '';		
			$header_bof = stripos($content, '<!--header_bof-->');		
			$header_eof = stripos($content, '<!--header_eof-->');		
			if ($header_bof!==false && $header_eof!==false){			
				$header_exists = true;			
				$header_content = substr( $content,  $header_bof, $header_eof - $header_bof );			
				$header_content = substr( $header_content,  stripos( $header_content, '>' ) + 1 );			
				$header_content = str_ireplace('$header', 'Featured Manufacturers', $header_content);		
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
			
			while ($featuredManufacturer = tep_db_fetch_array($featuredManufacturers)) {			
				
				$featuredManufacturer['specials_new_products_price'] = tep_get_products_special_price($featuredManufacturer['products_id']);			
				
				if ($featuredManufacturer['specials_new_products_price']) {				
					$price =  '<s>' . $currencies->display_price($featuredManufacturer['products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id'])) . '</s><br>';				$price .= '<span class="productSpecialPrice">' . $currencies->display_price($featuredManufacturer['specials_new_products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id'])) . '</span>';			
				} else {				
					$price =  $currencies->display_price($featuredManufacturer['products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id']));			
				}   			
				
				if (tep_not_null($featuredManufacturer['products_image'])) {				
					$feed_status = is_xml_feed_product($featuredManufacturer['products_id']);				
					
					if ($feed_status) 					
						$image = tep_small_image($featuredManufacturer['products_image'], $featuredManufacturer['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
					else
						$image = tep_image(DIR_WS_IMAGES . $featuredManufacturer['products_image'], $featuredManufacturer['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');			}						
					
					$link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featuredManufacturer['products_id']);			
					$name = $featuredManufacturer['products_name'];			
					$entry = str_ireplace(array('$image', '$link', '$name', '$price'),array($image, $link, $name, $price),$block_content);			
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
<!-- featured_manufacturers //-->
				<table border="0" cellpadding="0" cellspacing="0" width="100%" style="padding-top: 10px;">
  <tr>
    <td class="xcell"><?
     $info_box_contents = array();
     $info_box_contents[] = array('align' => 'left', 'text' => 'Featured Manufacturers');
     new contentBoxHeading($info_box_contents);

     $row = 0;
     $col = 0;
     $info_box_contents = array();
     while ($featuredManufacturer = tep_db_fetch_array($featuredManufacturers)) {
         
       $featuredManufacturer['specials_new_products_price'] = tep_get_products_special_price($featuredManufacturer['products_id']);

if ($featuredManufacturer['specials_new_products_price']) {
     $featuredManufacturer_price =  '<s>' . $currencies->display_price($featuredManufacturer['products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id'])) . '</s><br>';
     $featuredManufacturer_price .= '<span class="productSpecialPrice">' . $currencies->display_price($featuredManufacturer['specials_new_products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id'])) . '</span>';
   } else {
     $featuredManufacturer_price =  $currencies->display_price($featuredManufacturer['products_price'], tep_get_tax_rate($featuredManufacturer['products_tax_class_id']));
   }
   
   if (tep_not_null($featuredManufacturer['products_image'])) {
    	$feed_status = is_xml_feed_product($featuredManufacturer['products_id']);
  if ($feed_status) 
   $image = tep_small_image($featuredManufacturer['products_image'], $featuredManufacturer['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $featuredManufacturer['products_image'], $featuredManufacturer['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
   }
       $info_box_contents9[$row][$col] = array('align' => 'center',
                                              'params' => 'class="main" valign="top"',
                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featuredManufacturer['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featuredManufacturer['products_id']) . '"class="xcell"><b>' . $featuredManufacturer['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $featuredManufacturer_price. '</td></tr></table></td></tr></table>');
       $col ++;
       if (0 < $col) {
         $col = 0;
         $row ++;
       }
     }
     new contentBox($info_box_contents9);
?></td>
  </tr>
</table>
<?php	}    ?>
<!-- featured_manufacturers_eof //-->
<?php
   }
 }
}
?>