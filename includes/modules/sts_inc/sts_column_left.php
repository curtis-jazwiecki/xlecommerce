<?php

/*

  $Id: sts_column_left.php,v 4.3.3 2006/03/12 22:06:41 rigadin Exp $

  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce



  Released under the GNU General Public License



  STS v4.3.3 by Rigadin (rigadin@osc-help.net)



  Based on: Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com

*/



// code to add dyamic modules based on layout #mohit start

    $boxes_query = tep_db_query("SELECT title FROM module_boxes_layout  WHERE type = 'b' and template_id = '". basename(MODULE_STS_TEMPLATE_FOLDER) ."' and status = '1'");
    
    while ($boxes = tep_db_fetch_array($boxes_query)) {
        $sts->template[$boxes['title']] = '';   
    }

    $getquery = tep_db_query("SELECT title,  module_boxes_file FROM module_boxes_layout mbl, module_boxes mb WHERE mb.module_boxes_id = mbl.module_boxes_id and mbl.type = 'b' and mbl.template_id = '".basename(MODULE_STS_TEMPLATE_FOLDER)."' and mbl.status = '1' and FIND_IN_SET ( (SELECT `layout_groups_files_id` FROM `layout_groups_files` WHERE `layout_group_file` LIKE '".basename($PHP_SELF)."'), layout_files)");

	

  

  $sts->restart_capture(); // Clear buffer but do not save it nowhere, no interesting information in buffer.

  

  // Get categories box from db or cache  

	while($getarray = tep_db_fetch_array($getquery)){

	  

	  if(($getarray['module_boxes_file'] == 'categories.php') && (USE_CACHE == 'true') && empty($SID)){

		  	echo tep_cache_categories_box();  
            $sts->restart_capture ($getarray['title'], 'box');

	  }elseif(($getarray['module_boxes_file'] == 'manufacturers.php') && (USE_CACHE == 'true') && empty($SID)){

			echo tep_cache_manufacturers_box();
            $sts->restart_capture ($getarray['title'], 'box');

	  }elseif(($getarray['module_boxes_file'] == 'search_filter.php')){
	       $include = true;
	       if (basename($PHP_SELF) == 'shop.php') {
	           if (isset($_GET['cPath'])){
                 $category = $_GET['cPath'];
                 if (strrpos($category, '_')!==false){
                   $category = substr($category, strrpos($category, '_')+1 );
                }
              $sql = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id='" . (int)$category . "'");
              $info = tep_db_fetch_array($sql);
              if ($info['count']> 0){
               $include = false;
              }            
             } 
	       }

        if (basename($PHP_SELF) == 'index.php') $include = false;
        if ($include) {
			ob_start();

			require(DIR_WS_BOXES . $getarray['module_boxes_file']);

			$search_filterbox = ob_get_contents();

			ob_end_clean();

		    $sts->template['search_filterbox'] = $search_filterbox;
            }

	  }else if(($getarray['module_boxes_file'] == 'manufacturer_info.php') && (isset($HTTP_GET_VARS['products_id']))){

	  		include(DIR_WS_BOXES . $getarray['module_boxes_file']);

			$sts->restart_capture ($getarray['title'], 'box');  

	  }elseif(($getarray['module_boxes_file'] == 'order_history.php') && (tep_session_is_registered('customer_id'))){

		  	include(DIR_WS_BOXES . 'order_history.php');

			$sts->restart_capture ($getarray['title'], 'box');  

	  }elseif(($getarray['module_boxes_file'] == 'product_notifications.php') && (isset($HTTP_GET_VARS['products_id']))){

		  	include(DIR_WS_BOXES . $getarray['module_boxes_file']);

	  		$sts->restart_capture ($getarray['title'], 'box');  

	  }elseif(($getarray['module_boxes_file'] == 'tell_a_friend.php') && isset($HTTP_GET_VARS['products_id']) && basename($PHP_SELF) != FILENAME_TELL_A_FRIEND) {

	  		include(DIR_WS_BOXES . $getarray['module_boxes_file']);

			$sts->restart_capture ($getarray['title'], 'box'); 

	  }else{

	  		include(DIR_WS_BOXES . $getarray['module_boxes_file']);

	  		$sts->restart_capture ($getarray['title'], 'box');

	  }

	}

  

	// code to add dyamic modules based on ends




// Get categories box from db or cache  



  //if ((USE_CACHE == 'true') && empty($SID)) {

   // echo tep_cache_categories_box();

 // } else {

   // include(DIR_WS_BOXES . 'categories.php');

  //}  



 // $sts->restart_capture ('categorybox', 'box');  



// Get manufacturer box from db or cache  



  //if ((USE_CACHE == 'true') && empty($SID)) {

    //echo tep_cache_manufacturers_box();

  //} else {

    //include(DIR_WS_BOXES . 'manufacturers.php');

  //}



  //$sts->restart_capture ('manufacturerbox', 'box');



  //require(DIR_WS_BOXES . 'product_of_the_day.php');



  //$sts->restart_capture ('product_of_the_day', 'box'); // Get product of the day box



// require(DIR_WS_BOXES . 'whats_new.php');



  //$sts->restart_capture ('whatsnewbox', 'box'); // Get What's new box



  //require(DIR_WS_BOXES . 'search.php');



  //$sts->restart_capture ('searchbox', 'box'); // Get search box



  // Added for Search Filter



  /*$search_filterbox = '';



  $temp_file_name = 'search_filter-' . $language . '.cache';



  if(USE_CACHE=='true'){



    if (!read_cache($search_filterbox, $temp_file_name)){



        ob_start();



        require(DIR_WS_BOXES . 'search_filter.php');



        $search_filterbox = ob_get_contents();



        ob_end_clean();



        write_cache($search_filterbox, $temp_file_name);



    }



  } else {*/



   // ob_start();

    //require(DIR_WS_BOXES . 'search_filter.php');

    //$search_filterbox = ob_get_contents();

   // ob_end_clean();



    //write_cache($search_filterbox, $temp_file_name);



  //}



  //$sts->template['search_filterbox'] = $search_filterbox;



/*



require(DIR_WS_BOXES . 'search_filter.php');



$sts->restart_capture ('search_filterbox', 'box'); // Get search filter box   



*/



// End for Search Filter







// Added for Drop Down Menu



  //require(DIR_WS_BOXES . 'drop_down_menu.php');

  //$sts->restart_capture ('drop_down_menu', 'box'); // Get search filter box



  



  //require(DIR_WS_BOXES . 'drop_down_menu_column.php');

  //$sts->restart_capture ('drop_down_menu_column', 'box'); // Get search filter box







 // require(DIR_WS_BOXES . 'fly_out_menu.php');

  //$sts->restart_capture ('fly_out_menu', 'box'); // Get search filter box

 // End for Drop Down Menu







 // Begin Template Check

	$sts->template['stylesheet'] = '<link rel="stylesheet" type="text/css" href="' . DIR_WS_INCLUDES . 'sts_templates/'.MODULE_STS_TEMPLATE_FOLDER.'/stylesheet.css" />' . "\n";
    // Begin Template Check
/*
	$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");

	$check = tep_db_fetch_array($check_query);

	require(DIR_WS_INCLUDES . 'sts_templates/'.$check['configuration_value'].'/stylesheet.css');

	$sts->restart_capture ('stylesheet', 'box'); // Get search filter box
*/
// End Template Check

 // End Template Check







// Begin Related Items

	require(DIR_WS_MODULES . 'xsell_products.php');

	$sts->restart_capture ('display_related_products', 'box'); // Get xsell products module

// Begin Related Items



  //require(DIR_WS_BOXES . 'information.php');

  //$sts->restart_capture ('informationbox', 'box');  // Get information box



  //require(DIR_WS_BOXES . 'departments.php');



  //$sts->restart_capture ('departmentbox', 'box');  // Get information box



  //require(DIR_WS_BOXES . 'shopping_cart.php');

  //$sts->restart_capture ('cartbox', 'box'); // Get shopping cart box



  //if (isset($HTTP_GET_VARS['products_id'])) 

  	//include(DIR_WS_BOXES . 'manufacturer_info.php');

  

  //$sts->restart_capture ('maninfobox', 'box'); // Get manufacturer info box (empty if no product selected)



  //if (tep_session_is_registered('customer_id')) include(DIR_WS_BOXES . 'order_history.php');

  //$sts->restart_capture ('orderhistorybox', 'box'); // Get customer's order history box (empty if visitor not logged)



 // include(DIR_WS_BOXES . 'best_sellers.php');

  //$sts->restart_capture ('bestsellersbox_only', 'box'); // Get bestseller box only, new since v4.0.5

// Get bestseller or product notification box. If you use this, do not use these boxes separately!  



  if (isset($HTTP_GET_VARS['products_id'])) {

    //include(DIR_WS_BOXES . 'product_notifications.php');



	//$sts->restart_capture ('notificationbox', 'box'); // Get product notification box



// Get bestseller or product notification box. If you use this, do not use these boxes separately!    



    if (tep_session_is_registered('customer_id')) {

      $check_query = tep_db_query("select count(*) as count from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customer_id . "' and global_product_notifications = '1'");

      $check = tep_db_fetch_array($check_query);



      if ($check['count'] > 0) {

        $sts->template['bestsellersbox'] = $sts->template['bestsellersbox_only']; // Show bestseller box if customer asked for general notifications

      } else {

        $sts->template['bestsellersbox'] = $sts->template['notificationbox']; // Otherwise select notification box

      }

    } else {

      $sts->template['bestsellersbox'] = $sts->template['notificationbox']; // 

    }

  } else {

    $sts->template['bestsellersbox'] = $sts->template['bestsellersbox_only'];

	$sts->template['notificationbox'] = '';

  }



 // include(DIR_WS_BOXES . 'specials.php');



  //$sts->restart_capture ('specialbox', 'box'); // Get special box

  //$sts->template['specialfriendbox'] = $sts->template['specialbox']; // Shows specials or tell a friend



   // include(DIR_WS_BOXES . 'featured.php');



  //$sts->restart_capture ('featuredbox', 'box'); // Get featured box

  //$sts->template['featuredbox']=$sts->template['featuredbox']; // Show featured products





  //if (isset($HTTP_GET_VARS['products_id']) && basename($PHP_SELF) != FILENAME_TELL_A_FRIEND) {

    // include(DIR_WS_BOXES . 'tell_a_friend.php');

	//$sts->restart_capture ('tellafriendbox', 'box'); // Get tell a friend box

	//$sts->template['specialfriendbox']=$sts->template['tellafriendbox']; // Shows specials or tell a friend

 // } else{

  	//$sts->template['tellafriendbox']='';

 // }





// Get languages and currencies boxes, empty if in checkout procedure  

  if (substr(basename($PHP_SELF), 0, 8) != 'checkout') {

    include(DIR_WS_BOXES . 'languages.php');

    $sts->restart_capture ('languagebox', 'box');

    include(DIR_WS_BOXES . 'currencies.php');

    $sts->restart_capture ('currenciesbox', 'box');

  } else {

    $sts->template['languagebox']='';

    $sts->template['currenciesbox']='';

  }



  $sts->template['currenciestopbox'] = $sts->template['currenciesbox'];



  if (basename($PHP_SELF) != FILENAME_PRODUCT_REVIEWS_INFO) {

    require(DIR_WS_BOXES . 'reviews.php');

    $sts->restart_capture ('reviewsbox', 'box');  // Get the reviews box

  } else {

    $sts->template['reviewsbox']='';

  }	

?>