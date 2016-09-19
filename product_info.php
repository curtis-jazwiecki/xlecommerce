<?php
/*
  $Id: product_info.php,v 1.97 2003/07/01 14:34:54 hpdl Exp $
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

require ('includes/application_top.php');

require (DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);

$check_parent_exist_query = tep_db_query("select p.products_model, p.parent_products_model from " . TABLE_PRODUCTS . " p where p.products_status = '1' and p.products_id = '" . (int) $HTTP_GET_VARS['products_id'] . "'");

$check_parent_exist = tep_db_fetch_array($check_parent_exist_query);

if (!empty($check_parent_exist['parent_products_model'])) {

    $get_parent_id_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p where products_model = '" . $check_parent_exist['parent_products_model'] . "'");

    if (tep_db_num_rows($get_parent_id_query)) {

        $get_parent_id = tep_db_fetch_array($get_parent_id_query);

        tep_redirect(tep_href_link('product_info.php', 'products_id=' . $get_parent_id['products_id']));

    }

}

$product_check_query = tep_db_query("select count(*) as total,hide_price from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where (p.parent_products_model = '' or p.parent_products_model is null) and c.categories_status = '1' and p.products_status = '1' and p.products_id = '" . (int) $_GET['products_id'] . "' and pd.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and pd.language_id = '" . (int) $languages_id . "'" . (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS == "true" ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '') . " and p.is_store_item='0' ");

$product_check = tep_db_fetch_array($product_check_query);

// BOF Separate Pricing per Customer

if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {

    $customer_group_id = $_SESSION['sppc_customer_group_id'];

} else {

    $customer_group_id = '0';

}

// EOF Separate Pricing per Customer

function get_stock_message($quantity) {

    $resp = '';

    $quantity = (int) $quantity;

    if ($quantity <= 0)

        $resp = TXT_OUT_OF_STOCK;

    elseif ($quantity <= 10)

        $resp = TXT_LOW_STOCK;

    else

        $resp = TXT_IN_STOCK;

    return $resp;

}

$is_package_out_of_stock = false; // added on 30-12-2015

function display_bundle($bundle_id, $bundle_price) {

    global $languages_id, $product_info, $currencies,$is_package_out_of_stock;

    $return_str = '';

    $return_str .= '<table border="0" width="95%" cellspacing="1" cellpadding="2" class="infoBox"> <tr class="infoBoxContents"> <td> <table border="0" width="100%" cellspacing="0" cellpadding="2"> <tr> <td class="main" colspan="5"><b>';

    $bundle_sum = 0;

    $return_str .= TEXT_PRODUCTS_BY_BUNDLE . "</b></td></tr>\n";

    //$bundle_query = tep_db_query(" SELECT pd.products_name, pb.*, p.products_bundle, p.products_id, p.products_model, p.products_price, p.products_image FROM " . TABLE_PRODUCTS . " p INNER JOIN " . TABLE_PRODUCTS_DESCRIPTION . " pd ON p.products_id=pd.products_id INNER JOIN " . TABLE_PRODUCTS_BUNDLES . " pb ON pb.subproduct_id=pd.products_id WHERE pb.bundle_id = " . (int)$bundle_id . " and language_id = '" . (int)$languages_id . "'");

    $bundle_query = tep_db_query("select pb.*, p.products_bundle, p.products_id, p.products_model, p.products_price, p.products_image, pd.products_name, p.products_quantity from products_bundles pb inner join products p on pb.subproduct_id=p.products_id inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='" . (int) $languages_id . "') where pb.bundle_id='" . (int) $bundle_id . "'");

    while ($bundle_data = tep_db_fetch_array($bundle_query)) {

        $return_str .= "<tr><td class=main valign=top style='padding-top:10px;'>";

        

        if($bundle_data['products_quantity'] < 1){

            

            $is_package_out_of_stock = true;

        }

        $return_str .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $bundle_data['products_id']) . '" target="_blank">' . tep_small_image($bundle_data['products_image'], $bundle_data['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="1" vspace="1"') . '</a></td>';

        // comment out the following line to hide the subproduct qty

        $return_str .= "<td class=main align=right><b>" . $bundle_data['subproduct_qty'] . "&nbsp;x&nbsp;</b></td>";

        $return_str .= '<td class=main><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $bundle_data['products_id']) . '" target="_blank"><b>&nbsp;(' . $bundle_data['products_model'] . ') ' . $bundle_data['products_name'] . '</b></a>';

        if ($bundle_data['products_bundle'] == "yes")

            display_bundle($bundle_data['subproduct_id'], $bundle_data['products_price']);

        $return_str .= '</td>';

        $return_str .= '<td align=right class=main><b>&nbsp;' . $currencies->display_price($bundle_data['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . "</b></td></tr>\n";

        $bundle_sum += $bundle_data['products_price'] * $bundle_data['subproduct_qty'];

    }

    $bundle_saving = $bundle_sum - $bundle_price;

    $bundle_sum = $currencies->display_price($bundle_sum, tep_get_tax_rate($product_info['products_tax_class_id']));

    $bundle_saving = $currencies->display_price($bundle_saving, tep_get_tax_rate($product_info['products_tax_class_id']));

    // comment out the following line to hide the "saving" text

    $return_str .= "<tr><td colspan=5 class=main><p><b>" . TEXT_RATE_COSTS . '&nbsp;' . $bundle_sum . '</b></td></tr><tr><td class=main colspan=5><font color="red"><b>' . TEXT_IT_SAVE . '&nbsp;' . $bundle_saving . "</font></b></td></tr>\n";

    $return_str .= '</table></td> </tr> </table>';

    return $return_str;

}

?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php echo HTML_PARAMS; ?>>

    <head>

    <?php

// BOF: Header Tag Controller v2.6.0

if (file_exists(DIR_WS_INCLUDES . 'header_tags.php')) {

    require (DIR_WS_INCLUDES . 'header_tags.php');

} else {

    ?>

    <title><?php echo TITLE; ?></title>

    <?php

}

// EOF: Header Tag Controller v2.6.0

?>

<script language="javascript"><!--

function popupWindow(url) {

	window.open(url, 'popupWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')

}

function popupWindow2(url) {

	window.open(url, 'popupWindow', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=550,height=450,screenX=150,screenY=150,top=150,left=150')

}

//--></script>

<link rel="stylesheet" href="lightbox/css/lightbox.css" media="screen"/>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>

<script src="lightbox/js/lightbox-2.6.min.js"></script>

<?php

ob_start();

$children_query = tep_db_query("select count(p1.products_model) as child_count from products p1 where p1.parent_products_model='" . $check_parent_exist['products_model'] . "'");

$info = tep_db_fetch_array($children_query);

if ($info['child_count'] > 0) {?>

    <script type="text/javascript">

            function disclaimer_onclick(id) {

                var disclaimer = document.getElementById(id);

                if (!disclaimer.checked)

                    alert('<?php echo TEXT_DISCLAIMER_ERROR; ?>');

                return disclaimer.checked;

            }

            var oPt = jQuery.noConflict();

            oPt(document).ready(function () {

                var product_id = '<?php echo $HTTP_GET_VARS['products_id']; ?>';

                if (oPt('select#attribute').length) {

                    oPt.ajax({

                        url: '<?php echo (($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_SERVER . DIR_WS_HTTP_CATALOG) ?>control_specifications_scope.php',

                        method: 'post',

                        dataType: 'json',

                        data: {product_id: product_id, action: 'set_initial_price'},

                        success: function (response) {

                            if (response[0] != undefined && response[0].product_id != '') {

                                oPt('span.model').html(response[0].product_model);

                                oPt('span.price').html(response[0].product_price);

                                oPt('input:hidden[name="products_id"]').val(response[0].product_id);

                                if (response[0].product_price == '') {

                                    oPt('input.addtocart_btn').attr('disabled', 'disabled');

                                    oPt('input[name="wishlist_x"]').attr('disabled', 'disabled');

                                } else {

                                    if ('<?php echo STOCK_ALLOW_CHECKOUT ?>' == 'false' && parseInt(response[0].product_quantity) <= 0) {

                                        oPt('input.addtocart_btn').attr('disabled', 'disabled');

                                    } else {

                                        oPt('input.addtocart_btn').removeAttr('disabled');

                                    }

                                    oPt('input[name="wishlist_x"]').removeAttr('disabled');

                                }

                                oPt('span#availability_message').html(response[0].product_stock);

                                for (var i = 0; i < response[0].filters.length; i++) {

                                    option_id = response[0].filters[i].option;

                                    option_value_id = response[0].filters[i].value;

                                    elem = oPt('select[id="attribute"][name="id[' + option_id + ']"]');

                                    oPt(elem)

                                            .val(option_value_id)

                                            .find('option:gt(0)').css({'font-weight': 'normal', 'color': 'gray'})

                                            .parent()

                                            .find('option[value="' + option_value_id + '"]').css({'font-weight': 'bolder', 'color': 'black'})

                                            ;

                                }

                            }

                        }

                    });

                }

                oPt('select#attribute').change(function () {

                    var modified_option = oPt(this).attr('name').replace('id[', '').replace(']', '');

                    var all_filters_selected = true;

                    var filters = '';

                    oPt.each(oPt('select#attribute'), function () {

                        if (oPt(this).val() != '0') {

                            option_id = oPt(this).attr('name').replace('id[', '').replace(']', '');

                            filters += (option_id == modified_option ? '*' : '') + option_id + '_' + oPt(this).val() + '|';

                        } else {

                            all_filters_selected = false;

                        }

                    });

                    if (filters != '') {

                        filters = filters.substring(0, filters.length - 1);

                        oPt.ajax({

                            url: '<?php echo (($request_type == 'SSL') ? HTTPS_SERVER . DIR_WS_HTTPS_CATALOG : HTTP_SERVER . DIR_WS_HTTP_CATALOG) ?>control_specifications_scope.php',

                            method: 'post',

                            dataType: 'json',

                            data: {

                                product_id: product_id,

                                filters: filters,

                                all_filters_selected: (all_filters_selected ? '1' : '0')

                            },

                            beforeSend: function () {

                                oPt('img.loader[optionid="' + modified_option + '"]').css('visibility', 'visible');

                            },

                            success: function (response) {

                                oPt('img.loader').css('visibility', 'hidden');

                                oPt('span.model').html(response[0].product_model);

    <?php if ($product_check['hide_price'] != 1) { ?>

                                    oPt('span.price').html(response[0].product_price);

    <?php } ?>

                                if (response[0].upc_ean == 0 || response[0].upc_ean == '') { // added on 19-10-2015

                                    oPt('#txt_upc_ean').html(' -- ');

                                } else {

                                    oPt('#txt_upc_ean').html(response[0].upc_ean);

                                }

                                if (response[0].min_acceptable_price == 0 || response[0].min_acceptable_price == '') { // added on 19-10-2015

                                    oPt('#txt_map').html(' -- '); // added on 19-10-2015

                                } else {

                                    oPt('#txt_map').html(response[0].min_acceptable_price); // added on 19-10-2015

                                }

                                oPt('input:hidden[name="products_id"]').val(response[0].product_id);

                                if (response[0].product_price == '') {

                                    oPt('input.addtocart_btn').attr('disabled', 'disabled');

                                    oPt('input[name="wishlist_x"]').attr('disabled', 'disabled');

                                } else {

                                    if ('<?php echo STOCK_ALLOW_CHECKOUT ?>' == 'false' && parseInt(response[0].product_quantity) <= 0) {

                                        oPt('input.addtocart_btn').attr('disabled', 'disabled');

                                    } else {

                                        oPt('input.addtocart_btn').removeAttr('disabled');

                                    }

                                    oPt('input[name="wishlist_x"]').removeAttr('disabled');

                                }

                                oPt('span#availability_message').html(response[0].product_stock);

								

								// added on 19-04-2016 #start

								oPt('#products_stock_availability_message').html(response[0].display_products_stock_availability);

								// added on 19-04-2016 #ends

                                if (response[0].image != '<?php echo DIR_WS_IMAGES; ?>') {

                                    oPt('#pimage').attr('src', response[0].image);

                                    oPt('#pimage').parent('a').attr("href", response[0].image);

                                    oPt('#image_link').attr("href", response[0].image);

                                    oPt('.cboxElement').attr('href', response[0].image);

                                }

                                oPt('#view3').html('');

								if(response[1]['html'] != ''){

									oPt('#view3').html(response[1]['html']);

								}
                            }

                        });

                    }

                });

            });

        </script>

    <?php } ?>

    <script type="text/javascript">

        var oPt = jQuery.noConflict();

        oPt(document).ready(function () {

            var product_id = '<?php echo $HTTP_GET_VARS['products_id']; ?>';

            if (oPt('select#attribute').length || oPt('select#parent_attribute').length) {

                oPt('form[name="cart_quantity"]').submit(function (event) {

                    var buy = '1';

                    oPt('select[id="attribute"]').each(function () {

                        if (oPt(this).val() == "0") {

                            buy = '0';

                        }

                    });

                    oPt('select[id="parent_attribute"]').each(function () {

                        if (oPt(this).val() == "0") {

                            buy = '0';

                        }

                    });

                    if (buy == '0') {

                        alert('Please select all available options');

                        return false;

                    }

                });

            }

        });

    </script>

    <?php

$js_code = ob_get_contents();

ob_end_clean();

echo $js_code;

$sts->template['js_code'] = $js_code;

?>

</head>

<body style="margin:0;">

<!-- header //-->

<?php require (DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //--> 

<!-- body //-->

<table border="0" width="100%" cellspacing="3" cellpadding="3">

        <tr>

      

      <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">

          

          <!-- left_navigation //-->

          

          <?php require (DIR_WS_INCLUDES . 'column_left.php'); ?>

          

          <!-- left_navigation_eof //-->

          

        </table></td>

      

      <!-- body_text //-->

      

        <td width="100%" valign="top">

      

      <?php

    $child_items_exist = false;

    if ($info['child_count'] > 0) {

        $child_items_exist = true;

    }

    //if (!$child_items_exist) {

    echo tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product'));

    //}

    ?>

      <table border="0" width="100%" cellspacing="0" cellpadding="0">

<?php

$manufacturer_is_active = manufacturer_is_active((int) $_GET['products_id']);

if ($product_check['total'] < 1 || !$manufacturer_is_active) {

    ?>

    <tr>

          <td><?php new infoBox(array(array('text' => TEXT_PRODUCT_NOT_FOUND))); ?></td>

        </tr>

    <tr>

          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

        </tr>

    <tr>

          <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">

              <tr class="infoBoxContents">

              <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

                  <tr>

                  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                  <td align="right"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>

                  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                </tr>

                </table></td>

            </tr>

            </table></td>

        </tr>

    <?php

                            } else {

$product_info_query = tep_db_query("select p.products_largeimage, p.product_image_2, p.product_image_3, p.product_image_4, p.product_image_5, p.product_image_6, p.products_id, pd.products_name, pd.products_description, pd.products_specifications, p.products_model, p.products_quantity,p.store_quantity, p.products_image, p.products_mediumimage,pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id, p.disclaimer_needed, p.hide_price, m.manufacturers_name, p.products_bundle, p.sold_in_bundle_only,p.store_quantity from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m on p.manufacturers_id=m.manufacturers_id , " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_CATEGORIES . " c where c.categories_status = '1' and p.products_status = '1' and p.products_id = '" . (int) $_GET['products_id'] . "' and pd.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and pd.language_id = '" . (int) $languages_id . "'");

$product_info = tep_db_fetch_array($product_info_query);

$product_extended_query = tep_db_query("select upc_ean, min_acceptable_price, brand_name, manufacturer_model_number from products_extended where osc_products_id  = '" . (int) $_GET['products_id'] . "' ");

$product_extended = tep_db_fetch_array($product_extended_query);

$child_product_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p where p.products_status = '1' and p.parent_products_model = '" . $product_info['products_model'] . "'");

tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int) $_GET['products_id'] . "' and language_id = '" . (int) $languages_id . "'");

if (tep_not_null($product_info['products_model'])) {

	$products_name = $product_info['products_name'] . '<br><span class="smallText">' . 'item #' . ' [' . $product_info['products_model'] . ']</span>';

} else {

	$products_name = $product_info['products_name'];

}

$display_products_name = $product_info['products_name'];?>

      <tr>

    

      <td class="main">

    

    <table width="100%">

          <?php // Start Template Area - strip all HTML tags  ?>

          <?php

    // Get selected template

    $selected_template = MODULE_STS_TEMPLATE_FOLDER;

    /* 19-Jan-2015 bof */

    if (mobile_site == 'True' && checkmobile2() == true && $selected_template == 'full/template12') {

        $selected_template = 'full/template6';

    }

    /* 19-Jan-2015 eof */

    if(file_exists("includes/sts_templates/" . $selected_template . "/product_info.php")){
	
		$product_listing_template_0 = file("includes/sts_templates/" . $selected_template . "/product_info.php");

		$text_display = '';
	
		for ($p = 0; sizeof($product_listing_template_0) > $p; $p++) {
	
			$text_display .= $product_listing_template_0[$p];
	
		}
	
	}

    // BELOW CREATES ALL VARIABLES

    if (tep_not_null($product_info['products_image'])) {

        $feed_status = is_xml_feed_product($product_info['products_id']);

        if ($feed_status) {

            //$image = tep_medium_image($product_info['products_mediumimage'], $product_info['products_name'], '', '', 'id="pimage" class="subcatimages"');

            $image = tep_medium_image((tep_not_null($product_info['products_largeimage'])) ? $product_info['products_largeimage'] : ((tep_not_null($product_info['products_mediumimage'])) ? $product_info['products_mediumimage'] : $product_info['products_image']), $product_info['products_name'], '', '', 'id="pimage" class="subcatimages"');

        } else {

            $image = tep_image(DIR_WS_IMAGES . ((tep_not_null($product_info['products_largeimage'])) ? $product_info['products_largeimage'] : ((tep_not_null($product_info['products_mediumimage'])) ? $product_info['products_mediumimage'] : $product_info['products_image'])), $product_info['products_name'], '', '', 'id="pimage" class="subcatimages"');

        }

        $largeImg = ((tep_not_null($product_info['products_largeimage'])) ? $product_info['products_largeimage'] : ((tep_not_null($product_info['products_mediumimage'])) ? $product_info['products_mediumimage'] : $product_info['products_image']));

        if (strpos($largeImg, 'http') === false) {

            $largeImg = DIR_WS_IMAGES . $largeImg;

        }

        // search for one child product image if main image doesn't exists #start

        if (empty($image)) {

            $child_image_query = tep_db_fetch_array(tep_db_query("select products_largeimage,products_mediumimage,products_image from products where parent_products_model='" . $check_parent_exist['products_model'] . "' limit 1"));

            $image = tep_image(DIR_WS_IMAGES . ((tep_not_null($child_image_query['products_largeimage'])) ? $child_image_query['products_largeimage'] : ((tep_not_null($child_image_query['products_mediumimage'])) ? $child_image_query['products_mediumimage'] : $child_image_query['products_image'])), $child_image_query['products_name'], '', '', 'id="pimage" class="subcatimages"');

            $largeImg = DIR_WS_IMAGES . ((tep_not_null($child_image_query['products_largeimage'])) ? $child_image_query['products_largeimage'] : ((tep_not_null($child_image_query['products_mediumimage'])) ? $child_image_query['products_mediumimage'] : $child_image_query['products_image']));

        }

        // search for one child product image if main image doesn't exists #ends

        //  $display_product_image = '<script language="javascript"><!--' . "\n";

        $display_product_image .= '<a data-lightbox="image-1" title="' . $product_info['products_name'] . '" href="' . $largeImg . '" >' . $image . '<br>' .'<div id="HDpic">' . TEXT_CLICK_TO_ENLARGE  .'</div>'. '</a>' . "\n";

        /*    $display_product_image .= '//--></script>' . "\n";

        //  $display_product_image .= '<noscript>' . "\n";

        //$display_product_image .= '<a id="image_link" href="' . tep_href_image_link(DIR_WS_IMAGES . $product_info['products_image']) . '" target="_blank">' . $image . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>' . "\n";

        //    $display_product_image .= '<a id="image_link" href="' . tep_href_image_link(DIR_WS_IMAGES . $largeImg) . '" target="_blank">' . $image . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>' . "\n";

        //    $display_product_image .= '</noscript>' ;*/

        if (!empty($product_info['product_image_2']) || !empty($product_info['product_image_3']) || !empty($product_info['product_image_4']) || !empty($product_info['product_image_5']) || !empty($product_info['product_image_6'])) {

            $display_product_extra_images = '<div id="all_pImages" style="width:100%; float:left;">';

            $display_product_extra_images .= '<div style="width:25%; float:left; display:none;" id="pimage1"><a href="#" onclick="swap_image(\'' . $largeImg . '\',\'1\',\'' . $largeImg . '\');return false;">' . tep_small_image(str_replace("images/", "", $largeImg), $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'id="subpimage-1" class="subcatimages"') . '</a></div>';

            if (!empty($product_info['product_image_2'])) {

                $display_product_extra_images .= '<div style="width:25%; float:left;" id="pimage2"><a href="#" onclick="swap_image(\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_2']) . '\',\'2\',\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_2']) . '\');return false;">' . tep_small_image($product_info['product_image_2'], $product_info['products_name'] . '-2', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'id="subpimage-2" class="subcatimages"') . '</a></div>';

            }

            if (!empty($product_info['product_image_3'])) {

                $display_product_extra_images .= '<div style="width:25%; float:left;" id="pimage3"><a href="#" onclick="swap_image(\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_3']) . '\',\'3\',\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_3']) . '\');return false;">' . tep_small_image($product_info['product_image_3'], $product_info['products_name'] . '-3', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'id="subpimage-3" class="subcatimages"') . '</a></div>';

            }

            if (!empty($product_info['product_image_4'])) {

                $display_product_extra_images .= '<div style="width:25%; float:left;" id="pimage4"><a href="#" onclick="swap_image(\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_4']) . '\',\'4\',\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_4']) . '\');return false;">' . tep_small_image($product_info['product_image_4'], $product_info['products_name'] . '-4', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'id="subpimage-4" class="subcatimages"') . '</a></div>';

            }

            if (!empty($product_info['product_image_5'])) {

                $display_product_extra_images .= '<div style="width:25%; float:left;" id="pimage5"><a href="#" onclick="swap_image(\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_5']) . '\',\'5\',\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_5']) . '\');return false;">' . tep_small_image($product_info['product_image_5'], $product_info['products_name'] . '-5', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, ' id="subpimage-5" class="subcatimages"') . '</a></div>';

            }

            if (!empty($product_info['product_image_6'])) {

                $display_product_extra_images .= '<div style="width:25%; float:left;" id="pimage6"><a href="#" onclick="swap_image(\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_6']) . '\',\'6\',\'' . tep_href_image_link(DIR_WS_IMAGES . $product_info['product_image_6']) . '\');return false;">' . tep_small_image($product_info['product_image_6'], $product_info['products_name'] . '-6', SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, ' id="subpimage-6" class="subcatimages"') . '</a></div>';

            }

            $display_product_extra_images .= '</div>';

            $display_product_extra_images .= '

                                        <script>

     function swap_image(image_url,image_no,large_image){

					

					var currdiv = \'#pimage\'+image_no;

					jQuery(\'#popup\').attr("href", "javascript:popupWindow(\\\'' . tep_href_image_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id'] . '&image=') . '\\\'"+image_no+")");

					jQuery(\'#image_link\').attr("href", image_url);

					

					jQuery(\'.cboxElement\').attr("href", image_url);

					jQuery(\'#pimage\').attr("src",image_url);

					jQuery(\'#all_pImages > div\').show();

					jQuery(currdiv).hide();

				

	 }

	 </script>';

        } else {

            $display_product_extra_images = '';

        }

    }

    $display_package_str = '';

    if ($product_info['products_bundle'] == "yes") {

        $display_package_str = display_bundle($HTTP_GET_VARS['products_id'], $product_info['products_price']);

    }

    if ($product_info['sold_in_bundle_only'] == "yes") {

        $display_package_str .= '<p class="main"><b>' . TEXT_SOLD_IN_BUNDLE . '</b></p><blockquote class="main">';

        //$bquery = tep_db_query('select bundle_id from ' . TABLE_PRODUCTS_BUNDLES . ' where subproduct_id = ' . (int)$HTTP_GET_VARS['products_id']);

        $bquery = tep_db_query('select subproduct_id from ' . TABLE_PRODUCTS_BUNDLES . ' where bundle_id = ' . (int) $HTTP_GET_VARS['products_id']);

        while ($bid = tep_db_fetch_array($bquery)) {

            //$binfo_query = tep_db_query('select p.products_model, pd.products_name from ' . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int)$bid['bundle_id'] . "' and pd.products_id = p.products_id and pd.language_id = " . (int)$languages_id);

            $binfo_query = tep_db_query('select p.products_model, pd.products_name from ' . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_id = '" . (int) $bid['subproduct_id'] . "' and pd.products_id = p.products_id and pd.language_id = " . (int) $languages_id);

            $binfo = tep_db_fetch_array($binfo_query);

            //$display_package_str .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int)$bid['bundle_id']) . '" target="_blank">[' . $binfo['products_model'] . '] ' . $binfo['products_name'] . '</a><br />';

            $display_package_str .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . (int) $bid['subproduct_id']) . '" target="_blank">[' . $binfo['products_model'] . '] ' . $binfo['products_name'] . '</a><br />';

        }

        $display_package_str .= '</blockquote>';

    }

    //<!-- EOF Bundled Products-->

    // BOF Separate Pricing per Customer

    if ($customer_group_id > 0) {

        // only need to check products_groups if customer is not retail

        $scustomer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int) $HTTP_GET_VARS['products_id'] . "' and customers_group_id =  '" . $customer_group_id . "'");

        $scustomer_group_price = tep_db_fetch_array($scustomer_group_price_query);

    }

    // end if ($customer_group_id > 0)

    $new_price = tep_get_products_special_price($product_info['products_id']);

    $price_text = '';

    

    $yousave = '';

    // added on 14-12-2015 #start

      $productprice = $currencies->display_price($product_info['products_price'],tep_get_tax_rate($product_info['products_tax_class_id'])); // product retail price

      

      if(isset($new_price)&& !empty($new_price)){

        

        $specialprice = $new_price; // holds special price

        

        $yousave = $currencies->display_price(($product_info['products_price']-$new_price), tep_get_tax_rate($product_info['products_tax_class_id']));

        

      }else{

        

        $specialprice = ''; 

        

      }

      

      $customerprice = '';

      

      if ($customer_group_id > 0 && isset($scustomer_group_price['customers_group_price']) && !empty($scustomer_group_price['customers_group_price'])) {

        

        $customerprice = $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id'])); // holds customer group price

        

        $price_text = 'Our Price:' . $currencies->display_price($product_info['products_price'], '0') . '<br/>';

        

        if($specialprice > 0){

            

            $price_text .= 'Your Price:<s>' . $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';

            

            $checkoutprice = $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));

            $products_price_points = tep_display_points($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));

        

        }else{

            

            $price_text .= 'Your Price:' . $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

            

            $checkoutprice = $currencies->display_price($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

            

            $products_price_points = tep_display_points($scustomer_group_price['customers_group_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

            

        }

      

      }else{

        

        if($specialprice > 0){

            

            $price_text = 'Your Price:<s>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';

            

            $checkoutprice = $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));

            $products_price_points = tep_display_points($new_price, tep_get_tax_rate($product_info['products_tax_class_id']));

        

        }else{

        

            $price_text = 'Your Price:' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

            

            $checkoutprice = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

            $products_price_points = tep_display_points($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));

        

        }

      

      }

      

      

      

	  $msrp_price = $product_extended['unit_msrp'];

      
   if (!empty($product_extended['manufacturer_model_number']) && $product_extended['manufacturer_model_number'] != NULL) {
      $sts->template['manufacturer_model_number'] = $product_extended['manufacturer_model_number'];
   } else 
      $sts->template['manufacturer_model_number'] = 'not available';
   
   
      $sts->template['productprice']  = $productprice;

      $sts->template['customerprice'] = $customerprice;

      $sts->template['specialprice']  = $specialprice;

      $sts->template['yousave']       = $yousave;

      $sts->template['checkoutprice'] = $checkoutprice;

             

      if($msrp_price>0){

        $sts->template['msrp_price'] = '<tr><td width="38%;">MSRP </td><td>'. $msrp_price . '</td></tr>';

        if($productprice < $msrp_price){

            $you_save = $msrp_price - $productprice;

            $sts->template['you_save'] = '<tr><td width="38%;">You Save </td><td>'. $you_save . '</td></tr>';       

        }else{

            $sts->template['you_save'] ="";

        }

      }else{

        $sts->template['msrp_price']="";

        $sts->template['you_save']="";

      }

      

    // added on 14-12-2015 #ends

    

    // Points/Rewards system V2.1rc2a BOF

    if ((USE_POINTS_SYSTEM == 'true') && (DISPLAY_POINTS_INFO == 'true')) {

        $point_text = '';

        $products_points = tep_calc_products_price_points($products_price_points);

        $products_points_value = tep_calc_price_pvalue($products_points);

        if ((USE_POINTS_FOR_SPECIALS == 'true') || $new_price == false) {

            $point_text = '<br>' . sprintf(TEXT_PRODUCT_POINTS, number_format($products_points, POINTS_DECIMAL_PLACES), $currencies->format($products_points_value)) . '';

        }

    }

    //$display_products_specifications = stripslashes($product_info['products_specifications']);

    // display product specification from specification table #start

    $get_specification_query = tep_db_query("select psn.name,psv.value from product_specification_names as psn left join product_specification_values as psv on (psn.id=psv.specification_name_id) left join product_specifications as ps on (psv.id = ps.specification_id) where ps.products_id = '" . (int) $HTTP_GET_VARS['products_id'] . "' order by psn.name ASC");

    if (tep_db_num_rows($get_specification_query)) {

        $display_products_specifications = '<ul>';

        while ($spec_result = tep_db_fetch_array($get_specification_query)) {

            $display_products_specifications .= '<li><b>' . $spec_result['name'] . ' : </b>' . $spec_result['value'] . '</li>';

        }

        $display_products_specifications .= '</ul>';

    }

    // display product specification from specification table #ends

    $display_products_specifications .= '<table>';

    $display_products_specifications .= (!empty($product_extended['min_acceptable_price']) && $product_extended['min_acceptable_price'] > '0' ? '<tr><td><b>MAP:</b> </td><td id="txt_map">' . $product_extended['min_acceptable_price'] . '</td></tr>' : '');

    $display_products_specifications .= (!empty($product_extended['upc_ean']) ? '<tr><td><b>UPC Number:</b> </td><td id="txt_upc_ean">' . $product_extended['upc_ean'] . '</td></tr>' : '');

    $display_products_specifications .= (!empty($product_extended['brand_name']) ? '<tr><td>Manufacturer Part Num: </td><td>' . $product_extended['brand_name'] . '</td></tr>' : '');

    $display_products_specifications .= '</table>';

    // Show in stock/out of stock status - OBN

    if (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'false') {

        $display_products_stock = ($product_info['products_quantity'] > 0) ? TXT_IN_STOCK : STORE_STOCK_OUT_OF_STOCK_MESSAGE;

    } elseif (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'true') {

        if ($product_info['products_quantity'] <=

                STORE_STOCK_LOW_INVENTORY_QUANTITY && $product_info['products_quantity'] > 0)

            $display_products_stock = STORE_STOCK_LOW_INVENTORY_MESSAGE;

        elseif ($product_info['products_quantity'] > STORE_STOCK_LOW_INVENTORY_QUANTITY)

            $display_products_stock = TXT_IN_STOCK;

        else

            $display_products_stock = STORE_STOCK_OUT_OF_STOCK_MESSAGE;

    }

    

    

    if($is_package_out_of_stock){

        

        $display_products_stock = STORE_STOCK_OUT_OF_STOCK_MESSAGE;

    }

	

	

	

	 // code added on 19-04-2016 #start

	 $display_products_stock_availability = '';

	 if ($product_info['products_bundle'] != "yes") {

	 	// re-calculate stock

		$total_quantity = $product_info['products_quantity'] + $product_info['store_quantity'];

		recalculate_stock_status($display_products_stock,$total_quantity); 

	 }

	 

	 if($product_info['store_quantity'] > 0){

		$display_products_stock_availability = '<span id="products_stock_availability_message">'.TXT_IN_STORE_AVAILABILITY.' <b>'.TXT_IN_STOCK.'</b></span>';

	 }else{

		$display_products_stock_availability = '<span id="products_stock_availability_message">'.TXT_IN_STORE_AVAILABILITY.' <b>'.STORE_STOCK_OUT_OF_STOCK_MESSAGE.'</b></span>';

	 }

	 // code added on 19-04-2016 #ends

	 

    if ($product_info['hide_price'] == 1) {

        $display_products_price = "<div style='margin: 10px;'>Add to cart to see price</div>";

    } else {

        $display_products_price = $products_price;

    }

    

    

    

    

    

    

    

    

    

    

    //$products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int)$HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int)$languages_id . "' and popt.is_xml_feed_option='0'");

    $write_cache = false;

    $display_products_attributes = '';

    $temp_file_name = 'product_attributes-' . $language . '.cache' . $_GET['products_id'];

    if (USE_CACHE == 'true') {

        if (!read_cache($display_products_attributes, $temp_file_name, PURGE_CACHE_DAYS_LIMIT)) {

            $write_cache = true;

        }

    }

    //if($write_cache){

    $child_prod_ids = '';

    $child_attributes_exist = false;

    $child_product_query = tep_db_query("select p.products_id from " . TABLE_PRODUCTS . " p where p.products_status = '1' and p.parent_products_model = '" . $product_info['products_model'] . "'");

    while ($child_prod_id = tep_db_fetch_array($child_product_query)) {

        $child_prod_ids .= $child_prod_id['products_id'] . ',';

    }

    $child_prod_ids = rtrim($child_prod_ids, ',');

    if (!empty($child_prod_ids)) {

        $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_ATTRIBUTES . "  where products_id in (" . $child_prod_ids . ") and options_id not in (select distinct options_id from  " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . $_GET['products_id'] . "')");

        $products_attributes = tep_db_fetch_array($products_attributes_query);

        if ($products_attributes['total'] > 0) {

            $child_attributes_exist = true;

            ;

        }

    }

    $parent_options = array();

    if ($child_attributes_exist) {

        $check_parent_option_query = tep_db_query("select distinct options_id from " . TABLE_PRODUCTS_ATTRIBUTES . " where products_id='" . (int) $_GET['products_id'] . "'");

        while ($parent_attributes = tep_db_fetch_array($check_parent_option_query)) {

            $parent_options[] = $parent_attributes['options_id'];

        }

    }

    if (!empty($child_prod_ids)) {

        $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id in (" . $child_prod_ids . ") and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "'");

    } else {

        $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int) $HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "'");

    }

    $products_attributes = tep_db_fetch_array($products_attributes_query);

    if ($products_attributes['total'] > 0) {

        if (!empty($child_prod_ids)) {

            $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id in (" . $child_prod_ids . ") and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' order by patrib.products_options_sort_order, popt.products_options_name");

        } else {

            $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int) $HTTP_GET_VARS['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' order by patrib.products_options_sort_order, popt.products_options_name");

        }

        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {

            $products_options_array = array();

            //$products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and pa.options_id = '" . (int)$products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int)$languages_id . "' order by pa.products_options_sort_order");

            if (!empty($child_prod_ids)) {

                $products_options_query = tep_db_query("select distinct pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where (pa.products_id in (" . $child_prod_ids . ") or pa.products_id in ( select p1.products_id from products p1, products p2 where p1.parent_products_model=p2.products_model and p2.products_id='" . (int) $HTTP_GET_VARS['products_id'] . "' ) ) and pa.options_id = '" . (int) $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int) $languages_id . "' order by pa.products_options_sort_order, pov.products_options_values_name");

            } else {

                $products_options_query = tep_db_query("select distinct pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where (pa.products_id = '" . (int) $HTTP_GET_VARS['products_id'] . "' or pa.products_id in ( select p1.products_id from products p1, products p2 where p1.parent_products_model=p2.products_model and p2.products_id='" . (int) $HTTP_GET_VARS['products_id'] . "' ) ) and pa.options_id = '" . (int) $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int) $languages_id . "' order by pa.products_options_sort_order, pov.products_options_values_name");

            }

            while ($products_options = tep_db_fetch_array($products_options_query)) {

                $products_options_array[] = array(

                    'id' => $products_options['products_options_values_id'],

                    'text' => $products_options['products_options_values_name']);

                if ($products_options['options_values_price'] != '0') {

                    $products_options_array[sizeof($products_options_array) - 1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . ') ';

                }

            }

            if (isset($cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']])) {

                $selected_attribute = $cart->contents[$HTTP_GET_VARS['products_id']]['attributes'][$products_options_name['products_options_id']];

            } else {

                $selected_attribute = false;

            }

            //$display_products_attribute .= tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', array_merge(array(array('id' => '0', 'text' => $products_options_name['products_options_name'])), $products_options_array), $selected_attribute);

            if (in_array($products_options_name['products_options_id'], $parent_options)) {

                $id = 'parent_attribute';

            } else {

                $id = "attribute";

            }

            $display_products_attribute .= '<div style="margin-bottom:12px;">' . tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', array_merge(array(array('id' => '0', 'text' => 'Select ' . $products_options_name['products_options_name'])), $products_options_array), $selected_attribute, 'id="' . $id . '"') . tep_image(DIR_WS_IMAGES . 'ajax_loader_small.gif', '', '', '', 'class="loader" optionid="' . $products_options_name['products_options_id'] . '" style="visibility:hidden;"') . "</div>";

        }

    }

    $display_products_attributes = $display_products_attribute;

    //child products listing

    $display_child_products = '';

    $temp_file_name_child_products = 'child_products-' . $language . '.cache' . $_GET['products_id'];

    $write_cache = true;

    if (USE_CACHE == 'true') {

        if (!read_cache($display_child_products, $temp_file_name_child_products, PURGE_CACHE_DAYS_LIMIT)) {

            $write_cache = true;

        } else {

            $write_cache = false;

        }

    }

    if ($write_cache) {

        //Display child products listing only if no attributes are associated with child products excluding parent product

        if (!$child_attributes_exist) {

            $child_products_count = tep_db_num_rows($child_product_query);

            if (tep_db_num_rows($child_product_query) > 0) {

                $child_product_array = array();

                $count = 0;

                $product_attr = 'no';

                $set_disclaimer = 'no';

                $child_product_info_query = tep_db_query("select p.products_id, pd.products_name, pd.products_description, pd.products_specifications, p.products_model, p.products_quantity,p.store_quantity, p.products_image, p.products_mediumimage, p.products_price, p.products_tax_class_id, p.disclaimer_needed from " . TABLE_PRODUCTS . " p , " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' ".(STOCK_HIDE_OUT_OF_STOCK_PRODUCTS == "true" ? " and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."')" : '')." and p.parent_products_model = '" . $product_info['products_model'] . "' and pd.products_id = p.products_id and  pd.language_id = '" . (int) $languages_id . "'");

                while ($child_product_info = tep_db_fetch_array($child_product_info_query)) {

                    $count++;

                    if (tep_not_null($child_product_info['products_image'])) {

                        //$feed_status = is_xml_feed_product($child_product_info['products_id']);

                        if ($feed_status) {

                            $image = tep_small_image($child_product_info['products_image'], $child_product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="subcatimages"');

                        } else {

                            $image = tep_image(DIR_WS_IMAGES . ((tep_not_null($child_product_info['products_mediumimage'])) ? $child_product_info['products_mediumimage'] : $child_product_info['products_image']), $child_product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="subcatimages"');

                        }

                        $display_child_product_image = '<script language="javascript"><!--' . "\n";

                        $display_child_product_image .= "document.write('" . '<a href="javascript:popupWindow(\\\'' . tep_href_image_link(FILENAME_POPUP_IMAGE, 'pID=' . $child_product_info['products_id']) . '\\\')">' . $image . '<br>' .'<div id="HDpic">' . TEXT_CLICK_TO_ENLARGE  .'</div>'. '</a>' . "');" . "\n";

                        $display_child_product_image .= '//--></script>' . "\n";

                        $display_child_product_image .= '<noscript>' . "\n";

                        $display_child_product_image .= '<a href="' . tep_href_image_link(DIR_WS_IMAGES . $child_product_info['products_image']) . '" target="_blank">' . $image . '<br>' .'<div id="HDpic">' . TEXT_CLICK_TO_ENLARGE  .'</div>'. '</a>' . "\n";

                        $display_child_product_image .= '</noscript>' . "\n" . '</script>';

                    }

                    $child_product_array['image'][$count] = $display_child_product_image;

                    $child_product_array['name'][$count] = $child_product_info['products_name'];

                    $display_child_products_attribute = '';

                    $products_attributes_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int) $child_product_info['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "'");

                    $products_attributes = tep_db_fetch_array($products_attributes_query);

                    if ($products_attributes['total'] > 0) {

                        $products_options_name_query = tep_db_query("select distinct popt.products_options_id, popt.products_options_name from " . TABLE_PRODUCTS_OPTIONS . " popt, " . TABLE_PRODUCTS_ATTRIBUTES . " patrib where patrib.products_id='" . (int) $child_product_info['products_id'] . "' and patrib.options_id = popt.products_options_id and popt.language_id = '" . (int) $languages_id . "' order by patrib.products_options_sort_order");

                        while ($products_options_name = tep_db_fetch_array($products_options_name_query)) {

                            $products_options_array = array();

                            $products_options_array[] = array('id' => '0', 'text' => $products_options_name['products_options_name']);

                            $products_options_query = tep_db_query("select pov.products_options_values_id, pov.products_options_values_name, pa.options_values_price, pa.price_prefix from " . TABLE_PRODUCTS_ATTRIBUTES . " pa, " . TABLE_PRODUCTS_OPTIONS_VALUES . " pov where pa.products_id = '" . (int) $child_product_info['products_id'] . "' and pa.options_id = '" . (int) $products_options_name['products_options_id'] . "' and pa.options_values_id = pov.products_options_values_id and pov.language_id = '" . (int) $languages_id . "' order by pa.products_options_sort_order");

                            while ($products_options = tep_db_fetch_array($products_options_query)) {

                                $products_options_array[] = array(

                                    'id' => $products_options['products_options_values_id'],

                                    'text' => $products_options['products_options_values_name']);

                                if ($products_options['options_values_price'] != '0') {

                                    $products_options_array[sizeof($products_options_array) - 1]['text'] .= ' (' . $products_options['price_prefix'] . $currencies->display_price($products_options['options_values_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . ') ';

                                }

                            }

                            if (isset($cart->contents[$child_product_info['products_id']]['attributes'][$products_options_name['products_options_id']])) {

                                $selected_attribute = $cart->contents[$child_product_info['products_id']]['attributes'][$products_options_name['products_options_id']];

                            } else {

                                $selected_attribute = false;

                            }

                            $product_attr = 'yes';

                            $display_child_products_attribute .= tep_draw_pull_down_menu('id[' . $products_options_name['products_options_id'] . ']', $products_options_array, $selected_attribute) . '<br/>';

                        }

                    }

                    $child_product_array['attribute'][$count] = $display_child_products_attribute;

        if ($new_price = tep_get_products_special_price($child_product_info['products_id'])) {

                        $products_price = '<s>' . $currencies->display_price($child_product_info['products_price'], tep_get_tax_rate($child_product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($child_product_info['products_tax_class_id'])) . '</span>';

                        $products_price_points = tep_display_points($new_price, tep_get_tax_rate($child_product_info['products_tax_class_id']));

                    } else {

                        //Controls Product Price Display

                        $products_price = $currencies->display_price($child_product_info['products_price'], tep_get_tax_rate($child_product_info['products_tax_class_id']));

                        $products_price_points = tep_display_points($child_product_info['products_price'], tep_get_tax_rate($child_product_info['products_tax_class_id']));

                    }

                    // Points/Rewards system V2.1rc2a BOF

                    if ((USE_POINTS_SYSTEM == 'true') && (DISPLAY_POINTS_INFO == 'true')) {

                        $display_child_prod_points = '';

                        $products_points = tep_calc_products_price_points($products_price_points);

                        $products_points_value = tep_calc_price_pvalue($products_points);

                        if ((USE_POINTS_FOR_SPECIALS == 'true') || $new_price == false) {

                            $display_child_prod_points = '' . sprintf(TEXT_PRODUCT_POINTS, number_format($products_points, POINTS_DECIMAL_PLACES), $currencies->format($products_points_value)) . '';

                        }

                    }

                    // Points/Rewards system V2.1rc2a EOF

                    if ($child_product_info['hide_price'] == 1) {

                        $display_child_products_price = "<div style='margin: 10px;'>Add to cart to see price</div>";

                    } else {

                        $display_child_products_price = $products_price;

                    }

                    $child_product_array['points'][$count] = $display_child_prod_points;

                    $child_product_array['price'][$count] = $display_child_products_price;

                    $child_product_array['qty'][$count] = tep_draw_input_field('quantity', '1', 'size="2"');

                    $child_product_array['total_quantity'][$count] = $child_product_info['products_quantity'];

                    $display_child_products_disclaimer = '';

                    if ($child_product_info['disclaimer_needed'] != 0) {

                        $set_disclaimer = 'yes';

                        $display_child_products_disclaimer = '<input type="checkbox"  value="" id="disclaimer[' . $child_product['products_id'] . ']">';

                    }

                    $child_product_array['disclaimer'][$count] = $display_child_products_disclaimer;

                    $child_product_array['model'][$count] = $child_product_info['products_model'];

                    $child_product_array['addToCart'][$count] = tep_draw_hidden_field('products_id', $child_product_info['products_id']) . tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, ($child_product_info['disclaimer_needed'] == '1' ? 'value = "' . $child_product_info['products_id'] . '" onclick="javascript:return disclaimer_onclick(\'disclaimer[' . $child_product['products_id'] . ']\');"' : 'value = "' . $child_product_info['products_id'] . '" style="display:inline"'));

                }

                $display_child_products .= '<table width="100%" cellspacing="0" cellpadding="0" border ="0">';

                $display_child_products .= '<tr><td class="childHeading">Select Your Product</td></tr><tr><td>&nbsp;</td></tr>';

                for ($i = 1; $i <= sizeof($child_product_array['name']); $i++) {

                    $display_child_products .= '<tr><td class="childTitle">' . $child_product_array['name'][$i] . ' (' . get_stock_message($child_product_array['total_quantity'][$i]) . ')</td></tr><tr height="1px"><td></td></tr>';

                    $display_child_products .= '<tr><td>' . tep_draw_form('cart_quantity', tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params(array('action')) . 'action=add_product')) . '<table width = "100%" class="childAttributes"><tr><td>' . $child_product_array['image'][$i] . '</td><td><table>';

                    $display_child_products .= '<tr><td>Model: ' . $child_product_array['model'][$i] . '</td></tr> <tr><td>Price: ' . $child_product_array['price'][$i] . '</td></tr><tr><td>Qty: ' . $child_product_array['qty'][$i] . '</td></tr>';

                    if ((USE_POINTS_SYSTEM == 'true') && (DISPLAY_POINTS_INFO == 'true')) {

                        $display_child_products .= '<tr><td>' . $child_product_array['points'][$i] . '</td></tr>';

                    }

                    if ($set_disclaimer == 'yes') {

                        $display_child_products .= '<tr><td>' . $child_product_array['disclaimer'][$i] . '</td></tr>';

                    }

                    if ($product_attr == 'yes') {

                        $display_child_products .= '<tr><td>' . $child_product_array['attribute'][$i] .

                                '</td></tr>';

                    }

                    $display_child_products .= '<tr><td class="childButton">' . $child_product_array['addToCart'][$i] . '</td></tr></table></form></td></td></tr></table><br /></td></tr>';

                }

                $display_child_products .= '</table>';

            }

        }

        if (USE_CACHE == 'true' && $write_cache) {

            write_cache($display_child_products, $temp_file_name_child_products);

        }

    }

    $display_products_quantity = '<b>' . 'Quantity ' . '</b>' . tep_draw_input_field('cart_quantity', '1', 'size="2"');

    if ($product_info['disclaimer_needed'] != 0) {

        $display_products_disclaimer = '<input type="checkbox"  value="" id="disclaimer">';

       $display_products_disclaimer .= '<script language="javascript"><!--' . "\n" . "document.write('<a href=\"javascript:popupWindow2(\\'" . tep_href_link('termsNconditions.php') . '\\\')">' . TEXT_AGREE . '</a>\');';

        $display_products_disclaimer .= "function disclaimer_onclick()

                          {

                          var disclaimer=document.getElementById('disclaimer');

                          if (!disclaimer.checked)

                          alert('" . TEXT_DISCLAIMER_ERROR . "');

                          return disclaimer.checked;

                           }//--></script>";

    } else

        $display_products_disclaimer = '';

    if ($product_info['sold_in_bundle_only'] == "yes") {

        $button_cart = TEXT_BUNDLE_ONLY;

    } else {

        $button_cart = tep_image_submit('button_in_cart.gif', IMAGE_BUTTON_IN_CART, ($product_info['disclaimer_needed'] == '1' ? 'onclick="javascript:return disclaimer_onclick();"' : ''));

    }

    $display_products_add_to_cart = tep_draw_hidden_field('products_id', $product_info['products_id']) . $button_cart;

    //$display_product_add_to_wishlist = '<input type="image" src="includes/languages/english/images/buttons/" border="0" alt="Add to Wishlist" title=" Add to Wishlist " name="wishlist" value="wishlist">';                                

    $display_product_add_to_wishlist = tep_image_submit('wishlist.gif', 'Add to Wishlist', 'border="0" alt="Add to Wishlist" title=" Add to Wishlist " name="wishlist" value="wishlist"');

    //}

    $display_products_manufacturer = '';

    if (!empty($product_info['manufacturers_name'])) {

        $display_products_manufacturer = 'Manufacturer:' . '&nbsp;' . ucwords(strtolower($product_info['manufacturers_name']));

    }

    //$display_product_model = 'Item# <span class="model">---</span>';

    $display_product_model = 'Item# <span class="model">' . (!$child_items_exist ? $product_info['products_model'] : '---') . '</span>';

    //$display_child_product_price = '<span class="price">0.00</span>';

    if ($product_info['hide_price'] == 1 && !$child_items_exist) {

        $display_child_product_price = "<div style='margin: 10px;'>Add to cart to see price</div>";

    } else {

        $display_child_product_price = '<span class="price">' . (!$child_items_exist ? $checkoutprice : '') . '</span>';

    }

    /* if (tep_not_null($product_info['products_model'])) {

      $display_products_manufacturer = 'Manufacturer:' . '&nbsp;' . ucwords(strtolower($product_info['manufacturers_name'])) . '<br>';

      $display_products_manufacturer .= 'Item#:' . '&nbsp;' . $product_info['products_model'];

      } else {

      $display_products_manufacturer .= '';

      } */

    $display_products_description = str_replace('?', '\'', utf8_decode(stripslashes($product_info['products_description'])));

    $sql = tep_db_query("select sum(reviews_rating)/count(*) as rating from " . TABLE_REVIEWS . " where products_id='" . (int) $HTTP_GET_VARS['products_id'] . "'");

    $sql_info = tep_db_fetch_array($sql);

    $rating = ceil($sql_info['rating']);

    if (!$rating) {

        $rating = 5;

        $rating_text = 'Rate This Item';

    }

    $display_products_ratings = '<img src="images/stars_' . $rating . '.gif">&nbsp;' . (($rating_text != '') ? $rating_text : number_format($rating, 1) . ' out of 5');

    $sql = tep_db_query("select count(reviews_id) as count from " . TABLE_REVIEWS . " where products_id='" . (int) $HTTP_GET_VARS['products_id'] . "'");

    $sql_info = tep_db_fetch_array($sql);

    $count = (int) $sql_info['count'];

    $display_products_ratings_write = '<script language="javascript"><!--' . "\n" .

            "document.write('<a href=\"javascript:popupWindow2(\\'" . tep_href_link('product_reviews_popup.php', 'products_id=' . $HTTP_GET_VARS['products_id']) . '\\\')" style="color:#4b773c;">Read Reviews (' .

            $count . ')</a>\');' . '//--></script>';

    $display_products_ratings_write .= '<noscript>' . '<a href="' . tep_href_link('product_reviews_popup.php', 'products_id=' . $HTTP_GET_VARS['products_id']) .

            '" target="_blank" style="color:#4b773c;">Read Reviews (' . $count . ')</a>' .

            '</noscript>';

    $display_products_ratings_write .= '&nbsp;&nbsp;<b>|</b>&nbsp;&nbsp;' .

            '<a href="' . tep_href_link('product_reviews_write.php', 'products_id=' . $HTTP_GET_VARS['products_id']) .

            '" style="color:#4b773c;">Write a Review</a>';

    /* Modified for Related Products: dt:24July2008

      if ((USE_CACHE == 'true') && empty($SID)) {

      echo tep_cache_also_purchased(3600);

      } else {

      include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);

      }

      }

     */

    /*

      if ( (USE_CACHE == 'true') && !SID)

      {

      echo tep_cache_also_purchased(3600);

      include(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);

      }

      else

      {

      include(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);

      //include(DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);

      }

     */

}

//  $xsell_display_arr = file(DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);

//  $xsell_display = '';

//  for($z=0;sizeof($xsell_display_arr) > $z; $z++)

//  {

//    $xsell_display .= "$xsell_display_arr[$z]";

//  }

//  $display_product_options_title = TEXT_PRODUCT_OPTIONS;

if (DISPLAY_SOCIAL_MEDIA_BUTTONS == 'true') {

    //$display_product_share_link = '<div> <span class="shareText">Share this product:</span><br /><a href="' . tep_href_link('tell_a_friend.php', 'products_id=' . $product_info['products_id']) . '"><img src="images/shareicons/shareIcon_email.gif" alt="E-mail" title="Email this product" border="0"></a>&nbsp;  <a target="_blank" href="http://www.facebook.com/sharer.php?u=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;t=' . $product_info['products_name'] . '"><img  class="shareIcon" src="images/shareicons/shareIcon_Facebook.gif" alt="Facebook" title="Facebook" border="0"></a>&nbsp;  <a target="_blank" href="http://del.icio.us/post?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;notes="><img class="shareIcon" src="images/shareicons/shareIcon_Delicious.gif" alt="Del.icio.us" title="Del.icio.us" border="0"></a>&nbsp;  <a target="_blank" href="http://digg.com/submit/?phase=2&amp;url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;bodytext=""><img class="shareIcon" src="images/shareicons/shareIcon_Digg.gif" alt="Digg" title="Digg" border="0"></a>&nbsp;  <a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;summary="><img class="shareIcon" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"><img class="shareIcon" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"></a>&nbsp;  <a target="_blank" href="http://twitthis.com/twit?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '"><img class="shareIcon" src="images/shareicons/shareIcon_Twitter.gif" alt="Twitter" title="Twitter" border="0"></a> ';

    

  /*  $display_product_share_link = '<div> <span class="shareText">Share this product:</span><br /><a href="' . tep_href_link('tell_a_friend.php', 'products_id=' . $product_info['products_id']) . '"><img src="images/shareicons/shareIcon_email.gif" alt="E-mail" title="Email this product" border="0"></a>&nbsp;  <a target="_blank" href="http://www.facebook.com/sharer.php?u=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;t=' . $product_info['products_name'] . '"><img  class="shareIcon" src="images/shareicons/shareIcon_Facebook.gif" alt="Facebook" title="Facebook" border="0"></a>&nbsp;  <a target="_blank" href="http://del.icio.us/post?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;notes="><img class="shareIcon" src="images/shareicons/shareIcon_Delicious.gif" alt="Del.icio.us" title="Del.icio.us" border="0"></a>&nbsp; <a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;summary="><img class="shareIcon" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"><img class="shareIcon" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"></a>&nbsp;  <a target="_blank" href="http://twitthis.com/twit?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '"><img class="shareIcon" src="images/shareicons/shareIcon_Twitter.gif" alt="Twitter" title="Twitter" border="0"></a> ';

	

	

	// pin interest code #start

	$display_product_share_link .= '<span class="pinterest"><a title="'.$product_info['products_name'].'" href="http://pinterest.com/pin/create/button/?url='.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id'], 'NONSSL').'&media=' . $largeImg.'"></a> <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></span>';

	// pin interest code #ends

	

	// google plus share icon #start

	$display_product_share_link .= '<span class="google_plus"><div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id'], 'NONSSL').'"></div> <script type="text/javascript">

  (function() {

    var po = document.createElement("script"); 

	po.type = "text/javascript"; 

	po.async = true;

    po.src = "https://apis.google.com/js/platform.js";

    var s = document.getElementsByTagName("script")[0]; 

	s.parentNode.insertBefore(po, s);

  })();

</script> </div></span>';

	// google plus share icon #ends

	

$display_product_share_link.="<style type='text/css'> .pinterest > span{background:url('images/shareicons/shareIcon_Pinterest.gif') no-repeat;border-shadow:none;width:24px; height:22px;} .google_plus > #___plus_0 {opacity:0;} .google_plus{background:url('images/shareicons/shareIcon_Googleplus.gif') no-repeat; width:24px;}  </style>";*/

//$display_product_share_link = '<div> <span class="shareText">Share this product:</span><br /><a href="' . tep_href_link('tell_a_friend.php', 'products_id=' . $product_info['products_id']) . '"><img src="images/shareicons/shareIcon_email.gif" alt="E-mail" title="Email this product" border="0"></a>&nbsp;  <a target="_blank" href="http://www.facebook.com/sharer.php?u=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;t=' . $product_info['products_name'] . '"><img  class="shareIcon" src="images/shareicons/shareIcon_Facebook.gif" alt="Facebook" title="Facebook" border="0"></a>&nbsp;<a target="_blank" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;summary="><img class="shareIcon" width="23" height="23" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"></a>&nbsp;  <a target="_blank" href="http://twitthis.com/twit?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '"><img class="shareIcon" src="images/shareicons/shareIcon_Twitter.gif" alt="Twitter" title="Twitter" border="0"></a> ';

$display_product_share_link = '<div> <span class="shareText">Share this product:</span><br /><a target="_blank" href="http://www.facebook.com/sharer.php?u=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;t=' . $product_info['products_name'] . '"><img  class="shareIcon" src="images/shareicons/shareIcon_Facebook.gif" alt="Facebook" title="Facebook" border="0"></a>&nbsp;';

	

	

	// pin interest code #start

	$display_product_share_link .= '<span class="pinterest"><a title="'.$product_info['products_name'].'" href="http://pinterest.com/pin/create/button/?url='.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id'], 'NONSSL').'&media=' . $largeImg.'"></a> <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script></span>&nbsp;';

	// pin interest code #ends

$display_product_share_link .='<a href="' . tep_href_link('tell_a_friend.php', 'products_id=' . $product_info['products_id']) . '"><img src="images/shareicons/shareIcon_email.gif" alt="E-mail" title="Email this product" border="0"></a>&nbsp;<a target="_blank" href="http://twitthis.com/twit?url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '"><img class="shareIcon" src="images/shareicons/shareIcon_Twitter.gif" alt="Twitter" title="Twitter" border="0"></a>&nbsp;';

    

	

	// google plus share icon #start

	$display_product_share_link .= '<span class="google_plus"><div class="g-plus" data-action="share" data-annotation="bubble" data-href="'.tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id'], 'NONSSL').'"></div> <script type="text/javascript">

  (function() {

    var po = document.createElement("script"); 

	po.type = "text/javascript"; 

	po.async = true;

    po.src = "https://apis.google.com/js/platform.js";

    var s = document.getElementsByTagName("script")[0]; 

	s.parentNode.insertBefore(po, s);

  })();

</script></span>&nbsp;';

	// google plus share icon #ends

$display_product_share_link .='<a target="_blank" style="margin-left:29px;" href="http://www.linkedin.com/shareArticle?mini=true&amp;url=' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $product_info['products_id']) . '&amp;title=' . $product_info['products_name'] . '&amp;summary="><img class="shareIcon" width="23" height="23" src="images/shareicons/shareIcon_LinkedIn.gif" alt="LinkedIn" title="LinkedIn" border="0"></a> </div>';

	

$display_product_share_link.="<style type='text/css'> .pinterest > span{background:url('images/shareicons/shareIcon_Pinterest.gif') no-repeat;border-shadow:none;width:24px; height:22px;} .google_plus > #___plus_0 {opacity:0;} .google_plus{background:url('images/shareicons/shareIcon_Googleplus.gif') no-repeat;background-position:3px 1px !important;position:absolute !important;height:29px !important;width:26px !important;}  </style>";

	

	

} else {

    $display_product_share_link = '';

}

$display_product_reviews = '';

$sql = '';

$prod_id = (int) $HTTP_GET_VARS['products_id'];

$write_review = tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'products_id=' . $HTTP_GET_VARS['products_id']);

$sql = "SELECT * FROM reviews AS r LEFT JOIN reviews_description AS rd ON r.reviews_id=rd.reviews_id WHERE r.products_id='$prod_id'";

$q = tep_db_query($sql);

if (tep_db_num_rows($q)) {

    $display_product_reviews .= "<a class='writeReview' href='$write_review'>Write Your Review</a>";

    while ($review = tep_db_fetch_array($q)) {

        $display_product_reviews .= "<div class='productReview'>";

        $display_product_reviews .= "<h3 class='reviewTitle'>{$review['reviews_title']}</h3>";

        $display_product_reviews .= "<p class='reviewText'>{$review['reviews_text']}</p>";

        $display_product_reviews .= "<p class='reviewCustomer'>{$review['customers_nickname']}</p>";

        $display_product_reviews .= "</div>";

    }

} else {

    $display_product_reviews .= "<h3>This item is not yet reviewed</h3>";

    $display_product_reviews .= "<a class='writeReview' href='$write_review'>Write the first review</a>";

}

//if ($child_products_count) {

//    $text_availability_n_price = '<tr><td><span style="font-size:14px;">Choose product selection below for price and availability</td></tr>';

//} else {

$comparison_with_msrp_map_or_other_applies = ENABLE_PRODUCTS_PRICE_COMPARISON == 'True' ? true : false;

if ($comparison_with_msrp_map_or_other_applies) {

    $response = get_comparison_with_msrp_map_or_other_response($prod_id);

}

if (!empty($response[0]) && $response[0] > 0) {

    //$text_availability_n_price = '<tr><td><span style="font-size:14px;">Availability: <b>DISPLAY_PRODUCT_STOCK</b></span></td></tr><tr><td>' . $response[1] . '</td></tr><tr><td class="productPrice">' . $price_text . '</td></tr><tr><td>' . $point_text . '</td></tr><tr><td class="productPrice">' . $rmsrp . '</td></tr>';

    $text_availability_n_price = '<tr><td><span style="font-size:14px;">Availability: <b><span id="availability_message">DISPLAY_PRODUCT_STOCK</span></b></span></td></tr><tr><td>' . $response[1] . '</td></tr><tr><td class="productPrice">' . (($product_info['hide_price'] != 1) ? $price_text : "") . '</td></tr><tr><td>' . $point_text . '</td></tr><tr><td class="productPrice">' . $rmsrp . '</td></tr>';

} else {

    //$text_availability_n_price = '<tr><td><span style="font-size:14px;">Availability: <b>DISPLAY_PRODUCT_STOCK</b></span></td></tr><tr><td class="productPrice">' . $price_text . '</td></tr><tr><td>' . $point_text . '</td></tr>';

    $text_availability_n_price = '<tr><td><span style="font-size:14px;">Availability: <b><span id="availability_message">DISPLAY_PRODUCT_STOCK &nbsp;</span></b></span></td></tr><tr><td class="productPrice">' . (($product_info['hide_price'] != 1) ? $price_text : "") . '</td></tr><tr><td>' . $point_text . '</td></tr>';

}

//}

$sts->template['products_id'] = $_GET['products_id'];

$text_display = str_replace("DISPLAY_AVAILABILITY_N_PRICE", $text_availability_n_price, $text_display);

$sts->template['product_availability_n_price'] = str_replace('DISPLAY_PRODUCT_STOCK', $display_products_stock, $text_availability_n_price);

// added on 19-04-2016 #start

$text_display = str_replace("DISPLAY_PRODUCTS_STOCK_AVAILABILITY", $display_products_stock_availability, $text_display);

$sts->template['display_products_stock_availability'] = $display_products_stock_availability;

// added on 19-04-2016 #ends

$text_display = str_replace("DISPLAY_PRODUCT_IMAGE", $display_product_image, $text_display);

$sts->template['product_image'] = $display_product_image;

$text_display = str_replace("DISPLAY_PACKAGE", $display_package_str, $text_display);

$sts->template['display_package_str'] = $display_package_str;

$text_display = str_replace("DISPLAY_PRODUCT_EXTRA_IMAGE", $display_product_extra_images, $text_display);

$sts->template['display_product_extra_images'] = $display_product_extra_images;

$text_display = str_replace("DISPLAY_PRODUCT_SPECIFICATIONS", $display_products_specifications, $text_display);

$sts->template['product_specifications'] = $display_products_specifications;

$text_display = str_replace("DISPLAY_PRODUCT_ATTRIBUTES", $display_products_attributes, $text_display);

$sts->template['product_attributes'] = $display_products_attributes;

$text_display = str_replace("DISPLAY_PRODUCT_DESCRIPTION", $display_products_description, $text_display);

$sts->template['product_description'] = $display_products_description;

$text_display = str_replace("DISPLAY_PRODUCT_PRICE", $display_products_price, $text_display);

$text_display = str_replace("DISPLAY_PRODUCT_NAME", $display_products_name, $text_display);

$text_display = str_replace("DISPLAY_PRODUCT_MANUFACTURER", $display_products_manufacturer, $text_display);

$sts->template['product_manufacturer'] = $display_products_manufacturer;

$text_display = str_replace('DISPLAY_PRODUCT_MODEL', $display_product_model, $text_display);

$text_display = str_replace('DISPLAY_CHILD_PRODUCT_PRICE', $display_child_product_price, $text_display);

$sts->template['product_child_price'] = $display_child_product_price;

$text_display = str_replace("DISPLAY_PRODUCT_ATTRIBUTE", $display_products_attributes, $text_display);

$sts->template['product_attributes_value'] = $display_products_attribute;

//$text_display = str_replace("DISPLAY_PRODUCT_ATTRIBUTE", '', $text_display);

$text_display = str_replace("DISPLAY_PRODUCT_QUANTITY", $display_products_quantity, $text_display);

$sts->template['product_quantity'] = $display_products_quantity;

$text_display = str_replace("DISPLAY_PRODUCT_DISCLAIMER", $display_products_disclaimer, $text_display);

$sts->template['product_disclaimer'] = $display_products_disclaimer;

$text_display = str_replace("DISPLAY_PRODUCT_ADD_TO_CART", $display_products_add_to_cart, $text_display);

$text_display = str_replace("DISCLAIMER_ADD_TO_CART", $product_info['disclaimer_needed'], $text_display);

$sts->template['product_add_to_cart'] = $display_products_add_to_cart;

$text_display = str_replace("DISPLAY_PRODUCT_ADD_TO_WISHLIST", $display_product_add_to_wishlist, $text_display);

$sts->template['product_add_to_wishlist'] = $display_product_add_to_wishlist;

$text_display = str_replace("DISPLAY_CHILD_PRODUCTS", $display_child_products, $text_display);

$text_display = str_replace("DISPLAY_PRODUCT_RATINGS", $display_products_ratings, $text_display);

$sts->template['product_ratings'] = $display_products_ratings;

$text_display = str_replace("DISPLAY_PRODUCT_WRITE_RATINGS", $display_products_ratings_write, $text_display);

$sts->template['product_ratings_write'] = $display_products_ratings_write;

$text_display = str_replace("DISPLAY_PRODUCT_OPTIONS_TITLE", $display_product_options_title, $text_display);

$sts->template['product_options_title'] = $display_product_options_title;

$text_display = str_replace("DISPLAY_PRODUCT_STOCK", $display_products_stock, $text_display);

$sts->template['product_stock_message'] = $display_products_stock;

$text_display = str_replace("DISPLAY_PRODUCT_SHARE_LINK", $display_product_share_link, $text_display);

$sts->template['product_share_links'] = $display_product_share_link;

$text_display = str_replace("DISPLAY_EXTRA_IMAGES", $display_extra_images, $text_display);

$text_display = str_replace("DISPLAY_PRODUCT_REVIEWS", $display_product_reviews, $text_display);

$sts->template['product_reviews'] = $display_product_reviews;

?>

          <?php // End Template Area ?>

          <table  width="100%" border="0">

        <tr>

              <td><?php

                                // Modified for Related Products: dt:24July2008

                                /*

                                  ob_start();

                                  if ((USE_CACHE == 'true') && empty($SID)) {

                                  echo tep_cache_also_purchased(3600);

                                  } else {

                                  include (DIR_WS_MODULES . FILENAME_ALSO_PURCHASED_PRODUCTS);

                                  }

                                  $alsoPurchasedProducts = ob_get_contents();

                                  ob_end_clean();

                                 */

                                ob_start();

                                if ((USE_CACHE == 'true') && !SID) {

                                    echo tep_cache_also_purchased(3600);

                                    include (DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);

                                } else {

                                    include (DIR_WS_MODULES . FILENAME_XSELL_PRODUCTS);

                                }

                                $related_product_contents = ob_get_contents();

                                ob_end_clean();

                                /*

                                  ob_start();

                                  include (DIR_WS_MODULES . FILENAME_RECOMENDED_PRODUCTS);

                                  $recommendedHtml = ob_get_contents();

                                  ob_end_clean();

                                  ob_start();

                                  include (DIR_WS_MODULES . 'new_products.php');

                                  $newProductsHtml = ob_get_contents();

                                  ob_end_clean();

                                  $popularHtml = '';

                                  if (ENABLE_POPULAR_PRODUCTS=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'popular_products.php');

                                  $popularHtml = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $hotProductsHtml = '';

                                  if (ENABLE_HOT_PRODUCTS=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'hot_products.php');

                                  $hotProductsHtml = ob_get_contents();

                                  ob_end_clean();

                                  }

                                 */

                                $text_display = str_replace("DISPLAY_PRODUCT_RELATED_ITEMS", $related_product_contents, $text_display);

                                $sts->template['product_related_items'] = $related_product_contents;

                                /*

                                  $text_display = str_replace("DISPLAY_RECOMMENDED_PRODUCTS", $recommendedHtml, $text_display);

                                  $text_display = str_replace("DISPLAY_ALSO_PURCHASED_PRODUCTS", $alsoPurchasedProducts, $text_display);

                                  $text_display = str_replace("DISPLAY_NEW_PRODUCTS", $newProductsHtml, $text_display);

                                  $text_display = str_replace("DISPLAY_POPULAR_PRODUCT", $popularHtml, $text_display);

                                  $text_display = str_replace("DISPLAY_HOW_PRODUCTS", $hotProductsHtml, $text_display);

                                  $featuredproduct1 = '';

                                  if (ENABLE_FEATURE_PRODUCTS_ONE=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'featuredproduct1.php');

                                  $featuredproduct1 = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $featuredproduct1 = '';

                                  if (ENABLE_FEATURE_PRODUCTS_TWO=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'featuredproduct2.php');

                                  $featuredproduct2 = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $featuredproducr3 = '';

                                  if (ENABLE_FEATURE_PRODUCTS_THREE=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'featuredproduct3.php');

                                  $featuredproduct3 = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_1", $featuredproduct1, $text_display);

                                  $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_2", $featuredproduct2, $text_display);

                                  $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_3", $featuredproduct3, $text_display);

                                  $featuredManufacturers = '';

                                  if (ENABLE_FEATURE_MANUFACTURERS=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'featuredManufacturers.php');

                                  $featuredManufacturers = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $text_display = str_replace("DISPLAY_FEATURED_MANUFACTUERERS", $featuredManufacturers, $text_display);

                                  $featuredCategory = '';

                                  if (ENABLE_FEATURE_CATEGORY=='True'){

                                  ob_start();

                                  include (DIR_WS_MODULES . 'featuredCategory.php');

                                  $featuredCategory = ob_get_contents();

                                  ob_end_clean();

                                  }

                                  $text_display = str_replace("DISPLAY_FEATURED_CATEGORY", $featuredCategory, $text_display); */

//in order to control page loading speed, all right blocks set to blank and logic that produces the block commented

//$text_display = str_replace("DISPLAY_RECOMMENDED_PRODUCTS", $recommendedHtml, $text_display);

                                $text_display = str_replace("DISPLAY_RECOMMENDED_PRODUCTS", '', $text_display);

//$text_display = str_replace("DISPLAY_ALSO_PURCHASED_PRODUCTS", $alsoPurchasedProducts, $text_display);

                                $text_display = str_replace("DISPLAY_ALSO_PURCHASED_PRODUCTS", '', $text_display);

//$text_display = str_replace("DISPLAY_NEW_PRODUCTS", $newProductsHtml, $text_display);

                                $text_display = str_replace("DISPLAY_NEW_PRODUCTS", '', $text_display);

//$text_display = str_replace("DISPLAY_POPULAR_PRODUCT", $popularHtml, $text_display);

                                $text_display = str_replace("DISPLAY_POPULAR_PRODUCT", '', $text_display);

//$text_display = str_replace("DISPLAY_HOW_PRODUCTS", $hotProductsHtml, $text_display);

                                $text_display = str_replace("DISPLAY_HOW_PRODUCTS", '', $text_display);

//$text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_1", $featuredproduct1, $text_display);

                                $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_1", '', $text_display);

//$text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_2", $featuredproduct2, $text_display);

                                $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_2", '', $text_display);

//$text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_3", $featuredproduct3, $text_display);

                                $text_display = str_replace("DISPLAY_FEATURED_PRODUCTS_3", '', $text_display);

//$text_display = str_replace("DISPLAY_FEATURED_MANUFACTUERERS", $featuredManufacturers, $text_display);

                                $text_display = str_replace("DISPLAY_FEATURED_MANUFACTUERERS", '', $text_display);

//$text_display = str_replace("DISPLAY_FEATURED_CATEGORY", $featuredCategory, $text_display);

                                $text_display = str_replace("DISPLAY_FEATURED_CATEGORY", '', $text_display);

                                echo $text_display;

                                ?></td>

            </tr>

      </table>

            </td>

          

            </tr>

          

        </table>

      </td>

    

      </tr>

    

    <tr>

          <td><?php

                                            echo tep_draw_separator('pixel_trans.gif', '100%', '10');

                                            ?></td>

        </tr>

    <?php

                                            if (tep_not_null($product_info['products_url'])) {

                                                ?>

    <tr>

          <td class="main"><?php

                                                echo sprintf(TEXT_MORE_INFORMATION, tep_href_link(FILENAME_REDIRECT, 'action=url&goto=' . urlencode($product_info['products_url']), 'NONSSL', true, false));

                                                ?></td>

        </tr>

    <tr>

          <td><?php

                                                echo tep_draw_separator('pixel_trans.gif', '100%', '10');

                                                ?></td>

        </tr>

    <?php

                                            }

                                            ?>

    <tr>

          <td><?php

                                            echo tep_draw_separator('pixel_trans.gif', '100%', '10');

                                            ?></td>

        </tr>

    <?php

                                            ?>

    <?php

                                            ?>

  </table>

      <?php

//BOF:mod 10-21-2013

                                            /*

                                              //EOF:mod 10-21-2013

                                              if($Child_products_exists == 'No'){?>

                                              </form>

                                              <?php }

                                              //BOF:mod 10-21-2013

                                             */

//if (!$child_items_exist)

//{

                                            ?>

        </form>

      

      <?php

//}

//EOF:mod 10-21-2013

                                            ?>

        </td>

      

      

      <!-- body_text_eof //-->

      

      

    <td width="<?php

                                            echo BOX_WIDTH;

                                            ?>" valign="top"><table border="0" width="<?php

                                            echo BOX_WIDTH;

                                            ?>" cellspacing="0" cellpadding="2">

        

        <!-- right_navigation //-->

        

        <?php

                                            require (DIR_WS_INCLUDES . 'column_right.php');

                                            ?>

        

        <!-- right_navigation_eof //-->

        

      </table></td>

  </tr>

    </table>

<!-- body_eof //--> 

<!-- footer //-->

<?php

                                            require (DIR_WS_INCLUDES . 'footer.php');

                                            ?>

<!-- footer_eof //--> 

<br>

<div id="my-store-4135379">This store is powered by Ecwid - <a href="http://www.ecwid.com">AJAX shopping cart</a>. If you your browser does not support JavaScript, please proceed to its <a href="http://app.ecwid.com/jsp/4135379/catalog">simple HTML version</a>.</div>

<div> 

      <script type="text/javascript" src="http://app.ecwid.com/script.js?4135379" charset="utf-8"></script><script type="text/javascript"> xProductBrowser("categoriesPerRow=3", "views=grid(3,3) list(10) table(20)", "categoryView=grid", "searchView=list", "id=my-store-4135379");</script> 

    </div>

<br>

<div> 

      <script type="text/javascript" src="http://app.ecwid.com/script.js?4135379" charset="utf-8"></script> 

      

      <!-- remove layout parameter if you want to position minicart yourself --> 

      

      <script type="text/javascript"> xMinicart("layout=attachToCategories");</script> 

    </div>

</body>

</html>

<?php

                                            require (DIR_WS_INCLUDES . 'application_bottom.php');

                                            $time_start = explode(' ', PAGE_PARSE_START_TIME);

                                            $time_end = explode(' ', microtime());

                                            $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);

                                            //echo '<span class="smallText">Parse Time: ' . $parse_time . 's</span>';

                                            exit();

                                            ?>

											

                                            

                                         