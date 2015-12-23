<?php
/*
  $Id: shopping_cart.php,v 1.73 2003/06/09 23:03:56 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

require("includes/application_top.php");

require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_SHOPPING_CART);

$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_SHOPPING_CART));
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
        <?php
        // Begin Template Check
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
        $check = tep_db_fetch_array($check_query);

        echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/'.$check['configuration_value'].'/stylesheet.css">';
        // End Template Check
        ?>
        <script language="javascript"><!--
            function popupWindow2(url) {
                window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=450,screenX=150,screenY=150,top=150,left=150')
            }
            <?php /* // MVS Shipping Estimator Start ?>
            function estimatorpopupWindow(URL) {
                window.open(URL,'productsshippingestimator','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=600')
            }
            <?php // MVS Shipping Estimator End */ ?>
        //--></script>
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
                <td width="100%" valign="top">
                <?php echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_SHOPPING_CART, 'action=update_product')); ?>
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                                        <td class="pageHeading" align="right">
                                        <?php //echo tep_image(DIR_WS_IMAGES . '', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                        </tr>
                        <?php if ($cart->count_contents() > 0) { ?>
                        <tr>
                            <td>
                        <?php
                                $info_box_contents = array();
                                $info_box_contents[0][] = array(
                                    'params' => 'class="infoBoxHeading"',
                                    'text' => TABLE_HEADING_PRODUCTS);

                                $info_box_contents[0][] = array(
                                    'align' => 'center',
                                    'params' => 'class="infoBoxHeading"',
                                    'text' => TABLE_HEADING_QUANTITY);

                                $info_box_contents[0][] = array(
                                    'align' => 'right',
                                    'params' => 'class="infoBoxHeading"',
                                    'text' => TABLE_HEADING_PRICE);
	
                                $info_box_contents[0][] = array(
                                    'align' => 'center',
                                    'params' => 'class="infoBoxHeading"',
                                    'text' => TABLE_HEADING_REMOVE);

                                $any_out_of_stock = 0;
                                $products = $cart->get_products();
                                for ($i=0, $n=sizeof($products); $i<$n; $i++) {
	
                                    // Push all attributes information in an array
                                    if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
                                        while (list($option, $value) = each($products[$i]['attributes'])) {
                                            echo tep_draw_hidden_field('id[' . $products[$i]['id'] . '][' . $option . ']', $value);
                                            $attributes = tep_db_query("select popt.products_options_name, poval.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_OPTIONS_VALUES . " poval, " . TABLE_PRODUCTS_ATTRIBUTES . " pa where pa.products_id = '" . (int)$products[$i]['id'] . "' and pa.options_id = '" . (int)$option . "' and pa.options_id = popt.products_options_id and pa.options_values_id = '" . (int)$value . "' and pa.options_values_id = poval.products_options_values_id and popt.language_id = '" . (int)$languages_id . "' and poval.language_id = '" . (int)$languages_id . "'");
                                            $attributes_values = tep_db_fetch_array($attributes);

                                            $products[$i][$option]['products_options_name'] = $attributes_values['products_options_name'];
                                            $products[$i][$option]['options_values_id'] = $value;
                                            $products[$i][$option]['products_options_values_name'] = $attributes_values['products_options_values_name'];
                                            $products[$i][$option]['options_values_price'] = $attributes_values['options_values_price'];
                                            $products[$i][$option]['price_prefix'] = $attributes_values['price_prefix'];
                                        }
                                    }
                                }
    
                                // begin Bundled Products
                                if (STOCK_CHECK == 'true') {
                                    $bundle_contents = array();
                                    $bundle_values = array();
                                    $product_ids_in_bundles = array();
                                    $bundle_qty_ordered = array();
                                    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
                                        if ($products[$i]['bundle'] == "yes") {
                                            $tmp = get_all_bundle_products($products[$i]['id']);
                                            $bundle_values[$products[$i]['id']] = $products[$i]['final_price'];
                                            $bundle_contents[$products[$i]['id']] = $tmp;
                                            $bundle_qty_ordered[$products[$i]['id']] = $products[$i]['quantity'];
                                            foreach ($tmp as $id => $qty) {
                                                if (!in_array($id, $product_ids_in_bundles)) $product_ids_in_bundles[] = $id; // save unique ids
                                            }
                                        }
                                    }
                                    if (!empty($bundle_values)) { // if bundles exist in order
                                        arsort($bundle_values); // sort array so bundle ids with highest value come first
                                        $product_on_hand = array();
                                        $bundles_stock_check = array();
                                        foreach ($product_ids_in_bundles as $id) {
                                            // get quantity on hand for every product contained in bundles in this order
                                            $product_on_hand[$id] = tep_get_products_stock($id);
                                        }
                                        foreach ($bundle_values as $bid => $bprice) {
                                            $bundles_available = array();
                                            foreach ($bundle_contents[$bid] as $pid => $qty) {
                                                $bundles_available[] = intval($product_on_hand[$pid] / $qty);
                                            }
                                            $available = min($bundles_available); // max number of this bundle we can make with product on hand
                                            $bundles_stock_check[$bid] = '';
                                            if ($available <= 0) {
                                                $bundles_stock_check[$bid] = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
                                            } elseif ($available < $bundle_qty_ordered[$bid]) {
                                                $bundles_stock_check[$bid] = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK .  '</span>';
                                            }
                                            $deduct = min($available, $bundle_qty_ordered[$bid]); // assume we sell as many of the bundle as possible
                                            foreach ($bundle_contents[$bid] as $pid => $qty) {
                                                // reduce product left on hand by number sold in this bundle before checking next less expensive bundle
                                                // also lets us know how many we have left to sell individually
                                                $product_on_hand[$pid] -= ($deduct * $qty);
                                            }
                                        }
                                    }
                                }
                                $any_bundle_only = false;
                                // end Bundled Products

                                $disclaimer_req = 0;
                                for ($i=0, $n=sizeof($products); $i<$n; $i++) {
                                    /*$temp = '';
                                    $sql = tep_db_query("select disclaimer_needed from " . TABLE_PRODUCTS . " where products_id='" . $products[$i]['id'] ."'");
                                    $sql_info = tep_db_fetch_array($sql);
                                    if ((int)$sql_info['disclaimer_needed']==1){
                                        $disclaimer_req = 1;
                                        $temp = TEXT_DISCLAIMER_ANCHOR ;
                                    }*/
                                    if (($i/2) == floor($i/2)) {
                                        $info_box_contents[] = array('params' => 'class="productListing-even productListing-tr"');
                                    } else {
                                        $info_box_contents[] = array('params' => 'class="productListing-odd  productListing-tr"');
                                    }

                                    $cur_row = sizeof($info_box_contents) - 1;
                                    $feed_status = is_xml_feed_product($products[$i]['id']);
                                    if ($feed_status){
                                        $image = tep_small_image($products[$i]['image'], $products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                                    } else {
                                        $image = tep_image(DIR_WS_IMAGES . $products[$i]['image'], $products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                                    } 
   
                                    /*
                                    $products_name = '<table border="0" cellspacing="2" cellpadding="2">' .
                                                        '  <tr>' .
                                                                '    <td class="productListing-data" align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . tep_small_image($products[$i]['image'], $products[$i]['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a></td>' .
                                                                '    <td class="productListing-data" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">' . $temp . '<b>' . $products[$i]['name'] .  '</b></a>';
                                    */
                                    //mod for parent-child functionality by (MA) BOF
                                    $check_child_product_query = tep_db_query("select parent_products_model from products where products_id = '".$products[$i]['id']."' and parent_products_model IS NOT NULL");
                                    if(tep_db_num_rows( $check_child_product_query )){
                                        $check_child_product = tep_db_fetch_array($check_child_product_query);
                                        $parent_product_query = tep_db_query("select products_id from products where products_model = '".$check_child_product['parent_products_model']."'");
                                        $parent_product = tep_db_fetch_array($parent_product_query);
                                        $products_name = '<table border="0" cellspacing="2" cellpadding="2">' .
                                                            '  <tr>' .
                                                                '    <td class="productListing-data" align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $parent_product['products_id']) . '">' . $image . '</a></td>' .
                                                                    '    <td class="productListing-data" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $parent_product['products_id']) . '"><b>' . $products[$i]['name'] .  '</b></a>';
                                    } else {
                                        //mod for parent-child functionality by (MA) EOF
                                        $products_name = '<table border="0" cellspacing="2" cellpadding="2">' .
                                                            '  <tr>' .
                                                                '    <td class="productListing-data" align="center"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '">' . $image . '</a></td>' .
                                                                    '    <td class="productListing-data" valign="top"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . tep_get_prid($products[$i]['id'])) . '"><b>' . $products[$i]['name'] .  '</b></a>';
      
                                        //mod for parent-child functionality by (MA) BOF    
                                    }
									
                                    //mod for parent-child functionality by (MA) EOF
                                    /*if (STOCK_CHECK == 'true') {
                                        $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
                                        if (tep_not_null($stock_check)) {
                                            $any_out_of_stock = 1;
                                            $products_name .= $stock_check;
                                        }
                                    }*/
      
                                    if (STOCK_CHECK == 'true') {
                                        // begin Bundled Products
                                        if ($products[$i]['bundle'] == "yes") {
                                            $stock_check = $bundles_stock_check[$products[$i]['id']];
                                        } elseif (in_array($products[$i]['id'], $product_ids_in_bundles)) {
                                            // if ordering individually product that is also contained in a bundle in this order must be able to cover both quantities
                                            // check against product left on hand after bundles have been sold
                                            $stock_check = '';
                                            if ($product_on_hand[$products[$i]['id']] <= 0) {
                                                $stock_check = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
                                            } elseif ($product_on_hand[$products[$i]['id']] < $products[$i]['quantity']) {
                                                $stock_check = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '</span>';
                                            }
                                        } else {
                                            $stock_check = tep_check_stock($products[$i]['id'], $products[$i]['quantity']);
                                        }
                                        if (tep_not_null($stock_check)) {
                                            $any_out_of_stock = 1;
                                            $products_name .= $stock_check;
                                        }
                                    }
                                    if ($products[$i]['sold_in_bundle_only'] == 'yes') {
                                        $products_name .= '<br><span class="markProductOutOfStock">' . TEXT_BUNDLE_ONLY . '</span>';
                                        $any_bundle_only = true;
                                    }
                                    // end Bundled Products

                                    if (isset($products[$i]['attributes']) && is_array($products[$i]['attributes'])) {
                                        reset($products[$i]['attributes']);
                                        while (list($option, $value) = each($products[$i]['attributes'])) {
                                            $products_name .= '<br><small><i> - ' . $products[$i][$option]['products_options_name'] . ' ' . $products[$i][$option]['products_options_values_name'] . '</i></small>';
                                        }
                                    }

                                    $products_name .= '    </td>' .
                                                            '  </tr>' .
                                                                '</table>';

                                    $info_box_contents[$cur_row][] = array(
                                        'params' => 'class="productListing-data" style="border-bottom:1px solid #CCCCCC;"',
                                        'text' => $products_name);

                                    $info_box_contents[$cur_row][] = array(
                                        'align' => 'center',
                                        'params' => 'class="productListing-data" valign="top"  style="border-bottom:1px solid #CCCCCC;"',
                                        //BOF:MVS
                                        /*
                                        //EOF:MVS
                                        'text' => tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . 	tep_draw_hidden_field('products_id[]', $products[$i]['id']));
                                        //BOF:MVS
                                        */
                                        //'text' => tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . 	tep_draw_hidden_field( (SELECT_VENDOR_SHIPPING == 'true' ? 'products_' . $products[$i]['vendors_id'] . '[]' : 'products_id[]') , $products[$i]['id']));
                                        'text' => tep_draw_input_field('cart_quantity[]', $products[$i]['quantity'], 'size="4"') . 	tep_draw_hidden_field( (SELECT_VENDOR_SHIPPING == 'true' ? 'products_' . $products[$i]['vendors_id'] . '[]' : 'products_id[]') , $products[$i]['id']) . '<input class="skubutton" type="submit" value="UPDATE" style="display:none;"><span class="remove_cart_product_tpl12" pid="' . $products[$i]['id'] . '"  style="display:none;">Remove</span><span class="add_to_wishlist_tpl12" pid="' . $products[$i]['id'] . '"  style="display:none;">Add to Wishlist</span>');
                                //EOF:MVS

                                    $info_box_contents[$cur_row][] = array(
                                        'align' => 'right',
                                        'params' => 'class="productListing-data" valign="top"  style="border-bottom:1px solid #CCCCCC;"',
                                        'text' => '<b>' . $currencies->display_price($products[$i]['final_price'], tep_get_tax_rate($products[$i]['tax_class_id']), $products[$i]['quantity']) . '</b>');
											 
                                    $info_box_contents[$cur_row][] = array(
                                        'align' => 'center',
                                        'params' => 'class="productListing-data" valign="top"  style=" border-bottom:1px solid #CCCCCC;"',
                                        'text' => tep_draw_checkbox_field('cart_delete[]', $products[$i]['id']));
                                }

                                new productListingBox($info_box_contents); 
                            ?>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                        </tr>
                        <tr id="sub_total">
                            <td align="right" class="main"><b><?php echo SUB_TITLE_SUB_TOTAL; ?> <span><?php echo $currencies->format($cart->show_total()); ?></span></b></td>
                        </tr>
                        <?php
                        if ($any_out_of_stock == 1) {
                            if (STOCK_ALLOW_CHECKOUT == 'true') {
                        ?>
                        <tr>
                            <td class="stockWarning" align="center"><br><?php echo OUT_OF_STOCK_CAN_CHECKOUT; ?></td>
                        </tr>
                        <?php
                            } else {
                        ?>
                        <tr>
                            <td class="stockWarning" align="center"><br><?php echo OUT_OF_STOCK_CANT_CHECKOUT; ?></td>
                        </tr>
                        <?php
                            }
                        }
                        if ($any_bundle_only) {
                        ?>
                        <tr>
                            <td class="stockWarning" align="center"><br><?php echo TEXT_NO_CHECKOUT_BUNDLE_ONLY; ?></td>
                        </tr> 
                        <?php
                        }
                        ?>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                        </tr>
                        <?php /* ?>
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                                
                                <!--
                                <?php  //if ($disclaimer_req){ ?>
                                <tr class="infoBoxContents">
                                    <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td  valign="top" align="right" width="60%">
                                            <input type="checkbox" value="" id="disclaimer">
                                        </td>			
                                        <td  width="40%" valign="top" align="right" nowrap>
                                            <script language="javascript"><!--
                                                document.write('<?php //echo '<a href="javascript:popupWindow2(\\\'' . tep_href_link('disclaimer.html'). '\\\')">' . TEXT_DISCLAIMER_ANCHOR . TEXT_AGREE . '</a>'; ?>');
                                                function disclaimer_onclick(){
                                                    var disclaimer=document.getElementById('disclaimer');
                                                    if (!disclaimer.checked)
                                                        alert('<?php //echo  TEXT_DISCLAIMER_ERROR;?>');
                                                        return disclaimer.checked;
                                                    }
                                                //--></script>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                                <?php
                                //}
                                */
                                ?>
                        <tr class="infoBoxContents">
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                    <tr>
                                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                        <td class="main"><?php echo tep_image_submit('button_update_cart.gif', IMAGE_BUTTON_UPDATE_CART); ?></td>
                                        <?php
                                        $back = sizeof($navigation->path)-2;
                                        if (isset($navigation->path[$back])) {
                                        ?>
                                        <td class="main"><?php echo '<a href="' . tep_href_link($navigation->path[$back]['page'], tep_array_to_string($navigation->path[$back]['get'], array('action')), $navigation->path[$back]['mode']) . '">' . tep_image_button('button_continue_shopping.gif', IMAGE_BUTTON_CONTINUE_SHOPPING) . '</a>'; ?></td>
                                        <?php
                                        }
                                        ?>
                                        <?php
                                        /*
                                        // MVS Shipping Estimator start
                                        if (SHIP_ESTIMATOR_BUTTON_SHOPPING_CART == 'true') {
                                            echo '                <td class="main" align=center><a href="javascript:estimatorpopupWindow(\'' .  tep_href_link (FILENAME_SHIP_ESTIMATOR, 'pid=' . (int) $_GET['products_id'], 'SSL') . '\')">' . tep_image_button ('button_estimate_shipping.gif', IMAGE_BUTTON_SHIP_ESTIMATOR) . '</a></td>';
                                        }*/
                                        // MVS Shipping Estimator end
                                        ?>
                                        <td align="right" class="main" style="float:none !important;"><?php if (!($any_bundle_only || (($any_out_of_stock == 1) && (STOCK_ALLOW_CHECKOUT != 'true')))) echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">' . tep_image_button('button_checkout.gif', IMAGE_BUTTON_CHECKOUT, ($disclaimer_req ? 'onclick="javascript:return disclaimer_onclick();"' :'')) . '</a>'; ?></td>
                                       
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <!--SKU 14FEB FBLOGIN BOF-->
                        <tr>
                            <td>
                                <div id="fb-root"></div>
                                <script>
                                    window.fbAsyncInit = function() {
                                        FB.init({
                                            appId      : '<?php echo FACEBOOK_APP_ID;?>',
                                            status     : true, // check login status
                                            cookie     : true, // enable cookies to allow the server to access the session
                                            xfbml      : true  // parse XFBML
                                        });

                                        FB.Event.subscribe('auth.authResponseChange', function(response) {
                                            if (response.status === 'connected') {
                                                testAPI();
                                            } else if (response.status === 'not_authorized') {
                                                FB.login();
                                            } else {
                                                FB.login();
                                            }
                                        });
                                    };

                                    // Load the SDK asynchronously
                                    (function(d){
                                        var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
                                        if (d.getElementById(id)) {return;}
                                            js = d.createElement('script'); js.id = id; js.async = true;
                                            js.src = "//connect.facebook.net/en_US/all.js";
                                            ref.parentNode.insertBefore(js, ref);
                                        }(document));

                                    function testAPI() {
                                        console.log('Welcome!  Fetching your information.... ');
                                        FB.api('/me', function(response) {
                                            console.log('Good to see you, ' + response.name + '.');
                                            $.ajax({
                                                type: "POST",
                                                url: "fblogin.php",
                                                data: "mode=login&first_name=" + response.first_name + "&last_name=" + response.last_name + "&birthday=" + response.birthday + "&gender=" + response.gender + "&email=" + response.email + "&birthday=" + response.birthday,
                                                success: function(msg) {
                                                    location.href = '<?php echo tep_href_link(FILENAME_DEFAULT) ;?>';
                                                }
                                            });
                                        });
                                    }
                                </script>
                                <fb:login-button show-faces="false" width="200" max-rows="1"></fb:login-button>
                            </td>
                        </tr><!--SKU 14FEB FBLOGIN EOF-->
                    </table>
                </td>
            </tr>
                        <?php
                        } else {
                        ?>
                        <tr>
                            <td align="center" class="main"><?php new infoBox(array(array('text' => TEXT_CART_EMPTY))); ?></td>
                        </tr>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                        </tr>
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                                    <tr class="infoBoxContents">
                                        <td>
                                            <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                                <tr>
                                                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                                    <td align="right" class="main"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                                                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <?php
                        }
                        ?>
                    </table></form>
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