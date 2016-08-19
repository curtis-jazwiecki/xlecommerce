<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
  
  Featured Products V1.1
  Displays a list of featured products, selected from admin
  For use as an Infobox instead of the "New Products" Infobox  
*/
?>
<!-- featured_products //-->
<?php
$output = '';
$temp='';
if(FEATURED_PRODUCTS_DISPLAY == 'true'){
	$featured_products_category_id = $new_products_category_id;
	$cat_name_query = tep_db_query("select categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . $featured_products_category_id . "' limit 1");
	$cat_name_fetch = tep_db_fetch_array($cat_name_query);
	$cat_name = $cat_name_fetch['categories_name'];
	$info_box_contents = array();
	
	// get max display featured product #start
	$max_count = MAX_DISPLAY_FEATURED_PRODUCTS;
	
  
	//BOF seperate pricing
	if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
		$customer_group_id = $_SESSION['sppc_customer_group_id'];
	} else {
		$customer_group_id = '0';
	}
	//EOF seperate pricing
  
	/*if (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES. 'featured_header.jpg')) {
		$heading = '<a href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">'  . tep_image(DIR_WS_IMAGES . 'featured_header.jpg', TABLE_HEADING_FEATURED_PRODUCTS). '</a>';
	} else { */
		$heading = '<a class="headerNavigation" href="' . tep_href_link(FILENAME_FEATURED_PRODUCTS) . '">' .TABLE_HEADING_FEATURED_PRODUCTS . '</a>';
	//}
	if ( (!isset($featured_products_category_id)) || ($featured_products_category_id == '0') ) {
		$info_box_contents[] = array('align' => 'left', 'text' => $heading . '</a>');
		list($usec, $sec) = explode(' ', microtime());
		srand( (float) $sec + ((float) $usec * 100000) );
		$mtm= rand();
		//$featured_products_query = tep_db_query("select p.products_id, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_FEATURED . " f on p.products_id = f.products_id where p.products_status = '1' and f.status = '1' order by rand($mtm) DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);
		$featured_products_query = tep_db_query("select p.products_id, p.products_image, products_mediumimage, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price, p.hide_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id left join " . TABLE_FEATURED . " f on p.products_id = f.products_id where p.products_status = '1' and f.status = '1' and f.featured_group='1' order by rand($mtm) DESC limit " . (!empty($max_count) ? $max_count : MAX_DISPLAY_FEATURED_PRODUCTS) );
      		
		
		
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
    
         //$featured_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) left join " . TABLE_FEATURED . " f on p.products_id = f.products_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where c.parent_id = '" . $featured_products_category_id . "' and p.products_status = '1' and f.status = '1' order by rand() DESC limit " . MAX_DISPLAY_FEATURED_PRODUCTS);     
         $featured_products_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_mediumimage, p.products_tax_class_id, s.status as specstat, s.specials_new_products_price, p.products_price, p.hide_price from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c using(products_id) left join " . TABLE_CATEGORIES . " c using(categories_id) left join " . TABLE_FEATURED . " f on p.products_id = f.products_id left join " . TABLE_SPECIALS . " s on p.products_id = s.products_id where c.parent_id = '" . $featured_products_category_id . "' and p.products_status = '1' and f.status = '1' order by rand() DESC limit " . (!empty($max_count) ? $max_count : MAX_DISPLAY_FEATURED_PRODUCTS) );     
	}
    
    $total_featured = tep_db_num_rows($featured_products_query);
    if ($total_featured >= $items_per_row) {
    
	if (tep_db_num_rows($featured_products_query)){
		if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && ( ( ( !isset($on_home_page) || !$on_home_page ) && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_06.php.html') ) || ( $on_home_page && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/featured_products1.php.html')  ) ) ) {	
			if (empty($custom)){
				$content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_06.php.html');
			} else {
				$content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/featured_products1.php.html');
			}
        		
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
			while ($featured_products = tep_db_fetch_array($featured_products_query)) {
				//BOF seperate pricing
				$pg_query = tep_db_query("select pg.products_id, customers_group_price as price from " . TABLE_PRODUCTS_GROUPS . " pg where pg.products_id = '".$featured_products['products_id']."' and pg.customers_group_id = '" . $customer_group_id . "' and pg.customers_group_id <> 0");
				$pg_array = tep_db_fetch_array($pg_query);
                
				$new_price = tep_get_products_special_price($featured_products['products_id']);
    
                if(isset($new_price)&& !empty($new_price)){
                    $specialprice = $new_price; // holds special price
                }else{
                    $specialprice = ''; 
                }
                  
                  if ($customer_group_id > 0 && isset($pg_array['price']) && !empty($pg_array['price'])) {
                    if($specialprice > 0){
                        $featured_products['products_price'] = $new_price;
                    }else{
                        $featured_products['products_price'] = $pg_array['price'];
                    }
                  }else{
                    if($specialprice > 0){
                        $featured_products['products_price'] = $new_price;
                    }else{
                        //$featured_products['products_price'] = $featured_products['products_price'];
                    }
                  }
				//EOF seperate pricing
				$featured_products['products_name'] = tep_get_products_name($featured_products['products_id']);
                 $featured_products['products_image'] = (tep_not_null($featured_products['products_mediumimage']) ? $featured_products['products_mediumimage'] : $featured_products['products_image']);
                                    if (tep_not_null($featured_products['products_image'])) {
                                            $feed_status = is_xml_feed_product($featured_products['products_id']);
                                            if ($feed_status) {
                                                    $image = tep_small_image($featured_products['products_image'], $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
                                            } else  {
                                                    $image = tep_image(DIR_WS_IMAGES . $featured_products['products_image'], $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
                                            }
                                    }
				
				$link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']);
				$name = $featured_products['products_name'];
				$price = ($featured_products['hide_price'] == '1' ? HIDE_PRICE_TEXT : $currencies->display_price($featured_products['products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])));
                $manufacturer ="";
                $link_manufacturer="";
				$manufacturer_query = tep_db_query("select m.manufacturers_name,m.manufacturers_id from ". TABLE_PRODUCTS ." p ,".TABLE_MANUFACTURERS." m where p.manufacturers_id= m.manufacturers_id and p.products_id =". $featured_products['products_id']);
                while($manufacturer_row = tep_db_fetch_array($manufacturer_query)){
                    $manufacturer = $manufacturer_row['manufacturers_name'];
                    $link_manufacturer = tep_href_link('shop.php','manufacturers_id='.$manufacturer_row['manufacturers_id']); 
                }
                $msrp_price="";
                $msrp_query = tep_db_query("select unit_msrp from products_extended where osc_products_id =". $featured_products['products_id']);                 while($msrp_row = tep_db_fetch_array($msrp_query)){
                    if($msrp_row['unit_msrp']>0){
                        $msrp_price = "<b> MSRP: </b> $". $msrp_row['unit_msrp'];
                    }
                }    
				
				if (!empty($items_per_row) && $items_per_row>1){
					if (empty($temp)) $temp = $block_content;
					$temp = str_ireplace( array('$image_' . $count, '$link_' . $count, '$name_' . $count, '$price_' . $count,'$manufacturer_'.$count,'$link_manufacturer_'.$count,'$msrp_price_'.$count), array($image, $link, $name, $price,$manufacturer,$link_manufacturer,$msrp_price), $temp);
					if ($count==$items_per_row){
						$output .= $temp;
						$temp = '';
						$count = 1;
					} else {
						$count++;
					}
				} else {
					$entry = str_ireplace( array('$image', '$link', '$name', '$price'), array($image, $link, $name, $price), $block_content);			
					$output .= $entry;
				}
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
		} else {
			$row = 0;
			$col = 0; 
			$num = 0;
			while ($featured_products = tep_db_fetch_array($featured_products_query)) {
				//BOF seperate pricing
				$pg_query = tep_db_query("select pg.products_id, customers_group_price as price from " . TABLE_PRODUCTS_GROUPS . " pg where pg.products_id = '".$featured_products['products_id']."' and pg.customers_group_id = '" . $customer_group_id . "' and pg.customers_group_id <> 0");
				$pg_array = tep_db_fetch_array($pg_query);
                
				$new_price = tep_get_products_special_price($featured_products['products_id']);
    
                if(isset($new_price)&& !empty($new_price)){
                    $specialprice = $new_price; // holds special price
                }else{
                    $specialprice = ''; 
                }
                  
                  if ($customer_group_id > 0 && isset($pg_array['price']) && !empty($pg_array['price'])) {
                    if($specialprice > 0){
                        $featured_products['products_price'] = $new_price;
                    }else{
                        $featured_products['products_price'] = $pg_array['price'];
                    }
                  }else{
                    if($specialprice > 0){
                        $featured_products['products_price'] = $new_price;
                    }else{
                        //$featured_products['products_price'] = $featured_products['products_price'];
                    }
                  }
				//EOF seperate pricing
				$num ++;
				if ($num == 1) { 
					//new contentBoxHeading($info_box_contents); 
					echo $heading;
				}
				$featured_products['products_name'] = tep_get_products_name($featured_products['products_id']);
                if (tep_not_null($featured_products['products_mediumimage'])) {
                    $image = $featured_products['products_mediumimage'];
                    } else {
                        $image = $featured_products['products_image'];
                    }
				if (tep_not_null($featured_products['products_image'])) {
					$feed_status = is_xml_feed_product($featured_products['products_id']);
					if ($feed_status) {
						$image = tep_small_image($image, $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
					} else {
						$image = tep_image(DIR_WS_IMAGES . $image, $featured_products['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
					}
				}
                
	   if($featured_products['hide_price'] == '1') {
   	    					$info_box_contents[$row][$col] = array(
						'align' => 'center',
						'params' => 'class="smallText" style="padding: 10px; border-spacing: 5px; border-bottom: solid 1px silver; border-top: solid 1px silver; border-right: solid 1px silver; border-left: solid 1px silver;" width="31%" valign="middle"',
						'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $featured_products['products_name'] . '</a><br>' . HIDE_PRICE_TEXT . '</span>'
					);
        
        } elseif($featured_products['specstat'] && $featured_products['products_price'] > $featured_products['specials_new_products_price']) {
					$info_box_contents[$row][$col] = array(
						'align' => 'center',
						'params' => 'class="smallText" width="31%" valign="top" style="background-color:#999999;"',
						'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $featured_products['products_name'] . '</a><br><s>' . $currencies->display_price($featured_products['products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price($featured_products['specials_new_products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])) . '</span>'
					);
				} else {
					$info_box_contents[$row][$col] = array(
						'align' => 'center',
						'params' => 'class="smallText" style="padding: 10px; border-spacing: 5px; border-bottom: solid 1px silver; border-top: solid 1px silver; border-right: solid 1px silver; border-left: solid 1px silver;" width="31%" valign="middle"',
						'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $featured_products['products_id']) . '">' . $featured_products['products_name'] . '</a><br><span class="FeaturedPriceColor"> ' . $currencies->display_price($featured_products['products_price'], tep_get_tax_rate($featured_products['products_tax_class_id'])) . '</span>'
					);
				}
				$col ++;
				$info_box_contents[$row][$col] = array(
					'align' => 'center',
					'params' => 'width="1px" valign="top"',
					'text' => '&nbsp;');
				$col++;
				if ($col > 4) {
					$col = 0;
					$row ++;
					$info_box_contents[$row][$col] = array(
						'align' => 'center', 
						'params' => 'style="margin: -3px; font-size: 8px"', 
						'text' => '&nbsp;'
					);
					$col ++;
					$info_box_contents[$row][$col] = array(
						'align' => 'center', 
						'params' => 'style=""', 
						'text' => ''
					);
					$col ++;
					$info_box_contents[$row][$col] = array(
						'align' => 'center', 
						'params' => 'style=""', 
						'text' => ''
					);
					$col ++;
					$info_box_contents[$row][$col] = array(
						'align' => 'center', 
						'params' => 'style=""', 
						'text' => ''
					);
					$col ++;
					$info_box_contents[$row][$col] = array(
						'align' => 'center', 
						'params' => 'style="" width="1px"', 
						'text' => ''
					);
					$col = 0;
					$row ++;
				}
			}
			if($num) {
				new contentBox($info_box_contents);
			}
		}
	} else { // If it's disabled, then include the original New Products box
		// include (DIR_WS_MODULES . FILENAME_NEW_PRODUCTS); // disable for dont show if desactive the feature module
	}
   } 
}
?>
<!-- featured_products_eof //-->