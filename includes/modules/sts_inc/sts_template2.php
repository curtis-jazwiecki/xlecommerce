<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$template2_blocks = array(

    'top_bar', 

    'header', 

    'free_shipping_banner', 

    'footer_box_01', 

    'footer_box_02', 

    'footer_box_03', 

    'footer_box_04', 

    'footer_copyright', 

    'brands', 
    
    'brands_content' => array( 

        'block_name' => 'brands_content', 

        'type'=>'manufactures', 

        'enabled' => true, 

        'parameters'=>array(),  

    ),
    'navigation_menu' => array( 

        'block_name' => 'navigation_menu', 

        'type'=>'navigation menu', 

        'enabled' => true, 

        'parameters'=>array(),
        
        'filename' => 'navigation_menu.php.html'

    ),

    'newsletter',

    'featured_products' => array( 

        'block_name' => 'featured_products', 

        'type'=>'featured', 

        'enabled' => true, 

        'parameters'=>array('custom'=>true, 'on_home_page'=>true, 'max_count'=>'9', 'items_per_row'=>'3'),  

    ),

    'infobox_01' => array( 

            'block_name' => 'infobox_01', 

            'type'=>'related', 

            'enabled'=>true, 'parameters'=>array() 

    ), 

    'infobox_02' => array( 

            'block_name' => 'infobox_02', 

            'type'=>'featured_categories', 

            'enabled'=> ENABLE_FEATURE_CATEGORY=='True' ? true : false, 

            'parameters'=>array() 

    ), 

    'infobox_03' => array( 

            'block_name' => 'infobox_03', 

            'type'=>'featured_manufacturers', 

            'enabled'=> ENABLE_FEATURE_MANUFACTURERS=='True' ? true : false, 

            'parameters'=>array() 

    ), 

    'infobox_04' => array( 

            'block_name' => 'infobox_04', 

            'type'=>'hot', 

            'enabled'=> ENABLE_HOT_PRODUCTS=='True' ? true : false, 

            'parameters'=>array() 

    ), 

    'infobox_05' => array( 

            'block_name' => 'infobox_05', 

            'type'=>'recommended', 

            'enabled'=> ENABLE_RECOMENDED_PRODUCTS=='True' ? true : false, 

            'parameters'=>array() 

    ), 

    'infobox_06' => array( 

            'block_name' => 'infobox_06', 

            'type'=>'popular', 

            'enabled'=> ENABLE_POPULAR_PRODUCTS=='True' ? true : false, 

            'parameters'=>array('max_count'=>'4') 

    ), 

    'infobox_07' => array( 

            'block_name' => 'infobox_07', 

            'type'=>'also_purchased', 

            'enabled'=> true, 

            'parameters'=>array() 

    ), 

);

$sts->blocks = array();

foreach($template2_blocks as $block){

    if (is_array($block)){

        $is_enabled = $block['enabled'];

        if (!$is_enabled){

            $sts->template[$block['block_name']] = '';

        } else if(!empty($block['filename'])){
            ob_start();
                eval('?>'.get_block_content('template2', $block['block_name']).'<?php;');
                $val = ob_get_contents();
                $sts->template[$block['block_name']] = $val;
                ob_end_clean();
            break;
            
        }else {

            ob_start();

            switch( strtolower( $block['type'] ) ){

                case 'related':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't2_xsell_products-' . $language . '.cache' . $_GET['products_id'];

                        if (USE_CACHE=='true'){

                            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                                include(DIR_FS_CATALOG . DIR_WS_MODULES .  'xsell_products.php');

                                $val = ob_get_contents();

                                write_cache($val, $temp_file);

                            }

                        } else {

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'xsell_products.php');

                            $val = ob_get_contents();

                        }

                    }

                    break;

                case 'featured_categories':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't2_featured_categories-' . $language . '.cache' . $_GET['products_id'];

                        if (USE_CACHE=='true'){

                            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                                include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredCategory.php');

                                $val = ob_get_contents();

                                write_cache($val, $temp_file);

                            }

                        } else {

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredCategory.php');

                            $val = ob_get_contents();

                        }

                    }

                    break;

                case 'featured_manufacturers':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't2_featured_manufacturers-' . $language . '.cache' . $_GET['products_id'];

                        if (USE_CACHE=='true'){

                            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                                include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredManufacturers.php');

                                $val = ob_get_contents();

                                write_cache($val, $temp_file);

                            }

                        } else {

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featuredManufacturers.php');

                            $val = ob_get_contents();

                        }

                    }

                    break;

                case 'hot':

                    $val = '';

                    $temp_file = 't2_hot_products-' . $language . '.cache';

                    if (USE_CACHE=='true'){

                        if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'hot_products.php');

                            $val = ob_get_contents();

                            write_cache($val, $temp_file);

                        }

                    } else {

                        include(DIR_FS_CATALOG . DIR_WS_MODULES .  'hot_products.php');

                        $val = ob_get_contents();

                    }

                    break;

                case 'recommended':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't2_recommended_products-' . $language . '.cache' . $_GET['products_id'];

                        if (USE_CACHE=='true'){

                            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                                include(DIR_FS_CATALOG . DIR_WS_MODULES .  'recomended.php');

                                $val = ob_get_contents();

                                write_cache($val, $temp_file);

                            }

                        } else {

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'recomended.php');

                            $val = ob_get_contents();

                        }

                    }

                    break;

                case 'featured':

                    $custom = false;

                    $on_home_page = false;

                    $max_count = null;

                    $items_per_row = null;

                    if (!empty($block['parameters']['custom'])){

                        $custom = true;

                    }

                    if (!empty($block['parameters']['on_home_page'])){

                        $on_home_page = true;

                    }

                    if (!empty($block['parameters']['max_count'])){

                        $max_count = $block['parameters']['max_count'];

                    }

                    if (!empty($block['parameters']['items_per_row'])){

                        $items_per_row = $block['parameters']['items_per_row'];

                    }

                    include(DIR_FS_CATALOG . DIR_WS_MODULES .  'featured.php');

                    $val = ob_get_contents();

                    break;

                case 'popular':

                    $max_count = null;

                    if (!empty($block['parameters']['max_count'])){

                        $max_count = $block['parameters']['max_count'];

                    }

                    $val = '';

                    $temp_file = 't2_popular_products-' . $language . '.cache';

                    if (USE_CACHE=='true'){

                        if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                            include(DIR_FS_CATALOG . DIR_WS_MODULES .  'popular_products.php');

                            $val = ob_get_contents();

                            write_cache($val, $temp_file);

                        }

                    } else {

                        include(DIR_FS_CATALOG . DIR_WS_MODULES .  'popular_products.php');

                        $val = ob_get_contents();

                    }

                    break; 
                case 'manufactures':
                    
                        include(DIR_FS_CATALOG . DIR_WS_MODULES .  'manufactures_carsoule.php');

                        $val = ob_get_contents();
                    
                    break;
                case 'also_purchased':

                    $cart_items = '';

                    if ($cart){

                        if (is_array($cart->contents)) {

                            reset($cart->contents);

                            while (list($products_id, ) = each($cart->contents)) {

                                $cart_items .= ', ' . (int)$products_id;

                            }

                        }

                        if (!empty($cart_items)){

                            $cart_items = substr($cart_items, 2);

                        }

                    }

                    include(DIR_FS_CATALOG . DIR_WS_MODULES .  'also_purchased_products.php');

                    $val = ob_get_contents();

                    break;

            }

            ob_end_clean();

            $sts->template[$block['block_name']] = $val;

        }

    } else {

        $val = '';

        if (USE_CACHE=='true'){

            $temp_file = 't2_' . $block . '-' . $language . '.cache';

            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                $val = get_block_content('template2', $block);

                write_cache($val, $temp_file);

            }

        } else {

            $val = get_block_content('template2', $block);

        }

        $sts->template[$block] = $val;

    }

}

?>