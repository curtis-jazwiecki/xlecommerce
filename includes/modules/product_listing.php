<?php
/*
  $Id: product_listing.php,v 1.44 2003/06/09 22:49:59 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
if (isset($pw_mispell)) //added for search enhancements mod
  {
?>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
	  <tr><td><?php echo $pw_string; ?></td></tr>
	</table>
<?php 
  }
// Get selected template
$template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
$rows = tep_db_fetch_array($template_query);
$selected_template = $rows['configuration_value'];
// Get selected template

//end added search enhancements mod

// if template number 3 is selected, show 30 products per page - OBN
if(PRODUCT_LISTING_TEMPLATE == 2)
  { 
    // #06 9Jan2014 (MA) BOF
    $listing_split = new splitPageResults($listing_sql, (!empty($_SESSION['items_per_page']) ? ($_SESSION['items_per_page']=='All' ? '10000' : $_SESSION['items_per_page']) : MAX_DISPLAY_SEARCH_RESULTS), 'p.products_id'); 
    //$listing_split = new splitPageResults($listing_sql, 30, 'p.products_id'); 
    
  }
    // #06 9Jan2014 (MA) EOF
else
  { 
    // #06 9Jan2014 (MA) BOF
    $listing_split = new splitPageResults($listing_sql, (!empty($_SESSION['items_per_page']) ? ($_SESSION['items_per_page']=='All' ? '10000' : $_SESSION['items_per_page']) : MAX_DISPLAY_SEARCH_RESULTS), 'p.products_id'); 
    
    //$listing_split = new splitPageResults($listing_sql, MAX_DISPLAY_SEARCH_RESULTS, 'p.products_id'); 
  // #06 9Jan2014 (MA) EOF
  }
//echo $listing_sql;
if ( ($listing_split->number_of_rows > 0) && ( (PREV_NEXT_BAR_LOCATION == '1') || (PREV_NEXT_BAR_LOCATION == '3') ) )
  {
	?>
	<table border="0" width="100%" cellspacing="0" cellpadding="2">
	  <tr>
		<td class="smallText"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
		<td class="smallText" align="right"><?php echo TEXT_RESULT_PAGE . ' ' . $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
	  </tr>
	</table>
	<?php
  }

// Begin Compare Button - OBN
	/*if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
	  {
  		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="2" align="right"><a href="compare.php?'.tep_get_all_get_params(array('action')).'">'.tep_image_button('button_compare.gif', "Compare Products").'</a></td></tr></table>';
//  		echo '<table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="2" align="right"><a href="compare.php?'.tep_get_all_get_params(array('action')).'"><input border="0" type="Submit" alt="Compare" title="Compare" name=" Compare " value=" Compare "></a></td></tr></table>';
	  }*/
// End Compare Button - OBN


$list_box_contents = array();
  /* BoF Compare Products side-by-side
         Insert first column to add checkbox to compare products */
$list_box_contents[0][] = array('align' => "center",
                                'params' => 'class="productListing-heading"',
                                'text' => TABLE_HEADING_COMPARE . '<br>' . tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</form>');
  /* EoF Compare Products side-by-side */

if ($listing_split->number_of_rows > 0)
  {
    $rows = 0;
    /* BoF Compare Products side-by-side
           Generate hidden fields to submit with each checkbox */
    $hidden_get_variables = '';
    reset($HTTP_GET_VARS);
    while (list($key, $value) = each($HTTP_GET_VARS))
	  {
        if ((substr($key,0,8) != 'columns_') && ($key != tep_session_name()) && ($key != 'x') && ($key != 'y'))
		  {
            $hidden_get_variables .= tep_draw_hidden_field($key, $value);
          }
      }
     /* EoF Compare Products side-by-side */

    $listing_query = tep_db_query($listing_split->sql_query);
    $row = 0;
    $column = 0;
    $no_of_listings = tep_db_num_rows($listing_query);

    while ($_listing = tep_db_fetch_array($listing_query))
	  {
        $rows++;
		/* BoF Compare Products side-by-side
			 Hide all columns selected except for this product, which will be generated by the checkbox form is needed */
		$hidden_get_columns = '';
		reset($HTTP_GET_VARS);
		while (list($key, $value) = each($HTTP_GET_VARS))
		  {
			if ((substr($key,0,8) == 'columns_') && ($key != 'columns_'.$listing['products_id']) )
			  { $hidden_get_columns .= tep_draw_hidden_field($key, $value); } 
		  }
		/* EoF Compare Products side-by-side */
		$listing[] = $_listing;
	  }

      
          if(isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
  $customer_group_id = $_SESSION['sppc_customer_group_id'];
 
  } else {
     
   $customer_group_id = '0';
  }
           
	for ($x = 0; $x < $no_of_listings; $x++)
	  {
            
    $rows++;
	//BOF:mod 10-21-2013
    
	$is_child =false;
	if (!empty($listing[$x]['parent_products_model'])){
		$parent_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_mediumimage, pd.products_name from products p inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='" . (int)$languages_id . "') where p.products_model='" . tep_db_input($listing[$x]['parent_products_model']) . "' and p.products_status='1'");
		if (tep_db_num_rows($parent_query)){
			$parent = tep_db_fetch_array($parent_query);
			$listing[$x]['products_model'] = $parent['products_model'];
			$listing[$x]['products_id'] = $parent['products_id'];
			$listing[$x]['products_name'] = $parent['products_name'];
			$listing[$x]['products_image'] = $parent['products_image'];
			$listing[$x]['products_mediumimage'] = $parent['products_mediumimage'];
			$is_child = true;
		}
	}
               ///////////////////////////////////////////////////       
          // BOF Separate Pricing per Customer
      if ($customer_group_id > 0) { // only need to check products_groups if customer is not retail
        $scustomer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . $listing[$x]['products_id']. "' and customers_group_id =  '" . $customer_group_id . "'");
        $scustomer_group_price = tep_db_fetch_array($scustomer_group_price_query);
      } // end if ($customer_group_id > 0)

       $new_price = tep_get_products_special_price($listing[$x]['products_id']); 
       $p_query = tep_db_query("select products_price, products_tax_class_id from products where products_id = ".$listing[$x]['products_id']);
       $p_query_res = tep_db_fetch_array($p_query);
          
         
          if($customer_group_id > 0 && isset($scustomer_group_price['customers_group_price']) && !empty($scustomer_group_price['customers_group_price']))
          {
             if(isset($new_price) && !empty($new_price))
             {
                 
                if($scustomer_group_price['customers_group_price'] < $new_price) 
                { 
                   $lc_price = '<s>' . $currencies->display_price($p_query_res['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($p_query_res['products_tax_class_id'])) . '</span>';
                   // $products_price =  $currencies->display_price($scustomer_group_price['customers_group_price'], '0') ; 
                }
                 else { 
                    $lc_price = '<s>' . $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($p_query_res['products_tax_class_id'])) . '</span>';
                }
             }
             else
             { 
                 if($p_query_res['products_price'] < $scustomer_group_price['customers_group_price'])
                 {
                   $lc_price =  $currencies->display_price($p_query_res['products_price'], '0') ;   
                 }
                 else
                 {
                $lc_price =  $currencies->display_price($scustomer_group_price['customers_group_price'], '0') ; 
                 }
             }
          }
          else
          { 
              if(isset($new_price) && !empty($new_price))
             {
                  $lc_price = '<s>' . $currencies->display_price($p_query_res['products_price'], tep_get_tax_rate($p_query_res['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
              } else {
              $lc_price =  $currencies->display_price($p_query_res['products_price'], '0') ;
              }
          }
     
      
        //BOF seperate pricing
        if($customer_group_id != 0){
                  $pg_query = tep_db_query("select pg.products_id, customers_group_price as price from " . TABLE_PRODUCTS_GROUPS . " pg where pg.products_id = '".$listing[$x]['products_id']."' and pg.customers_group_id = '" . $customer_group_id . "'");
                  if(tep_db_num_rows($pg_query)){
                      $flag = 1;
                      $pg_array = tep_db_fetch_array($pg_query);
                      $listing[$x]['products_price'] = $pg_array['price'];
                  }
            }
            
            //EOF seperate pricing
	
	//EOF:mod 10-21-2013	
        
	$product_contents = array();
	for ($col=0, $n=sizeof($column_list); $col<$n; $col++)
	  {
		$lc_align = '';
		switch ($column_list[$col])
		  {
			case 'PRODUCT_LIST_MODEL':
			 $lc_align = '';
			 $lc_text = '&nbsp;' . $listing[$x]['products_model'] . '&nbsp;';
			 break;
			case 'PRODUCT_LIST_NAME':
			 $lc_align = '';
			 if (isset($HTTP_GET_VARS['manufacturers_id']))
			   {
			 	$lc_text = '';
			 	$lc_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" class="cart">' . $listing[$x]['products_name'] . '</a>';
			   }
			 else
			   {
				$lc_text='';
			  	$lc_name = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '" class="cart">' . $listing[$x]['products_name'] . '</a>';
			}
			 break;
			case 'PRODUCT_LIST_MANUFACTURER':
			 $lc_align = '';
			 $lc_text = '&nbsp;<a href="' . tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $listing[$x]['manufacturers_id']) . '">' . $listing[$x]['manufacturers_name'] . '</a>&nbsp;';
	         break;
			case 'PRODUCT_LIST_PRICE':
			 if ($listing[$x]['hide_price'] == '1')
			   {
				$lc_price = HIDE_PRICE_TEXT;
			   }
			 else
			   {
				/*if ($listing[$x]['products_price'] <= 0)
				  {
					$lc_price = '';
				  }
				else
				  {
                                   ///////////
                                    if($flag == 1)
                                    {
					 $lc_text = '';
                                                    $lc_price = '&nbsp;' . $currencies->display_price($listing[$x]['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
				    }
                                   
                                   else
                                      {
                                      /////// 
                                      //     $lc_align = 'right';
                                            if (tep_not_null($listing[$x]['specials_new_products_price']))
                                              {
                                                    $lc_text = '';
                                                    $lc_price = '&nbsp;<s>' .  $currencies->display_price($listing[$x]['products_price'], tep_get_tax_rate($listing[$x]['products_tax_class_id'])) . '</s>&nbsp;&nbsp;<br><span class="productSpecialPrice">' . $currencies->display_price($listing[$x]['specials_new_products_price'], tep_get_tax_rate($listing[$x]['products_tax_class_id'])) . '</span>&nbsp;';
                                              }
                                            else
                                              {
                                                    $lc_text = '';
                                                    $lc_price = '&nbsp;' . $currencies->display_price($listing[$x]['products_price'], tep_get_tax_rate($listing['products_tax_class_id'])) . '&nbsp;';
                                              }
                                      }
                                  } */
			   }  
			 break;
			/*case 'PRODUCT_LIST_QUANTITY':
			 $lc_align = 'right';
			 $lc_text = '&nbsp;' . $listing[$x]['products_quantity'] . '&nbsp;';
			 break;*/
                         // BOF Bundled Products
                        case 'PRODUCT_LIST_QUANTITY':
                          $lc_align = 'right';
                          $lc_text = '&nbsp;' . tep_get_products_stock($listing['products_id']) . '&nbsp;';
                        break;
                        // EOF Bundled Products

          	case 'PRODUCT_LIST_WEIGHT':
             $lc_align = 'right';
             $lc_text = '&nbsp;' . $listing[$x]['products_weight'] . '&nbsp;';
             break;
          	case 'PRODUCT_LIST_IMAGE':
        	 $feed_status = is_xml_feed_product($listing[$x]['products_id']);
			 if ($feed_status && stripos($listing[$x]['products_image'], 'http://')!==false){
				if (@getimagesize($listing[$x]['products_image'])){
					$image = '<img src="' . $listing[$x]['products_image'] . '" title="' . $listing[$x]['products_name'] . '" class="subcatimages" border="0">';
				} else {
					$image = '<img src="' . $listing[$x]['products_mediumimage'] . '" title="' . $listing[$x]['products_name'] . '" width="150" height="150" class="subcatimages" border="0">';
				}
			 } else {
				$image = tep_small_image(DIR_WS_IMAGES . $listing[$x]['products_image'], $listing[$x]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
			 }
             $lc_align = 'center';
             if (isset($HTTP_GET_VARS['manufacturers_id'])) {
			 $lc_text='';
             $lc_image = '<table><tr><td style="border:1px solid #333333;" valign="bottom"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" >' .  $image . '</a></td></tr></table>';
             } else {
			 $lc_text='';
             $lc_image = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '"  >' . $image . '</a>';
			 }
             break;
	     /*   case 'PRODUCT_LIST_BUY_NOW':
             $lc_align = 'center';
             $lc_text = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
             break;*/
             // BOF Bundled Products
            case 'PRODUCT_LIST_BUY_NOW':
              $lc_align = 'center';
              if ($listing['sold_in_bundle_only'] == "yes") {
                $lc_text = TEXT_BUNDLE_ONLY;
              } elseif ((STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') && (tep_get_products_stock($listing['products_id']) < 1)) {
                $lc_text = tep_image_button('button_out_of_stock.gif', IMAGE_BUTTON_OUT_OF_STOCK) . '&nbsp;';
              } else {
                $lc_text = '<a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $listing['products_id']) . '">' . tep_image_button('button_buy_now.gif', IMAGE_BUTTON_BUY_NOW) . '</a>&nbsp;';
              }
              break;
          // EOF Bundled Products

		  }
        $product_contents[] = $lc_text;
	  }

// Query to hide price
	$hide_price_query = tep_db_query("select hide_price from products where products_id = '" . $listing[$x]['products_id'] . "'");
	$hide_price_result = tep_db_fetch_array($hide_price_query);
	$listing[$x]['hide_price'] = $hide_price_result['hide_price'];
// End hide price

// Check for current layout choice
	if(PRODUCT_LISTING_TEMPLATE == 0)
      {
		$product_listing_template_0 = file("includes/sts_templates/".$selected_template."/product_listing_standard.php");
	    $text_display = '';

		// Begin - Old Product Listing Layout
		$lc_text = implode('<br>', $product_contents);
		$list_box_contents[$row][$column] = array('align' => 'left',
												  'params' => 'class="productListing-data" ');
		for($p=0;sizeof($product_listing_template_0) > $p; $p++)
		  { $text_display .= $product_listing_template_0[$p]; }

		$lc_text_compare = '';
		if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
		  {
			$lc_text_compare = tep_draw_form('compare', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) ), 'get');
			//$lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false,'onclick="this.form.submit();"');
            $lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false);
			$lc_text_compare .= $hidden_get_variables;
			$lc_text_compare .= $hidden_get_columns;
			$lc_text_compare .= tep_hide_session_id();
			$lc_text_compare .= '</form>';
		  }

  		$text_display = str_replace("DISPLAY_PRODUCT_IMAGE",$lc_image, $text_display);
		if($listing[$x]['hide_price'] == 1)
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE","<b>Add to cart<br />to see price</b>", $text_display);
		else
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE",$lc_price, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_NAME",$lc_name, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_COMPARE",$lc_text_compare, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_SEPARATOR",tep_draw_separator('pixel_trans.gif','100%','5'), $text_display);

		$list_box_contents[$row][$column] = array('text'  => $text_display);

		$column ++;
		if ($column >= 4)
		  {
			$row ++;
			$column = 0;
		  }
		// End - Old Product Listing Layout
	  }
	elseif(PRODUCT_LISTING_TEMPLATE == 1)
	  {
		// Begin - Ebay Style Listing
		$product_listing_template_1 = file("includes/sts_templates/".$selected_template."/product_listing_column.php");

		$text_display = '';

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
	
			$stock_info = '<b>';
				if($product_info_2['products_quantity'] <= STORE_STOCK_LOW_INVENTORY_QUANTITY && $product_info_2['products_quantity'] > 0)
					$stock_info .= STORE_STOCK_LOW_INVENTORY_MESSAGE;
				elseif($product_info_2['products_quantity'] > STORE_STOCK_LOW_INVENTORY_QUANTITY)
					$stock_info .= 'In Stock';
				else
					$stock_info .= STORE_STOCK_OUT_OF_STOCK_MESSAGE;
			$stock_info .= '</b>';
		  }
	

		// Begin - Old Product Listing Layout
		$lc_text = implode('<br>', $product_contents);
		$list_box_contents[$row][$column] = array('align' => 'left',
													'params' => 'class="productListing-data" ');
		for($p=0;sizeof($product_listing_template_1) > $p; $p++)
		  { $text_display .= $product_listing_template_1[$p]; }

		$lc_text_compare = '';
		if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
		  {
			$lc_text_compare = tep_draw_form('compare', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) ), 'get');
			//$lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false,'onclick="this.form.submit();"');
            $lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false);
			$lc_text_compare .= $hidden_get_variables;
			$lc_text_compare .= $hidden_get_columns;
			$lc_text_compare .= tep_hide_session_id();
			$lc_text_compare .= '</form>';
		  }

  		$text_display = str_replace("DISPLAY_PRODUCT_IMAGE",$lc_image, $text_display);
		if($listing[$x]['hide_price'] == 1)
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE","<b>Add to cart<br />to see price</b>", $text_display);
		else
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE",$lc_price, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_NAME",$lc_name, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_STOCK",$stock_info, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_COMPARE",$lc_text_compare, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_SEPARATOR",tep_draw_separator('pixel_trans.gif','100%','5'), $text_display);

		$list_box_contents[$row][$column] = array('text'  => $text_display);

		$column ++;
		if ($column >= 1)
		  {
			$row ++;
			$column = 0;
		  }
		// End - Ebay Style Listing
	  }
	elseif(PRODUCT_LISTING_TEMPLATE == 2)
      {
		//grab medium picture for descriptive layout
		//begin medium picture
		$product_listing_template_2 = file("includes/sts_templates/".$selected_template."/product_listing_large.php");
		$text_display = '';

        $feed_status = is_xml_feed_product($listing[$x]['products_id']);
		if ($feed_status && stripos($listing[$x]['products_image'], 'http://')!==false){
			if (@getimagesize($listing[$x]['products_mediumimage'])){
				$image = '<img src="' . $listing[$x]['products_mediumimage'] . '" title="' . $listing[$x]['products_name'] . '" class="subcatimages" border="0">';
			} elseif (@getimagesize($listing[$x]['products_image'])){
				$image = '<img src="' . $listing[$x]['products_image'] . '" title="' . $listing[$x]['products_name'] . '" width="150" height="150" class="subcatimages" border="0">';
			} else {
				$src = DIR_WS_IMAGES . DEFAULT_IMAGE;
				$image = tep_image($src, $listing[$x]['products_name']);	
			}
		} else {
        	$image = tep_image(DIR_WS_IMAGES . $listing[$x]['products_image'], $listing[$x]['products_name'], 150, 150,'class="subcatimages" border="0"');
             $image = str_replace("/small/","/medium/",$image);
		}
        $lc_align = 'center';
        if (isset($HTTP_GET_VARS['manufacturers_id']))
		  {
			$lc_text='';
            $lc_image_med = '<table width="100%" style="align: center"><tr><td style="border:1px solid #333333;" valign="bottom"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $HTTP_GET_VARS['manufacturers_id'] . '&products_id=' . $listing[$x]['products_id']) . '" >' .  $image . '</a></td></tr></table>';
          }
		else
		  {
			$lc_text='';
			//$lc_image_med = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '"  >' . str_replace("/small/","/medium/",$image) . '</a>';
			$lc_image_med = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $listing[$x]['products_id']) . '"  >' . $image . '</a>';
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

		for($p=0;sizeof($product_listing_template_2) > $p; $p++)
		  { $text_display .= $product_listing_template_2[$p]; }


		$lc_text_compare = '';
		if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
		  {
			$lc_text_compare = tep_draw_form('compare', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) ), 'get');
			//$lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false,'onclick="this.form.submit();"');
            $lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false);
			$lc_text_compare .= $hidden_get_variables;
			$lc_text_compare .= $hidden_get_columns;
			$lc_text_compare .= tep_hide_session_id();
			$lc_text_compare .= '</form>';
		  }

  		$text_display = str_replace("DISPLAY_PRODUCT_IMAGE",str_replace("noimage.gif","noimage_large.gif",$lc_image_med), $text_display);
		if($listing[$x]['hide_price'] == 1)
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE","<b>Add to cart<br />to see price</b>", $text_display);
		else
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE",$lc_price, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_NAME",$lc_name, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_STOCK",$stock_info, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_COMPARE",$lc_text_compare, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_SEPARATOR",tep_draw_separator('pixel_trans.gif','100%','5'), $text_display);

		$list_box_contents[$row][$column] = array('text'  => $text_display);

		unset($stock_info);
		$column ++;
		if ($column >= 3)
		  {
			$row ++;
			$column = 0;
		  }
		// End -  Begin - Descriptive Template
	  }
	elseif(PRODUCT_LISTING_TEMPLATE == 3)
      {
		//grab medium picture for descriptive layout
		//begin medium picture
		$product_listing_template_3 = file("includes/sts_templates/".$selected_template."/product_listing_large_description.php");
		$text_display = '';

		$feed_status = is_xml_feed_product($listing[$x]['products_id']);
		if ($feed_status && stripos($listing[$x]['products_image'], 'http://')!==false){
			if (@getimagesize($listing[$x]['products_image'])){
				$image = '<img src="' . $listing[$x]['products_image'] . '" title="' . $listing[$x]['products_name'] . '" class="subcatimages" border="0">';
			} else {
				$image = '<img src="' . $listing[$x]['products_mediumimage'] . '" title="' . $listing[$x]['products_name'] . '" width="150" height="150" class="subcatimages" border="0">';
			}
		} else {
			$image = tep_image(DIR_WS_IMAGES . $listing[$x]['products_image'], $listing[$x]['products_name'], 150, 150,'class="subcatimages" border="0"');
		}
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

	  	// Get thedescription of the product
		$description_query = tep_db_query("select products_description from products_description where products_id = '" . $listing[$x]['products_id'] . "'");
		$description_result = tep_db_fetch_array($description_query);
		$products_description = $description_result['products_description'];

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
		for($p=0;sizeof($product_listing_template_3) > $p; $p++)
		  { $text_display .= $product_listing_template_3[$p]; }

		$lc_text_compare = '';
		if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
		  {
			$lc_text_compare = tep_draw_form('compare', tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) ), 'get');
			//$lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false,'onclick="this.form.submit();"');
            $lc_text_compare .= "Compare ".tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false);
			$lc_text_compare .= $hidden_get_variables;
			$lc_text_compare .= $hidden_get_columns;
			$lc_text_compare .= tep_hide_session_id();
			$lc_text_compare .= '</form>';
		  }

		$text_display = str_replace("DISPLAY_PRODUCT_IMAGE",str_replace("noimage.gif","noimage_large.gif",$lc_image_med), $text_display);
		if($listing[$x]['hide_price'] == 1)
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE","<b>Add to cart<br />to see price</b>", $text_display);
		else
			$text_display = str_replace("DISPLAY_PRODUCT_PRICE",$lc_price, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_NAME",$lc_name, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_STOCK",$stock_info, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_SEPARATOR",tep_draw_separator('pixel_trans.gif','100%','5'), $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_DESCRIPTION", $products_description, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_COMPARE",$lc_text_compare, $text_display);
		$text_display = str_replace("DISPLAY_PRODUCT_ADD_TO_CART", $add_to_cart, $text_display);		  
		
		$list_box_contents[$row][$column] = array('text'  => $text_display);
		
		unset($stock_info);
		$column ++;
		if ($column >= 1)
		  {
			$row ++;
			$column = 0;
		  }
		// End - Descriptive Template
	  }
  }
/* boe mod add paging to top of page too */
 if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<table class="paging" border="0" width="100%" cellspacing="0" cellpadding="2">
    <?//// #06 9Jan2014 (MA) BOF?>
  <tr>
    <td class="smallText pagingCount"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText pagingLinks"><?php echo $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
    <td align="right" class="smallText perPage">
        Items per page: 
                        &nbsp;&nbsp;<a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='12';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='12' ? ' style="font-weight:bolder;"' : ''); ?> >12</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='24';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='24' ? ' style="font-weight:bolder;"' : ''); ?> >24</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='50';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='50' ? ' style="font-weight:bolder;"' : ''); ?> >50</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='100';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='100' ? ' style="font-weight:bolder;"' : ''); ?> >100</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='All';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='All' ? ' style="font-weight:bolder;"' : ''); ?> >All</a>&nbsp;&nbsp;
    </td>
  </tr>
  
<?php
// #06 9Jan2014 (MA) EOF
// Begin Compare Button - OBN
	if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
	  {
  		echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="2" align="right"><a href="compare.php?'.tep_get_all_get_params(array('action')).'">'.tep_image_button('button_compare.gif', "Compare Products").'</a></td></tr></table></td></tr>';
	  }
// End Compare Button - OBN
?>
</table>
<?php
  }
/* eoe mod add paging to top of page too */

    new productListingBox($list_box_contents);
    /* BoF Compare Products side-by-side
           Add checkbox to compare products */
    $lc_align = 'center';
    $lc_text .= tep_draw_checkbox_field('columns_'.$listing['products_id'],$listing['products_id'],false,'onclick="this.form.submit();"');
    $lc_text .= $hidden_get_variables;
    $lc_text .= $hidden_get_columns;
    $lc_text .= tep_hide_session_id();
    $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                           'params' => 'class="productListing-data"',
                                           'text'  => $lc_text);
     /* EoF Compare Products side-by-side */ 
  } else {
    $list_box_contents = array();
    $list_box_contents[0] = array('params' => 'class="productListing-odd"');
    $list_box_contents[0][] = array('params' => 'class="productListing-data"',
                                    'text' => TEXT_NO_PRODUCTS);
    new productListingBox($list_box_contents);
	/* BoF Compare Products side-by-side
		 Add checkbox to compare products */
	$lc_align = 'center';
	$lc_text .= tep_draw_checkbox_field('columns_'.$listing[$x]['products_id'],$listing[$x]['products_id'],false,'onclick="this.form.submit();"');
	$lc_text .= $hidden_get_variables;
	$lc_text .= $hidden_get_columns;
	$lc_text .= tep_hide_session_id();
	
	$list_box_contents[$cur_row][] = array('align' => $lc_align,
										   'params' => 'class="productListing-data"',
										   'text'  => $lc_text);
	/* EoF Compare Products side-by-side */ 
  }
  if ( ($listing_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3')) ) {
?>

<table class="paging" border="0" width="100%" cellspacing="0" cellpadding="2">
<?// #06 9Jan2014 (MA) BOF?>
  <tr>
    <td class="smallText pagingCount"><?php echo $listing_split->display_count(TEXT_DISPLAY_NUMBER_OF_PRODUCTS); ?></td>
    <td class="smallText pagingLinks"><?php echo  $listing_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info', 'x', 'y'))); ?></td>
    <td align="right" class="smallText perPage">
        Items per page: 
                        &nbsp;&nbsp;<a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='12';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='12' ? ' style="font-weight:bolder;"' : ''); ?> >12</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='24';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='24' ? ' style="font-weight:bolder;"' : ''); ?> >24</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='50';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='50' ? ' style="font-weight:bolder;"' : ''); ?> >50</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='100';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='100' ? ' style="font-weight:bolder;"' : ''); ?> >100</a>&nbsp;|&nbsp;
                        <a href="javascript://" onclick="javascript:document.forms['filter'].elements['items_per_page'].value='All';document.forms['filter'].submit();" <?php echo ($_SESSION['items_per_page']=='All' ? ' style="font-weight:bolder;"' : ''); ?> >All</a>&nbsp;&nbsp;
    </td>
  </tr>
  <?// #06 9Jan2014 (MA) EOF?>
<?php
// Begin Compare Button - OBN
	if(COMPARE_PRODUCTS_SIDEBYSIDE_ENABLE == 'true')
	  {
  		echo '<tr><td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2"><tr><td colspan="2" align="right"><a href="compare.php?'.tep_get_all_get_params(array('action')).'">'.tep_image_button('button_compare.gif', "Compare Products").'</a></td></tr></table></td></tr>';
	  }
// End Compare Button - OBN
?>
</table>
<?php
  }
?>