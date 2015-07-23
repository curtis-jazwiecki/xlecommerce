<?php

if ( file_exists(DIR_FS_CATALOG . STS_TEMPLATE_DIR . 'dresscode_constants.php') ) {
    require( DIR_FS_CATALOG . STS_TEMPLATE_DIR. 'dresscode_constants.php');
}

if ( file_exists(DIR_FS_CATALOG . STS_TEMPLATE_DIR . 'dresscode_theme/dresscode_blocks/shopping_cart.php') ) {
    require( DIR_FS_CATALOG . STS_TEMPLATE_DIR . 'dresscode_theme/dresscode_blocks/shopping_cart.php');
   $sts->restart_capture ('header_cart', 'box'); 
} 

$dresscode_grids = 3;
/* constants */

    $main_page_url = false;
    if (basename($PHP_SELF) == 'index.php' && !isset($HTTP_GET_VARS['cPath']) && !isset($HTTP_GET_VARS['manufacturers_id'])) {
        $main_page_url = true;
    }
    $product_page_url = false;
    if (basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
        $product_page_url = true;
    }

/* header links */
if (tep_session_is_registered('customer_id')) {
    $login_url = tep_href_link(FILENAME_LOGOFF, '', 'SSL');
    $login_text = MENU_TEXT_LOGOUT;

    $create_account_url = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
} else {
    $login_url = tep_href_link(FILENAME_LOGIN, '', 'SSL');
    $login_text = MENU_TEXT_LOGIN;

    $create_account_url = tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL');
}



/* functions for texts */


function et_short_text($text, $limit='10') {
	$str = strlen($text) > $limit ? substr(strip_tags($text), 0, $limit) . '&hellip;' : strip_tags($text);
	return $str;
}

function trimmed_text($text, $limit='20') {
	/* mb_internal_encoding("UTF-8"); */
	$str = strlen($text) > $limit ? mb_substr(strip_tags($text), 0, $limit) . '&hellip;' : strip_tags($text);
	return $str;
}

function detectMobileDevice() {
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');

    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        return true;
    }
    else if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        return true;
    }
    else if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
        return true;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'macintosh') > 0) {
        return false;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'linux') > 0) {
        return false;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
        return false;
    }
    else if (in_array($mobile_ua,$mobile_agents)) {
        return true;
    }
    else {
        return true;
    }
}

$template19_blocks = array(

    'header', 

    'navigation_menu', 

    'slider', 

    'slogan', 

    'paralax',

    

    'footer_brands', 

    'footer_box_01', 

    'footer_box_02', 

    'footer_box_03', 

    'footer_box_04', 

    'footer_higher_1', 

    'footer_higher_2', 

    'footer_higher_3', 

    'footer_higher_4', 

    'newsletter_signup_block', 

    'brands',

    'brands_content' => array( 



        'block_name' => 'brands_content', 



        'type'=>'manufactures', 



        'enabled' => true, 



        'parameters'=>array(),  



    ),

    /*'top_bar', 

    'free_shipping_banner', 

    'footer_box_01', 

    'footer_box_02', 

    'footer_box_03', 

    'footer_box_04', 

    'footer_copyright', 

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

    ),*/ 

    'infobox_07' => array( 

            'block_name' => 'infobox_07', 

            //'type'=>'also_purchased', 

            'type'=>'featured', 

            'enabled'=> true, 

            'parameters'=>array() 

    ),

);

$sts->blocks = array();

foreach($template19_blocks as $block){

    if (is_array($block)){

        $is_enabled = $block['enabled'];

        if (!$is_enabled){

            $sts->template[$block['block_name']] = '';

        } else {

            ob_start();

            switch( strtolower( $block['type'] ) ){

                case 'related':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't19_xsell_products-' . $language . '.cache' . $_GET['products_id'];

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

                        $temp_file = 't19_featured_categories-' . $language . '.cache' . $_GET['products_id'];

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

                case 'manufactures':

                    

                        $mcontent = '';

                        $manufacture_query = tep_db_query("SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, count(p.products_id) as 'total_product' FROM manufacturers m, products p WHERE m.manufacturers_id = p.manufacturers_id and m.manufacturers_status = '1' and`products_status` = '1' group by p.manufacturers_id order by manufacturers_image desc, total_product desc limit 20");

                        while($manufacture = tep_db_fetch_array($manufacture_query)){

                            if(!empty($manufacture['manufacturers_image'])){

                                $mcontent .= '<li><img alt="'.strtoupper($manufacture['manufacturers_name']).'" height="45" src="images/'.$manufacture['manufacturers_image'].'"></li>';

                            }else{

                               // $mcontent .= '<li><div style="margin-top:20px;font-weight:bold;width:180px;height:46px;font-size:18px;vertical-align:middle;" >'.strtoupper($manufacture['manufacturers_name']).'</div></li>';

                                $mcontent .= '<li><div style="text-align:center;color:#8f8f8f;font-weight:bold;font-size:18px;vertical-align:middle;" >'.strtoupper($manufacture['manufacturers_name']).'</div></li>';

                            }



                        }

                        

                        echo $mcontent;

                        $val = ob_get_contents();

                    

                    break;

                case 'featured_manufacturers':

                    $val = '';

                    if (isset($_GET['products_id']) && !empty($_GET['products_id'])){

                        $temp_file = 't19_featured_manufacturers-' . $language . '.cache' . $_GET['products_id'];

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

                    $temp_file = 't19_hot_products-' . $language . '.cache';

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

                        $temp_file = 't19_recommended_products-' . $language . '.cache' . $_GET['products_id'];

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

                    $on_home_page = true;

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

                    $temp_file = 't19_popular_products-' . $language . '.cache';

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
         $val = get_block_content('template19', $block);
         /*
        if (USE_CACHE=='true'){

            $temp_file = $block . '-' . $language . '.cache';

            if (!read_cache($val, $temp_file, PURGE_CACHE_DAYS_LIMIT)){

                $val = get_block_content('template19', $block);

                write_cache($val, $temp_file);

            }

        } else {

            $val = get_block_content('template19', $block);

        }
    */
        $sts->template[$block] = $val;

    }

}

?>