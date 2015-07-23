<?php
/*
  $Id: compare.php,v 1.2 2004/10/25 23:49:59 cb Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2004 osCommerce

  Released under the GNU General Public License
*/
session_start();

    require('includes/application_top.php');
    require(DIR_WS_LANGUAGES . $language . '/' . 'compare.php'); // Needed for definitions of headings
    
    if (!$languages_id) { 
    	$languages_id = tep_db_query("select language_id from languages where code = '$language'") or die(mysql_error());
    }

    // Uncomment this section and supply your own set of product ids 
    // to debug this contribution in standalone mode
    // product_listing.php  passes this variable along to compare.php
/*
    $columns = array ();
    $columns[] = 29 ;
    $columns[] = 30 ;
    $columns[] = 36 ;
    $columns[] = 60 ;
    $columns[] = 80 ;
*/

/* BoF v1.10 reconstruct columns array from passed variables*/

$columns = array ();
/*
reset($HTTP_GET_VARS);
while (list($key, $value) = each($HTTP_GET_VARS)) {
    if (substr($key,0,8) == 'columns_') {
        if ($value > 0) $columns[] .= $value;
    } 
} 
*/
$columns = explode('|', $_SESSION['compare_models']);
if ($columns) array_pop($columns);

if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') {
   echo 'columns ';
   print_r($columns);
}
/* EoF v1.10 */

// v1.01 if $columns is not set, there is nothing to compare, show appropriate message instead
if (count($columns) >= COMPARE_PRODUCTS_SIDEBYSIDE_MINIMUM) {
    // Code based on Product Listing - using similar configuration settings 
    // create column list
    $define_list = array('COMPARE_PRODUCTS_SIDEBYSIDE_MODEL' => COMPARE_PRODUCTS_SIDEBYSIDE_MODEL,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_NAME' => COMPARE_PRODUCTS_SIDEBYSIDE_NAME,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_MANUFACTURER' => COMPARE_PRODUCTS_SIDEBYSIDE_MANUFACTURER,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_DESCRIPTION' => COMPARE_PRODUCTS_SIDEBYSIDE_DESCRIPTION,
						 'COMPARE_PRODUCTS_SIDEBYSIDE_SPECIFICATIONS' => COMPARE_PRODUCTS_SIDEBYSIDE_SPECIFICATIONS,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_PRICE' => COMPARE_PRODUCTS_SIDEBYSIDE_PRICE,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_QUANTITY' => COMPARE_PRODUCTS_SIDEBYSIDE_QUANTITY,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_WEIGHT' => COMPARE_PRODUCTS_SIDEBYSIDE_WEIGHT,
                         'COMPARE_PRODUCTS_SIDEBYSIDE_IMAGE' => COMPARE_PRODUCTS_SIDEBYSIDE_IMAGE,
// not yet implemented                         'COMPARE_PRODUCTS_SIDEBYSIDE_BUY_NOW' => COMPARE_PRODUCTS_SIDEBYSIDE_BUY_NOW,
                        );

    asort($define_list);
    $column_list = array();
    reset($define_list);
    while (list($key, $value) = each($define_list)) {
      if ($value == 'true' ) $column_list[] = $key;
    }

    $select_column_list = '';
    // required heading field    
//    $select_column_list .= 'pd.products_name, ';
    for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
      switch ($column_list[$i]) {
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_MODEL':
          $select_column_list .= 'p.products_model, ';
          break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_NAME':
          // mandatory field already set 
          $select_column_list .= 'pd.products_name, ';
          break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_MANUFACTURER':
          $select_column_list .= 'm.manufacturers_name, ';
          break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_DESCRIPTION':
          $select_column_list .= 'pd.products_description, ';
          break;
//        case 'COMPARE_PRODUCTS_SIDEBYSIDE_QUANTITY':
 //         $select_column_list .= 'p.products_quantity, ';
  //        break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_SPECIFICATIONS':
          $select_column_list .= 'pd.products_specifications, ';
          break;
	    case 'COMPARE_PRODUCTS_SIDEBYSIDE_IMAGE':
          $select_column_list .= 'p.products_image, ';
          break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_WEIGHT':
          $select_column_list .= 'p.products_weight, ';
          break;
        case 'COMPARE_PRODUCTS_SIDEBYSIDE_FEATURED':
          // not supported in Product Comparison
          //$select_column_list .= 'ax.articles_id, ';
          break;
      }
    }

    //SQL selection, from and where clauses - for consistency put together
    // BoF 1.2 ensure runs without addition of static attribute contribution
    if (COMPARE_PRODUCTS_SIDEBYSIDE_STATIC == 'true') {
      $options = "select distinct po.products_options_id, po.products_options_name as options, po.products_options_is_static as static";
    } else {
      $options = "select distinct po.products_options_id, po.products_options_name as options, '0' as static";
    }
    // EoF 1.2
    
    $pd_details = "select " . $select_column_list . " p.disclaimer_needed, p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price";
    $pd_matches = "  from products_description pd join products p using (products_id) left join
                          manufacturers m using (manufacturers_id) left join
                          specials s on p.products_id = s.products_id
                    where p.products_id in (" . implode(",", $columns) . ")";
    $pd_orderby = " order by p.products_id asc";

    $opt_details = "select p.products_id, po.products_options_id as options_id, po.products_options_name as options, pov.products_options_values_name as value_names, pa.options_values_price as value_prices, pa.price_prefix as value_price_prefixes";
    
    //note, maintenance of entries in the products_options_values_to_products_options table is not enforced
    $opt_matches = "  from products p join products_description pd using (products_id) left join 
                           products_attributes pa using (products_id) left join 
                           products_options po on pa.options_id = po.products_options_id and pd.language_id = po.language_id left join
                           products_options_values pov on pa.options_values_id = pov.products_options_values_id and
                           pd.language_id = pov.language_id
                     where p.products_id in (" . implode(",", $columns) . ")"; 
    if (COMPARE_PRODUCTS_SIDEBYSIDE_STATIC == 'true') {
    	$opt_details .= ", po.products_options_is_visible as visible, po.products_options_is_static as static ";
    	$opt_matches .= " and (po.products_options_is_static = 0 or 
                       ( po.products_options_is_static = 1 and po.products_options_is_visible = 1) )";
        $opt_orderby = " order by p.products_id, po.products_options_is_static desc, po.products_options_name asc";
    } else {   
      $opt_orderby = " order by p.products_id, po.products_options_name asc";
    }

    //BoF PEF v1.2 part 1/2
    if (COMPARE_PRODUCTS_SIDEBYSIDE_PEF == 'true') {
    	
    	//List of pef fields
    	$pef_select  = "select distinct p.products_id, pef.products_extra_fields_id, pef.products_extra_fields_name ";
      $pef_from    = "  from products p join products_to_products_extra_fields p2pef using (products_id) left join 
                             products_extra_fields pef on p2pef.products_extra_fields_id = pef.products_extra_fields_id
                       where p.products_id in (" . implode(",", $columns) . ") and pef.products_extra_fields_status = 1"; 
      $pef_orderby = " order by p.products_id, pef.products_extra_fields_order asc";
      $pef_query = $pef_select . $pef_from . $pef_orderby;
      $pef_result = tep_db_query($pef_query); 
      
      //matrix with pef details
      $pef_details_query = $pef_select . ", p2pef.products_extra_fields_value " . $pef_from . $pef_orderby;
      $pef_details_result = tep_db_query($pef_details_query);
      
      //fill pef matrix
      $pef_matrix = array();
      while ($row = tep_db_fetch_array($pef_details_result)) {
    	        $pef_matrix[$row['products_id']][++$matrix[$row['products_id']][cn]] = $row; 
      }
      if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') print_r($pef_matrix);
      }
    //EoF PEF v1.2 part 1/2


    //get product details
    $productdetailsquery = $pd_details . $pd_matches . " and pd.language_id = '" . (int)$languages_id . "'"  . $pd_orderby;
    $productdetailsresult = tep_db_query($productdetailsquery); 
    
    // product details array
    $pd = array();
    while ($row = tep_db_fetch_array($productdetailsresult)) {
        $pd[$row['products_id']] = $row; 
    }
    if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') print_r($pd);

    //get options list
    $optionsquery = $options . $opt_matches . " and pd.language_id = '" . (int)$languages_id . "'" . $opt_orderby;
    $optionsresult = tep_db_query($optionsquery); 
    $optionscount = tep_db_num_rows($optionsresult);
    
    //get product option details
    $detailsquery = $opt_details . $opt_matches . " and pd.language_id = '" . (int)$languages_id . "'"  . $opt_orderby;
    $optionsdetailsresult = tep_db_query($detailsquery); 

    if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') print_r($columns);
    if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') print_r($columns[cn]);
    
    // options matrix
    $matrix = array();
    while ($row = tep_db_fetch_array($optionsdetailsresult)) {
    	        $matrix[$row['products_id']][++$matrix[$row['products_id']][cn]] = $row; 
    }
    if (COMPARE_PRODUCTS_SIDEBYSIDE_DEBUG == 'true') print_r($matrix);

    // Begin table heading
    $lc_align = "center";
    $cur_row = 0;

    // Product Name
    if (COMPARE_PRODUCTS_SIDEBYSIDE_NAME == 'true') { 
      $list_box_contents[$cur_row][] = array('align' => "left",
                                             'params' => 'class="productListing-heading" valign="top" width="100px"',
                                             'text' => '&nbsp;');
      for ($k = 0; $k < count($columns); $k++) {
        if (isset($HTTP_GET_VARS['manufacturers_id'])) {
          $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $pd[$columns[$k]]['manufacturers_id'] . '&products_id=' . $pd[$columns[$k]]['products_id']) . '">' . $pd[$columns[$k]]['products_name'] . '</a>';
        } else {
          $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $pd[$columns[$k]]['products_id']) . '">' . $pd[$columns[$k]]['products_name'] . '</a>';
        }
        $list_box_contents[$cur_row][] = array('align' => 'left',
                                               'params' => 'class="productListing-heading" valign="top" width="*"',
                                               'text' => $lc_text);
      }
    }

    // Product Image
    if (COMPARE_PRODUCTS_SIDEBYSIDE_IMAGE == 'true') { 
      $cur_row++;
      $list_box_contents[$cur_row][] = array('align' => "left",
                                             'params' => 'class="productListing-heading" valign="baseline"',
                                             'text' => '&nbsp;');
      for ($k = 0; $k < count($columns); $k++) {
        if (isset($HTTP_GET_VARS['manufacturers_id']))
		  {
            $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'manufacturers_id=' . $pd[$columns[$k]]['manufacturers_id'] . '&products_id=' . $pd[$columns[$k]]['products_id']) . '">' . tep_small_image($pd[$columns[$k]]['products_image'], $pd[$columns[$k]]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>';
		  }
		else
		  {
            $lc_text = '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $pd[$columns[$k]]['products_id']) . '">' . tep_small_image($pd[$columns[$k]]['products_image'], $pd[$columns[$k]]['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a>';
          }

        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                               'params' => 'class="productListing-heading" valign="baseline"',
                                               'text' => $lc_text);
      }
    }

    // Product Price
    if (COMPARE_PRODUCTS_SIDEBYSIDE_PRICE == 'true') { 
      $cur_row++;
      $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                             'params' => 'class="compareListing-data"',
                                             'text' => 'Price');
      for ($k = 0; $k < count($columns); $k++) {
	  //Hack by Klavs Klavsen - export tax ID and assume it goes same for all
	  global $mybadtax;
	  $mybadtax = $pd[$columns[$k]]['products_tax_class_id'];
	  //End Hack
        $special_price_ex = $pd[$columns[$k]]['specials_new_products_price'];
   	    if ($special_price_ex > 0) {
          $lc_text = '&nbsp;<s>' .  $currencies->display_price($pd[$columns[$k]]['products_price'], tep_get_tax_rate($pd[$columns[$k]]['products_tax_class_id'])) . '</s><br><span class="productSpecialPrice">' . $currencies->display_price($special_price_ex, tep_get_tax_rate($pd[$columns[$k]]['products_tax_class_id'])) . '</span>&nbsp;';
        } else {
          $lc_text = '&nbsp;' . $currencies->display_price($pd[$columns[$k]]['products_price'], tep_get_tax_rate($pd[$columns[$k]]['products_tax_class_id'])) . '&nbsp;';
        }
        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                               'params' => 'class="compareListing-data"',
                                               'text' => $lc_text);
      }
    }
    // End of Heading
    // Start of Specifications

    if (COMPARE_PRODUCTS_SIDEBYSIDE_MODEL == 'true') { 
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => TABLE_HEADING_MODEL );
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => '&nbsp;' . $pd[$columns[$k]]['products_model'] . '&nbsp;');
        }
    }

    if (COMPARE_PRODUCTS_SIDEBYSIDE_MANUFACTURER == 'true') { 
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => TABLE_HEADING_MANUFACTURER);
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => '&nbsp;' . $pd[$columns[$k]]['manufacturers_name'] . '&nbsp;');
        }
    }

    if (COMPARE_PRODUCTS_SIDEBYSIDE_DESCRIPTION == 'true') {
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => "left",
                                        'params' => 'class="compareListing-data" valign="top"',
                                        'text' => TABLE_HEADING_DESCRIPTION);
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => "left",
                                        'params' => 'class="compareListing-data" valign="top"',
                                        'text' => $pd[$columns[$k]]['products_description']);
        }
    }
    if (COMPARE_PRODUCTS_SIDEBYSIDE_SPECIFICATIONS == 'true') {
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => "left",
                                        'params' => 'class="compareListing-data" valign="top"',
                                        'text' => TABLE_HEADING_SPECIFICATIONS);
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => "left",
                                        'params' => 'class="compareListing-data" valign="top"',
                                        'text' => $pd[$columns[$k]]['products_specifications']);
        }
    }    
/*
	if (COMPARE_PRODUCTS_SIDEBYSIDE_QUANTITY == 'true') {
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => TABLE_HEADING_QUANTITY);
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => '&nbsp;' . $pd[$columns[$k]]['products_quantity'] . '&nbsp;');
        }
    }
*/

    if (COMPARE_PRODUCTS_SIDEBYSIDE_WEIGHT == 'true') {
        $cur_row++;
        $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => TABLE_HEADING_WEIGHT);
        for ($k = 0; $k < count($columns); $k++) {
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                        'params' => 'class="compareListing-data"',
                                        'text' => '&nbsp;' . $pd[$columns[$k]]['products_weight'] . '&nbsp;');
        }
    }

    // BoF PEF v1.2 part 2/2
    // loop through all possible product extra fields  
    if (COMPARE_PRODUCTS_SIDEBYSIDE_PEF == 'true') {
      while ($row = tep_db_fetch_array($pef_result)) {
          $cur_row++;
          $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                 'params' => 'class="compareListing-data"',
                                                 'text'  => $row['products_extra_fields_name']);
    	  for ($k = 0; $k < count($columns); $k++) {
            $displaycell = "&nbsp;";
            foreach ($pef_matrix as $prod => $records) {
                if ( $columns[$k] == $prod ) {
                    foreach ($records as $key => $value) {	
            	        if ( $value['products_extra_fields_name'] == $row['products_extra_fields_name'] ) {
            	            $displaycell .= $value['products_extra_fields_value'] . '<br>';
            	        }
            	    }
            	}
            }
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                   'params' => 'class="compareListing-data"',
                                                   'text'  => $displaycell);
    	  } 
      } // end while
    }
    // EoF PEF v1.2 part 2/2
    
    // loop through all possible static options  
    if (COMPARE_PRODUCTS_SIDEBYSIDE_STATIC == 'true') {
      while ($row = tep_db_fetch_array($optionsresult)) {
      	if ( $row['options'] and $row['static']) {
          $cur_row++;
          $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                 'params' => 'class="compareListing-data"',
                                                 'text'  => $row['options']);
    	  for ($k = 0; $k < count($columns); $k++) {
            $displaycell = "&nbsp;";
            foreach ($matrix as $prod => $records) {
                if ( $columns[$k] == $prod ) {
                    foreach ($records as $key => $value) {	
            	        if ( $value['options'] == $row['options'] ) {
            	            $displaycell .= $value['value_names'];
			    $displaycell .= '<br>';
            	        }
            	    }
            	}
            }
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                   'params' => 'class="compareListing-data"',
                                                   'text'  => $displaycell);
    	  } 
        } // end if 
      } // end while
    }
    
    // loop through all possible real options
    if (COMPARE_PRODUCTS_SIDEBYSIDE_OPTIONS == 'true') {
      if(tep_db_num_rows($optionsresult) > 0){
        mysql_data_seek($optionsresult, 0); 
      }
     
      while ($row = tep_db_fetch_array($optionsresult)) {
        if ( $row['options'] and ($row['static'] == '0' )) {
          $cur_row++;
          $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                 'params' => 'class="compareListing-data"',
                                                 'text'  => $row['options']);
          for ($k = 0; $k < count($columns); $k++) {
            $displaycell = "&nbsp;";
            foreach ($matrix as $prod => $records) {
                if ( $columns[$k] == $prod ) {
                    foreach ($records as $key => $value) {	
            	        if ( $value['options'] == $row['options'] ) {
            	            $displaycell .= $value['value_names'];
			   if ($value['value_prices'] != '0')
			       $displaycell .= '(' .
				   $value['value_price_prefixes'] .
				   $currencies->display_price($value['value_prices'],
					   tep_get_tax_rate($mybadtax)) .')';
			       $displaycell .=  '<br>';
            	        }
            	    }
            	}
            }
            $list_box_contents[$cur_row][] = array('align' => $lc_align,
                                                  'params' => 'class="compareListing-data"',
                                                   'text'  => $displaycell);
    	  } 
        } // end if 
      } // end while
    } //end if

   //ADD BUY NOW
   $cur_row++;
   $list_box_contents[$cur_row][] = array('align' => "left",
                                          'params' => 'class="compareListing-data" valign="top"',
                                          'text' => 'Add To Cart');
   for ($k = 0; $k < count($columns); $k++) {

// Add disclaimer if needed 
	if($pd[$columns[$k]]['disclaimer_needed'] == 1)
      {
		$display_products_disclaimer = tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_product')) . '<input type="checkbox"  value="" id="disclaimer'.$k.'">'; 

		$display_products_disclaimer .= '<script language="javascript"><!--'."\n".
										"document.write('<a href=\"javascript:popupWindow2(\\'" .
										tep_href_link('disclaimer.html'). '\\\')"><span class="smallText">' . TEXT_AGREE . '</span></a>\');';

		$display_products_disclaimer .= "function disclaimer_onclick".$k."()
										  {
											var disclaimer".$k."=document.getElementById('disclaimer".$k."');
											if (!disclaimer".$k.".checked)
											alert('" . TEXT_DISCLAIMER_ERROR . "');
											return disclaimer".$k.".checked;
										   }//--></script>";

		$display_products_add_to_cart = '<center>'.tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, ( 'onclick="javascript:return disclaimer_onclick'.$k.'();"')) . '</center>';

		 $buy_now = $display_products_disclaimer . $display_products_add_to_cart . "<br />" . '<input type="hidden" value="'.$columns[$k].'" name="products_id">' . '&nbsp;</form>';
   	  }
	else
      {
		 //$buy_now = tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_product')) . '<center>'.tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, '') . '</center>' . '<input type="hidden" value="'.$columns[$k].'" name="products_id">' . '&nbsp;</form>';
            //$buy_now = tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_product')) . '<center>' . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, '') . '<input type="submit" value="add to cart" class="addtocart_btn"></center>' . '<input type="hidden" value="'.$columns[$k].'" name="products_id">' . '&nbsp;</form>';
			
			$buy_now = tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, 'action=add_product')) . '<center> <input type="submit" value="add to cart" class="addtocart_btn"></center>' . '<input type="hidden" value="'.$columns[$k].'" name="products_id">' . '&nbsp;</form>';
   	  }
     $list_box_contents[$cur_row][] = array('align' => "left",
                                            'params' => 'class="compareListing-data" valign="top"',
											'text' => $buy_now);
   }

} else { // v1.01 not enough products were selected --> end if $columns
 $warningmsg = sprintf(ERR_NO_ITEMS_SELECTED,
COMPARE_PRODUCTS_SIDEBYSIDE_MINIMUM);


}
// close the php and start an html table 
?> 
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
  <?php
// BOF: Header Tag Controller v2.6.0
if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {
  require(DIR_WS_INCLUDES . 'header_tags.php');
} else {
?> 
<title><?php echo TITLE; ?></title>
<?php
}
// EOF: Header Tag Controller v2.6.0
?>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<link rel="stylesheet" type="text/css" href="stylesheet.css">
<script language="javascript"><!--
function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<!-- body //-->
<table background-color="white" border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
<?php if (COMPARE_PRODUCTS_SIDEBYSIDE_COLUMN_LEFT == 'true') { ?>
	    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<?php } ?>
<!-- body_text //-->

    <td valign="top">
      <table width="100%" cellpadding="0px" cellspacing="0px" border="0px">
        <tr>
          <td>

<?php
  if ($warningmsg) {
//    echo '<table><tr class="messageStackError"><td class="messageStackError"><img src="images/icons/error.gif" border="0" alt="ICON_ERROR" title=" ICON_ERROR " width="20" height="20">&nbsp;&nbsp;<font color="#FF0000">' . $warningmsg . '</font></td></tr></table>';

    new infoBox(array(array('text' => $warningmsg . '*' . count($_SESSION['compare_models']) . '*')));
  } else {
  	new productListingBox($list_box_contents);
  }
?> 

          </td>
        </tr>
      </table>
    </td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
      </table>
    </td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>