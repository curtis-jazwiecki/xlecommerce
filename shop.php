<?php
/*
  $Id: index.php,v 1.1 2003/06/11 17:37:59 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
  require('includes/application_top.php');
  // BOF Separate Pricing Per Customer
  if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
  $customer_group_id = $_SESSION['sppc_customer_group_id'];
  } else {
   $customer_group_id = '0';
  }
// EOF Separate Pricing Per Customer 


// begin price filter
	$price_filter = '';
	if(isset($HTTP_GET_VARS['pto']) && isset($HTTP_GET_VARS['pfrom']))
	  {
		  $price_filter = ' AND (p.products_price >= "'.$HTTP_GET_VARS['pfrom'].'" AND p.products_price <= "'.$HTTP_GET_VARS['pto'].'")';
	  }
// end price filter

// the following cPath references come from application_top.php
  $category_depth = 'top';
  if (isset($cPath) && tep_not_null($cPath)) {
    $categories_products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_TO_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
    $cateqories_products = tep_db_fetch_array($categories_products_query);
    if ($cateqories_products['total'] > 0) {
      $category_depth = 'products'; // display products
    } else {
      $category_parent_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where categories_status = '1' and parent_id = '" . (int)$current_category_id . "'");
      $category_parent = tep_db_fetch_array($category_parent_query);
      if ($category_parent['total'] > 0) {
        $category_depth = 'nested'; // navigate through the categories
      } else {
        $category_depth = 'products'; // category has no products, but display the 'no products' message
      }
    }
  }
// Add-on - Information Pages Unlimited
  require_once(DIR_WS_FUNCTIONS . 'information.php');
  tep_information_shop_define(); // Should be called before the Default Language is defined
  
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);
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

<style type="text/css">
</style>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top">
      <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
		<!-- left_navigation //-->
		<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
		<!-- left_navigation_eof //-->
	  </table>
    </td>
<!-- body_text //-->
<?php
if ($category_depth == 'nested')
  {
	$check_query = tep_db_query("select categories_id from ".TABLE_CATEGORIES." where parent_id = '".(int)$current_category_id."' and categories_status = '1' ");
	/*
    while($check = tep_db_fetch_array($check_query))
	  {
      	$level2_query = tep_db_query("select count(*) as total from ".TABLE_CATEGORIES." where parent_id = '".(int)$check['categories_id']."' and categories_status = '1'");
      	$level2_array = tep_db_fetch_array($level2_query);
      	if($level2_array['total'] > 0)
	  	  {
        	$display_type = 'bullet';
			break;
	   	  }
	  	else
	      {
			$display_type = 'column';
	   	  }
      }
      */
    $display_type = 'column';
    $category_query = tep_db_query("select cd.categories_name, c.categories_image, cd.categories_htc_description, c.banner_image from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.categories_id = '" . (int)$current_category_id . "' and cd.categories_id = '" . (int)$current_category_id . "' and cd.language_id = '" . (int)$languages_id . "'");
    $category = tep_db_fetch_array($category_query);
?>
	<td width="100%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
              <td class="maincat_title"><h1><?php echo $category['categories_name']; ?></h1></td>
          	</tr>
		<?php
		if (tep_not_null($category['categories_htc_description']))
		  { ?> 
            <tr>
              <td valign="top" style="padding-top:5px; ">
			    <?php echo $category['categories_htc_description']; ?>
              </td>
            </tr>
		<?php
          }
		?>
		<?php
        if ($category['banner_image'] != '')
		  {
		?> 
            <tr>
              <td><h2><?php echo tep_image(DIR_WS_IMAGES . $category['banner_image'], $category['categories_name']); ?></h2></td>
            </tr>
		<?php
          }
		?>
        </table>
      </td>
    </tr>
    <tr>
      <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
    <tr>
      <td>
      	<table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    if (isset($cPath) && strpos('_', $cPath))
	  { 
// check to see if there are deeper categories within the current category
		$category_links = array_reverse($cPath_array);
		for($i=0, $n=sizeof($category_links); $i<$n; $i++)
		  {
			$categories_query = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
			$categories = tep_db_fetch_array($categories_query);
			if ($categories['total'] < 1)
			  {
				// do nothing, go through the loop
			  }
			else
			  {
				$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$category_links[$i] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
				break; // we've found the deepest category the customer is in
			  }
		  }
	  }
	else
	  {
	      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.categories_image, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$current_category_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
	  }
	if ($display_type == 'bullet')
	  {
?>
    <tr>
        <td valign="top"><?php include(DIR_WS_MODULES . 'categories_bullet.php'); ?>
         
	
<?php
	  }
	else
	  {
?>
    <tr>
        <td><?php  include(DIR_WS_MODULES . 'categories_column.php'); ?>

<?php } ?>
        </td>
<?php
/*
if ($category['categories_htc_description'] != '') {
	echo '<td valign="top" align="right">';
	include(DIR_WS_MODULES . 'categories_sidebanners.php');
	echo '</td>';
}
*/
?>
</tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
            <td><?php //include(DIR_WS_MODULES . FILENAME_NEW_PRODUCTS); ?></td>
          </tr>
		 
        </table></td>
      </tr>
    </table></td>
<?php
  } elseif ($category_depth == 'products' || isset($HTTP_GET_VARS['manufacturers_id'])) {
	

// #06 9Jan2014 (MA) BOF
/* BOE Sort by instock - jr */

  // if the get var does not exist
  if ( !isset( $_GET['sort_by_instock'] ) )
  {
    // check if a session exists
    if ( !isset($_SESSION['sort_by_instock']) )
    {
      // it does not, default to 1
      $_SESSION['sort_by_instock'] = 1;
    }
  // there was a get var,  set the session to it
  }
  else
  {
    $_SESSION['sort_by_instock'] = $_GET['sort_by_instock'];
  }

  // set the checked status of the radio buttons
  $sort_by_instock_yes_checked = $_SESSION['sort_by_instock'] == 1 ? 'checked' : '';
  $sort_by_instock_no_checked = $_SESSION['sort_by_instock'] == 0 ? 'checked' : '';

  // this is part of the sql query
  $sort_by_instock_sql = $_SESSION['sort_by_instock'] == 1 ? 'IF(p.products_quantity = 0, "p.products_quantity DESC", null), ' : '';
  
  /* EOE Srot by instock */
// #06 9Jan2014 (MA) BOF


// create column list
    $define_list = array('PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                         'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                         'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                         'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                         'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                         'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                         'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                         'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW,
                         'PRODUCT_RATING' => PRODUCT_RATING   
                        );   

    asort($define_list);
	
    $column_list = array();
    reset($define_list);
    while (list($key, $value) = each($define_list)) {
      if ($value > 0) $column_list[] = $key;
    }
    //print_r($column_list);
    
    // BOF Separate Pricing Per Customer
// this will build the table with specials prices for the retail group or update it if needed
// this function should have been added to includes/functions/database.php
   if ($customer_group_id == '0') {
     tep_db_check_age_specials_retail_table();
   }
   $status_product_prices_table = false;
   $status_need_to_get_prices = false;

   // find out if sorting by price has been requested
   if ( (isset($HTTP_GET_VARS['sort'])) && (preg_match('/[1-8][ad]/', $HTTP_GET_VARS['sort'])) && (substr($HTTP_GET_VARS['sort'], 0, 1) <= sizeof($column_list)) && $customer_group_id != '0' ){
    $_sort_col = substr($HTTP_GET_VARS['sort'], 0 , 1);
    if ($column_list[$_sort_col-1] == 'PRODUCT_LIST_PRICE') {
      $status_need_to_get_prices = true;
      }
   }

   if ($status_need_to_get_prices == true && $customer_group_id != '0') {
   $product_prices_table = TABLE_PRODUCTS_GROUP_PRICES.$customer_group_id;
   // the table with product prices for a particular customer group is re-built only a number of times per hour
   // (setting in /includes/database_tables.php called MAXIMUM_DELAY_UPDATE_PG_PRICES_TABLE, in minutes)
   // to trigger the update the next function is called (new function that should have been
   // added to includes/functions/database.php)
   tep_db_check_age_products_group_prices_cg_table($customer_group_id);
   $status_product_prices_table = true;

   } // end if ($status_need_to_get_prices == true && $customer_group_id != '0')
// EOF Separate Pricing Per Customer


    $select_column_list = '';
    $select_column_list = "p.sold_in_bundle_only, ";
   // print_r($column_list);
//die('ee');
    for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
      switch ($column_list[$i]) {
        case 'PRODUCT_LIST_MODEL':
          $select_column_list .= 'p.products_model, ';
          break;
        case 'PRODUCT_LIST_NAME':
          $select_column_list .= 'pd.products_name, ';
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $select_column_list .= 'm.manufacturers_name, ';
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $select_column_list .= 'p.products_quantity, ';
          break;
        case 'PRODUCT_RATING':
          $select_column_list .= 'p.reviews_rating, ';
          break;
        case 'PRODUCT_LIST_IMAGE':
			//BOF:20131023
			/*
			//EOF:20131023
          $select_column_list .= 'p.products_image, ';
			//BOF:20131023
			*/
			$select_column_list .= 'p.products_image, products_mediumimage, ';
			//EOF:20131023
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $select_column_list .= 'p.products_weight, ';
          break;
      }
    }
    //print_r($column_list);
/************************Category Status MOD BEGIN by tech1@outdoorbusinessnetwork.com*************************/
// show the products of a specified manufacturer
    if (isset($HTTP_GET_VARS['manufacturers_id'])) {
      if (isset($HTTP_GET_VARS['filter_id']) && tep_not_null($HTTP_GET_VARS['filter_id'])) {
     // BOF Separate Pricing Per Customer
	if ($status_product_prices_table == true) { // ok in mysql 5
	$listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, tmp_pp.products_price, p.products_tax_class_id, IF(tmp_pp.status, tmp_pp.specials_new_products_price, NULL) as specials_new_products_price, IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . $product_prices_table . " as tmp_pp using(products_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd , " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=="true" ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";		
	} else { // either retail or no need to get correct special prices -- changed for mysql 5
	$listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS_RETAIL_PRICES . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";
	} // end else { // either retail...
// EOF Separate Pricing Per Customer
      } else {
// We show them all
// BOF Separate Pricing Per Customer
        if ($status_product_prices_table == true) { // ok in mysql 5
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, tmp_pp.products_price, p.products_tax_class_id, IF(tmp_pp.status, tmp_pp.specials_new_products_price, NULL) as specials_new_products_price, IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . $product_prices_table . " as tmp_pp using(products_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";	
	} else { // either retail or no need to get correct special prices -- changed for mysql 5
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . TABLE_SPECIALS_RETAIL_PRICES . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m where p.products_status = '1' and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";
	} // end else { // either retail...
// EOF Separate Pricing Per Customer
      }
    } else {
// show the products in a given categorie
      if (isset($HTTP_GET_VARS['filter_id']) && tep_not_null($HTTP_GET_VARS['filter_id'])) {
// We are asked to show only specific catgeory;  
// BOF Separate Pricing Per Customer
        if ($status_product_prices_table == true) { // ok for mysql 5
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, tmp_pp.products_price, p.products_tax_class_id, IF(tmp_pp.status, tmp_pp.specials_new_products_price, NULL) as specials_new_products_price, IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) as final_price from " . TABLE_PRODUCTS . " p left join " . $product_prices_table . " as tmp_pp using(products_id), " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";	
        } else { // either retail or no need to get correct special prices -- ok in mysql 5
        $listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_MANUFACTURERS . " m, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c left join " . TABLE_SPECIALS_RETAIL_PRICES . " s using(products_id) where p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['filter_id'] . "' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";
        } // end else { // either retail...
// EOF Separate Pricing Per Customer
      } else {
// We show them all
// BOF Separate Pricing Per Customer --last query changed for mysql 5 compatibility
        if ($status_product_prices_table == true) {
	// original, no need to change for mysql 5
	$listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, tmp_pp.products_price, p.products_tax_class_id, IF(tmp_pp.status, tmp_pp.specials_new_products_price, NULL) as specials_new_products_price, IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) as final_price from " . TABLE_PRODUCTS_DESCRIPTION . " pd left join " . $product_prices_table . " as tmp_pp using(products_id), " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') ";
        } else { // either retail or no need to get correct special prices -- changed for mysql 5
        //$listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_SPECIALS_RETAIL_PRICES . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.products_id = p2c.products_id and pd.products_id = p2c.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "' and m.manufacturers_status='1' " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and p.products_quantity>='" . (int)STOCK_MINIMUM_VALUE . "' " : '') . " and p.is_store_item='0' "; 
		$listing_sql = "select " . $select_column_list . " p.products_id, p.manufacturers_id, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id = m.manufacturers_id left join " . TABLE_SPECIALS_RETAIL_PRICES . " s on p.products_id = s.products_id, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c where p.products_status = '1' and p.products_id = p2c.products_id and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p2c.categories_id = '" . (int)$current_category_id . "' and (m.manufacturers_status='1' or m.manufacturers_status is null) " . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS=='true' ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' and (p.parent_products_model is NULL or p.parent_products_model = '') "; 
      } // end else { // either retail...
// EOF Separate Pricing per Customer
     
        }
      	
    }
    
/************************Category Status MOD END by tech1@outdoorbusinessnetwork.com*************************/
   
    if ( (!isset($HTTP_GET_VARS['sort'])) || (!preg_match('/[1-9][ad]/', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'], 0, 1) > sizeof($column_list)) ) {
      for ($i=0, $n=sizeof($column_list); $i<$n; $i++) {
        // #06 9Jan2014 (MA) BOF
        if ($column_list[$i] == 'PRODUCT_LIST_PRICE') {
        // #06 9Jan2014 (MA) EOF
          $HTTP_GET_VARS['sort'] = PRODUCT_LIST_PRICE . 'a';
          // #06 9Jan2014 (MA) BOF
          $listing_sql .= " order by ".$sort_by_instock_sql." final_price";
          // #06 9Jan2014 (MA) EOF
          
          break;
        }
      }
    } else {
         //print_r($column_list);
      //die('ee');
      $sort_col = substr($HTTP_GET_VARS['sort'], 0 , 1);
      $sort_order = substr($HTTP_GET_VARS['sort'], 1);
      $listing_sql .= ' order by ';
      // #06 9Jan2014 (MA) BOF
      $listing_sql .= $sort_by_instock_sql;
      // #06 9Jan2014 (MA) EOF
      
      switch ($column_list[$sort_col-1]) {
        case 'PRODUCT_LIST_MODEL':
          $listing_sql .= "p.products_model " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_NAME':
          $listing_sql .= "pd.products_name " . ($sort_order == 'd' ? 'desc' : '');
          break;
        case 'PRODUCT_LIST_MANUFACTURER':
          $listing_sql .= "m.manufacturers_name " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_QUANTITY':
          $listing_sql .= "p.products_quantity " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_LIST_IMAGE':
          $listing_sql .= "pd.products_name";
          break;
        case 'PRODUCT_LIST_WEIGHT':
          $listing_sql .= "p.products_weight " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        //case '3':
        case 'PRODUCT_LIST_PRICE':
          $listing_sql .= "final_price " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        case 'PRODUCT_RATING':
          $listing_sql .= "p.reviews_rating " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
        default:
            $listing_sql .= "final_price " . ($sort_order == 'd' ? 'desc' : '') . ", pd.products_name";
          break;
      }
    }
    
  //  echo $listing_sql;

if (isset($HTTP_GET_VARS['manufacturers_id'])) 
      $db_query = tep_db_query("select manufacturers_htc_title_tag as htc_title, manufacturers_htc_description as htc_description from " . TABLE_MANUFACTURERS_INFO . " where languages_id = '" . (int)$languages_id . "' and manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'");
    else 
      $db_query = tep_db_query("select categories_name as htc_title, categories_htc_description as htc_description from " . TABLE_CATEGORIES_DESCRIPTION . " where categories_id = '" . (int)$current_category_id . "' and language_id = '" . (int)$languages_id . "'");
    $htc = tep_db_fetch_array($db_query);
	
    ?>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td>
    <script>
        /*jQuery(document).ready(function(){
            jQuery('input:checkbox[name^="columns_"]').click(function(){
                var product_id = jQuery(this).val();
                var action = (jQuery(this).is(':checked') ? 'add' : 'remove'); 
                jQuery.ajax({
                   url: '<?php echo HTTPS_SERVER . DIR_WS_HTTPS_CATALOG; ?>register_product_for_comparison.php', 
                   method: 'post', 
                   data: {
                        action: action, 
                        id: product_id
                   }, 
                   success: function(response){
                        if (response=='added'){
                            alert('Product added for comparison');
                        } else if (response=='removed'){
                            alert('Product removed from comparison');
                        }
                   }
                });
            });
        });*/
    </script>
        <table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
             <td class="maincat_title"><h1><?php echo $htc['htc_title']; ?></h1></td>
<?php
// optional Product List Filter

    if (PRODUCT_LIST_FILTER > 0) {
	echo '</tr><tr>';
      if (isset($HTTP_GET_VARS['manufacturers_id'])) {
        $filterlist_sql = "select distinct c.categories_id as id, cd.categories_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where (p.parent_products_model = '' or p.parent_products_model IS NULL) and c.categories_status = '1' and p.products_status = '1' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and p2c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and p.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "' order by cd.categories_name";
      } else {
        $filterlist_sql= "select distinct m.manufacturers_id as id, m.manufacturers_name as name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, ".TABLE_CATEGORIES." c, " . TABLE_MANUFACTURERS . " m where (p.parent_products_model = '' or p.parent_products_model IS NULL) and c.categories_status = '1' and p.products_status = '1' and p.manufacturers_id = m.manufacturers_id and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id and  p2c.categories_id = '" . (int)$current_category_id . "' and m.manufacturers_status='1'  order by m.manufacturers_name";
      }
      //echo $filterlist_sql;
      $filterlist_query = tep_db_query($filterlist_sql);
      if (tep_db_num_rows($filterlist_query) > 0) {
        echo '            <td align="left" class="main">' . tep_draw_form('filter_products', FILENAME_DEFAULT, 'get') . TEXT_SHOW . '&nbsp;';
        if (isset($HTTP_GET_VARS['manufacturers_id'])) {
          echo tep_draw_hidden_field('manufacturers_id', $HTTP_GET_VARS['manufacturers_id']);
          $options = array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES));
        } else {
          echo tep_draw_hidden_field('cPath', $cPath);
          $options = array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS));
        }
      }else{
          echo '            <td align="left" class="main">' . tep_draw_form('filter', FILENAME_DEFAULT, 'get') ;
          echo tep_draw_hidden_field('cPath', $cPath);
      }
        // #06 9Jan2014 (MA) BOF        
        //echo tep_draw_hidden_field('sort', $HTTP_GET_VARS['sort']);
        
        if ( empty( $_GET['items_per_page'] ) ) {
        if ( empty( $_SESSION['items_per_page'] ) ) {
          $_SESSION['items_per_page'] = '24';
        }
      } else {
        $_SESSION['items_per_page'] = $_GET['items_per_page'];
      }
      if (tep_db_num_rows($filterlist_query) > 1) {
        while ( $filterlist = tep_db_fetch_array( $filterlist_query ) ) {
          $options[] = array('id' => $filterlist['id'], 'text' => $filterlist['name']);
        }
        echo tep_draw_pull_down_menu('filter_id', $options, (isset($HTTP_GET_VARS['filter_id']) ? $HTTP_GET_VARS['filter_id'] : ''), 'onchange="this.form.submit()"');
      }
        echo '<select name="sort" onchange="this.form.submit();">
                <option value="' . PRODUCT_LIST_NAME . 'a" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_LIST_NAME . 'a' ? ' selected ' : '' ) . '>Name - A to Z</option>
                <option value="' . PRODUCT_LIST_NAME . 'd" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_LIST_NAME . 'd' ? ' selected ' : '' ) . '>Name - Z to A</option>
                <option value="' . PRODUCT_LIST_PRICE . 'a" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_LIST_PRICE . 'a' ? ' selected ' : '' ) . '>Price - Low to High</option>
                <option value="' . PRODUCT_LIST_PRICE . 'd" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_LIST_PRICE . 'd' ? ' selected ' : '' ) . '>Price - High to Low</option>
                <option value="' . PRODUCT_RATING . 'a" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_RATING . 'a' ? ' selected ' : '' ) . '>Rating - Low to High</option>
                <option value="' . PRODUCT_RATING . 'd" ' . ( $HTTP_GET_VARS['sort']==PRODUCT_RATING . 'd' ? ' selected ' : '' ) . '>Rating - High to Low</option>
             </select>';
      echo '<input type="hidden" name="items_per_page" id="items_per_page" value="' . $_SESSION['items_per_page'] . '" />';
?>
        In Stock First:
        <input type="radio" name="sort_by_instock" value="1" <?php echo $sort_by_instock_yes_checked ?> onChange="this.form.submit();">Y</input>
        <input type="radio" name="sort_by_instock" value="0" <?php echo $sort_by_instock_no_checked ?> onChange="this.form.submit();">N</input>
        <?php
        // #06 9Jan2014 (MA) EOF
        echo '</form></td>' . "\n";
      
    }

// Get the right image for the top-right
   $image = DIR_WS_IMAGES . 'table_background_list.gif';
    if (isset($HTTP_GET_VARS['manufacturers_id'])) {
      $image = tep_db_query("select manufacturers_image from " . TABLE_MANUFACTURERS . " where manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'");
      $image = tep_db_fetch_array($image);
      $image = $image['manufacturers_image'];
    } elseif ($current_category_id) {
      $image = tep_db_query("select categories_image from " . TABLE_CATEGORIES . " where categories_id = '" . (int)$current_category_id . "'");
      $image = tep_db_fetch_array($image);
      $image = $image['categories_image'];
    }
    
?>
          </tr>
          <?php if (tep_not_null($htc['htc_description'])) { ?> 
          <tr>
           <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
          <tr>
           <td colspan="2"><h2><?php echo $htc['htc_description']; ?></h2></td>
          </tr>
          <?php } ?>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><?php include(DIR_WS_MODULES . FILENAME_PRODUCT_LISTING); ?></td>
      </tr>
    </table></td>
<?php
  } else { // default page
?>
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
           <tr>
            <td class="main"><?php echo TEXT_MAIN; ?></td>
          </tr>
          <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">

          <tr>
    <td><?php include(DIR_WS_MODULES . 'main_categories.php'); ?>
    </td></tr>
         
<?php
    include(DIR_WS_MODULES . FILENAME_UPCOMING_PRODUCTS);
?>
        </table></td>
      </tr>
    </table></td>
<?php
  }
?>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>
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
