<?php

/*

  $Id: index.php,v 1.1 2003/06/11 17:37:59 hpdl Exp $

  

  CloudCommerce - Multi-Channel eCommerce Solutions

  http://www.cloudcommerce.org

  Copyright (c) 2016 Outdoor Business Network, Inc.

*/

require('includes/application_top.php');

if(checkmobile2()) {

    if (!isset($_SESSION['switch_template_flag']) || !$_SESSION['switch_template_flag']){

        $template = 'full/template6';

    }

}

// BOF Separate Pricing Per Customer

if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {

    $customer_group_id = $_SESSION['sppc_customer_group_id'];

} else {

    $customer_group_id = '0';

}

// EOF Separate Pricing Per Customer



$category_depth = 'top';

// Add-on - Information Pages Unlimited

require_once(DIR_WS_FUNCTIONS . 'information.php');

tep_information_customer_greeting_define(); // Should be called before the Default Language is defined

  

require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_DEFAULT);

//get top three categories #start

$top_3_home_page_category = get_top_categories('3');

$sts->template['top_3_home_page_category'] = $top_3_home_page_category;



$get_featured_product = getFeaturedProductsByGroup(0);

$sts->template['get_featured_product'] = $get_featured_product;



$get_featured_manufacturer_products =  getFeaturedProductsByGroup(1);

$sts->template['get_featured_manufacturer_products'] = $get_featured_manufacturer_products;

// get top three categories #ends


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

                <?php

                $current_template = strtolower(basename(STS_TEMPLATE_DIR));

                if ($current_template=='template11' || $current_template=='template12' || $current_template=='template18' || $current_template=='template19' || $current_template=='template21'){

                    include_once('includes/sts_templates/' . $check['configuration_value'] . '/blocks/main_content.php.html');

                } else {

                ?>

                    <table border="0" width="100%" cellspacing="0" cellpadding="0">

                        <tr>

                            <td>

                                <table border="0" width="100%" cellspacing="0" cellpadding="0">

                                </table>

                            </td>

                        </tr>

                        <tr>

                            <td>

                                <table border="0" width="100%" cellspacing="0" cellpadding="0">

                                <?php if ($banner = tep_banner_exists('dynamic', 'mainpage')) { ?>

                                    <tr>

                                        <td align="center"><?php echo tep_display_banner('static', $banner); ?></td>

                                    </tr>

                                    <!--//BOF MOBILESITE 10 JAN 2014	-->  

                                <?php

                                }

                                ?>

                                <tr><td class="main">

                                        <?php

                                //if(mobile_site=='True' && checkmobile2()==true){

                                if(checkmobile2() && (!isset($_SESSION['switch_template_flag']) || !$_SESSION['switch_template_flag'])) {

                                    echo '<table align="center"><tr><td><div align="center"><a href="/shop.php"><img src="images/mobile/shop_button.jpg" border="0"/></a></div></td></tr><tr><td><div align="center"><a href="/specials.php"><img src="images/mobile/specials_button.jpg" border="0" /></a></div></td></tr><tr><td><div align="center"><a href="/contact_us.php"><img src="images/mobile/contact_button.jpg" border="0"/></a></div></td></tr></table><br>';

      echo '$featuredmobileproducts';

                                    /*

                                ?>

                                    <tr>

                                        <td align="center"><img src="images/mobilebanner/<?php echo mobile_banner;?>">

                                        </td>

                                    </tr>

                                    <style>

                                        #skubanner table{width:99%; float: left;}

                                        img[src="http://obnkroll.com/images/krollfrontimage.jpg"],img[src="http://obnkroll.com/images/template/fp_specials.jpg"]{

                                            display: none;

                                        }

                                        tr#orion img {width: 99%; max-width: 340px;}

                                        #skubanner table img {

                                            max-width: 100%;

                                            width: auto;

                                        }

                                    </style>

                                <?php

                                */

                                } else {

                                ?>

                                <!--//EOF MOBILESITE 10 JAN 2014	-->  

                                    <tr>

                                        <td class="main" id="skubanner"><?php echo TEXT_MAIN; ?></td>

                                    </tr>

                                    <?PHP

                                } 

                                ?>

                                    <tr>

                                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

                                    </tr>

                                    <?php // include(DIR_WS_MODULES . FILENAME_UPCOMING_PRODUCTS); ?>

                                    <?php //include(DIR_WS_MODULES . 'featured.php'); ?>

                                </table>

                            </td>

                        </tr>

                    </table>

                <?php

                }

                ?>

                </td>

                <!-- body_text_eof //-->

                <td width="<?php echo BOX_WIDTH; ?>" valign="top">

                    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">

                    <!-- right_navigation -->

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