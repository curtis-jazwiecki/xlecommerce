<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
if (isset($pw_mispell)){ //added for search enhancements mod
?>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td><?php echo $pw_string; ?></td></tr>
</table>
<?php
 } //end added search enhancements mod
  $listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id');

  if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) ) {
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }

  $list_box_contents = array();

  if ($listing_split->number_of_rows > 0) {
    $listing_query = tep_db_query($listing_split->sql_query);

    $row = 0;
    $column = 0;
    $no_of_listings = tep_db_num_rows($listing_query);

    while ($_listing = tep_db_fetch_array($listing_query)) {
      $listing[] = $_listing;
    }


    for ($x = 0; $x < $no_of_listings; $x++) {
      $rows++;
      $product_contents = array();
      for ($col=0, $n=sizeof($column_list); $col<$n; $col++) {
        $lc_align = '';
        switch ($column_list[$col]) {
          case 'PRODUCT_LIST_MODEL':
            $lc_align = '';
            $lc_text = '&nbsp;' . $listing[$x]['products_model'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_NAME':
            $lc_align = '';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
			 $lc_text = '';
             $lc_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" class="cart">' . $listing[$x]['products_name'] . '</a>';
            } else {
			$lc_text='';
              $lc_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '" class="cart">' . $listing[$x]['products_name'] . '</a>';
            }
            break;
          case 'PRODUCT_LIST_MANUFACTURER':
            $lc_align = '';
            $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing[$x]['manufacturers_id']) . '">' . $listing[$x]['manufacturers_name'] . '</a>&nbsp;';
            break;
          case 'PRODUCT_LIST_PRICE':
          if ($listing[$x]['products_price'] <= 0) {
			$lc_price = '';
		  } else {
		     $lc_align = 'right';
            if (tep_not_null($listing[$x]['specials_new_products_price'])) {
			$lc_text = '';
              $lc_price = '&nbsp;<s>' .  $currencies->display_price($listing[$x]['products_price'], tep_get_tax_rate($listing[$x]['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($listing[$x]['specials_new_products_price'], tep_get_tax_rate($listing[$x]['products_tax_class_id'])) . '</span>&nbsp;';
            } else {
			$lc_text = '';
              $lc_price = '&nbsp;' . $currencies->display_price($listing[$x]['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
            }
           } 
            break;
          case 'PRODUCT_LIST_QUANTITY':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing[$x]['products_quantity'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_WEIGHT':
            $lc_align = 'right';
            $lc_text = '&nbsp;' . $listing[$x]['products_weight'] . '&nbsp;';
            break;
          case 'PRODUCT_LIST_IMAGE':
         $feed_status = is_xml_feed_product($listing[$x]['products_id']);
          if ($feed_status) 
		  $image = tep_small_image($listing[$x]['products_image'], $listing[$x]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
            else 
            $image = tep_image(DIR_WS_IMAGES . $listing[$x]['products_image'], $listing[$x]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
            $lc_align = 'center';
            if (isset($HTTP_GET_VARS['manufacturers_id'])) {
			$lc_text='';
              $lc_image = '<table><tr><td style="border:1px solid #333333;" valign="bottom"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" >' .  $image . '</a></td></tr></table>';
            } else {
			$lc_text='';
              $lc_image = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '"  >' . $image . '</a>';
            }
            break;
          
         case 'PRODUCT_LIST_BUY_NOW':
            $lc_align = 'center';
			
            $lc_text = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
            break;
          
        }
        $product_contents[] = $lc_text;
      }
/* - OLD DISPLAY LAYOUT - OBN
      $lc_text = implode('<br>', $product_contents);
      $list_box_contents[$row][$column] = array('align' => 'left',
                                                'params' => 'class="productListing-data" '  ,
                                                'text'  => '<table cellpadding="0" border="0" cellspacing="0">
												<tr><td width="100" align="left" valign="bottom">' .$lc_image .'</td></tr>' .
												'<tr><td width="120" height="5" align="center" class="smallText" >'.tep_draw_separator('pixel_trans.gif', '100%', '5') .'</td></tr>'.
												'<tr><td width="120" height="50" class="smallText" valign="top">'. $lc_name . '<br>' . $lc_price . '</td></tr></table>');
	  
	  
*/
		//grab medium picture for descriptive layout
		//begin medium picture
		$text_display = '';

        	$feed_status = is_xml_feed_product($listing[$x]['products_id']);
          	if ($feed_status) 
		  		$image = tep_small_image($listing[$x]['products_image'], $listing[$x]['products_name'], 150, 150,'class="subcatimages" border="0"');
          	else 
          		$image = tep_image(DIR_WS_IMAGES . $listing[$x]['products_image'], $listing[$x]['products_name'], 150, 150,'class="subcatimages" border="0"');
            $lc_align = 'center';
            if (isset($HTTP_GET_VARS['manufacturers_id']))
			  {
				$lc_text='';
            	$lc_image_med = '<table width="100%" style="align: center"><tr><td style="border:1px solid #333333;" valign="bottom"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" >' .  str_replace("/small/","/medium/",$image) . '</a></td></tr></table>';
              }
			else
			  {
				$lc_text='';
				$lc_image_med = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '"  >' . str_replace("/small/","/medium/",$image) . '</a>';
			//end medium picture
			  }

		// Begin - Descriptive Template
		  $lc_text = implode('<br>', $product_contents);
		  $product_info_query = tep_db_query("select p.products_quantity from " . TABLE_PRODUCTS . " p  where p.products_id = '" . $listing[$x]['products_id'] . "'");
		  $product_info_2 = tep_db_fetch_array($product_info_query);
	
			// Show in stock/out of stock status - OBN
		if (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'false')
		  {
			$stock_info = '<b>';
			$stock_info .= ($product_info_2['products_quantity'] > 0) ? 'In Stock' : STORE_STOCK_OUT_OF_STOCK_MESSAGE;
			$stock_info .= '</b>';
		  }
			// Low Stock message if below given value - OBN
		elseif (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'true')
		  {
			$stock_info .= '<b>';
				if($product_info_2['products_quantity'] <= STORE_STOCK_LOW_INVENTORY_QUANTITY && $product_info_2['products_quantity'] > 0)
					$stock_info .= STORE_STOCK_LOW_INVENTORY_MESSAGE;
				elseif($product_info_2['products_quantity'] > STORE_STOCK_LOW_INVENTORY_QUANTITY)
					$stock_info .= 'In Stock';
				else
					$stock_info .= STORE_STOCK_OUT_OF_STOCK_MESSAGE;
			$stock_info .= '</b>';
		  }
	
		  $list_box_contents[$row][$column] = array('align' => 'left',
													'params' => 'class="productListing-data" ');
		  $text_display_test = '<table cellpadding="0" border="0" cellspacing="0" width="100%" style="margin: 0 0 8px 0;">';
		  $text_display_test .= '<tr valign="top" height="150px">';
		  $text_display_test .= '<td width="15"></td>';
		  $text_display_test .= '<td width="100" align="center" valign="top">DISPLAY_PRODUCT_IMAGE</td>';
		  $text_display_test .= '<td width="15"></td>';
		  $text_display_test .= '</tr>';
		  $text_display_test .= '<tr valign="top">';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '<td class="smallText">DISPLAY_PRODUCT_NAME<br /><br /></td>';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '</tr>';
		  $text_display_test .= '<tr valign="top">';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '<td class="smallText" align="right">DISPLAY_PRODUCT_STOCK<br /><br /></td>';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '</tr>';
		  $text_display_test .= '<tr valign="top">';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '<td class="smallText" align="right">DISPLAY_PRODUCT_PRICE<br /><br /></td>';
		  $text_display_test .= '<td></td>';
		  $text_display_test .= '</tr>';
		  $text_display_test .= '</table>';

		  $text_display = 
			'<table cellpadding="0" border="0" cellspacing="0" width="100%" style="margin: 0 0 8px 0;">' . 
				'<tr valign="top" height="150px">' . 
					'<td>' . 
						'<p>' . 
							DISPLAY_PRODUCT_IMAGE . '<br>' . 
						'</p>' . 
						'<p id="mt">' . 
							'<b class="feature_title">' . DISPLAY_PRODUCT_NAME . '</b><br>' . 
							'<b' . DISPLAY_PRODUCT_PRICE . '</b><br>' . 
						'</p>' . 
					'</td>' . 
				'</tr>' . 
			'</table>';

		  for($p=0;sizeof($product_listing_template_2) > $p; $p++)
			{ $text_display .= $product_listing_template_2[$p]; }
		  
  		  $text_display = str_replace("DISPLAY_PRODUCT_IMAGE",str_replace("noimage.gif","noimage_large.gif",$lc_image_med), $text_display);
		  $text_display = str_replace("DISPLAY_PRODUCT_PRICE",$lc_price, $text_display);
		  $text_display = str_replace("DISPLAY_PRODUCT_NAME",$lc_name, $text_display);
		  $text_display = str_replace("DISPLAY_PRODUCT_STOCK",$stock_info, $text_display);
		  $text_display = str_replace("DISPLAY_PRODUCT_SEPARATOR",tep_draw_separator('pixel_trans.gif','100%','5'), $text_display);

		  $list_box_contents[$row][$column] = array('text'  => $text_display);

		  unset($stock_info);
		  $column ++;
		  if ($column >= 3) {
			$row ++;
			$column = 0;
		  }
		// End -  Begin - Descriptive Template
	}

    new productListingBox($list_box_contents);
  } else {
    $list_box_contents = array();
    $list_box_contents[0] = array('params' => 'class="productListing-odd"');
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                   'text' => TEXT_NO_PRODUCTS);
    new productListingBox($list_box_contents);
  }
  if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<table border="0" width="100%" cellspacing="0" cellpadding="2">
  <tr>
    <td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
  </tr>
</table>
<?php
  }
?>
