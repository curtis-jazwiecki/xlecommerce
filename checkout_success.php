<?php
/*
  $Id: checkout_success.php,v 1.49 2003/06/09 23:03:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
	
// if the customer is not logged on, redirect them to the shopping cart page
  if (!tep_session_is_registered('customer_id') && basename($_SERVER['HTTP_REFERER']) != 'checkout.php') {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'update')) {
    $notify_string = 'action=notify&';
    $notify = $HTTP_POST_VARS['notify'];
    if (!is_array($notify)) $notify = array($notify);
    for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
      $notify_string .= 'notify[]=' . $notify[$i] . '&';
    }
    if (strlen($notify_string) > 0) $notify_string = substr($notify_string, 0, -1);

    tep_redirect(tep_href_link(FILENAME_DEFAULT, $notify_string));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_SUCCESS);

  $breadcrumb->add(NAVBAR_TITLE_1);
  $breadcrumb->add(NAVBAR_TITLE_2);

  $global_query = tep_db_query("select global_product_notifications from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customer_id . "'");
  $global = tep_db_fetch_array($global_query);

  if ($global['global_product_notifications'] != '1') {
    $orders_query = tep_db_query("select orders_id from " . TABLE_ORDERS . " where customers_id = '" . (int)$customer_id . "' order by date_purchased desc limit 1");
    $orders = tep_db_fetch_array($orders_query);

    $products_array = array();
    $products_query = tep_db_query("select products_id, products_name from " . TABLE_ORDERS_PRODUCTS . " where orders_id = '" . (int)$orders['orders_id'] . "' order by products_name");
    while ($products = tep_db_fetch_array($products_query)) {
      $products_array[] = array('id' => $products['products_id'],
                                'text' => $products['products_name']);
    }
  }
  
// PWA BOF 2b
//delete the temporary account
  $pwa_query = tep_db_query("select guest_account from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  $pwa = tep_db_fetch_array($pwa_query);
  if ($pwa['guest_account'] == 1) {
  tep_db_query("delete from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  tep_db_query("delete from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "'");
  tep_db_query("delete from " . TABLE_CUSTOMERS_INFO . " where customers_info_id = '" . (int)$customer_id . "'");
  tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET . " where customers_id = '" . (int)$customer_id . "'");
  tep_db_query("delete from " . TABLE_CUSTOMERS_BASKET_ATTRIBUTES . " where customers_id = '" . (int)$customer_id . "'");
  tep_session_unregister('guest_account');
  tep_session_unregister('customer_id');
  tep_session_unregister('customer_default_address_id');
  tep_session_unregister('customer_first_name');
  tep_session_unregister('customer_country_id');
  tep_session_unregister('customer_zone_id');
  tep_session_unregister('comments');
  }
// PWA EOF 2b

fire_order_feed_to_obn($orders['orders_id']);

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
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top" colspan="3"><?php echo tep_draw_form('order', tep_href_link(FILENAME_CHECKOUT_SUCCESS, 'action=update', 'SSL')); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td valign="top" class="pageHeading" colspan="3"><?php echo HEADING_TITLE; ?></td></tr>
		<tr><td colspan="3"><table cellpadding="0" cellspacing="0" border="0" width="100%">
			  <tr><td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_shipping.gif'); ?></a></td>
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_payment.gif'); ?></a></td>
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_confirmation.gif'); ?></a></td>
			  	<td><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_success_active.gif'); ?></td>
			  </tr>
			  </table>
	</td></tr>
		
		
		<!--<div align="center"><h2 style="color:#FF0000">Site for demonstration purposes only. No orders will be processed or shipped.</h2></div>-->
		
		<tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg" class="infoBoxHeadingLoginl"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin"><b><?php echo 'Checkout Success'; ?></b></td><td width="5" height="20" class="infoBoxHeadingLogin2" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td></tr>
		
		<tr><td style="border:1px solid #cacaca;" colspan="3">
        <table cellpadding="0" cellspacing="0" width="100%" border="0" class="tab-checkout-success">
		
		<tr><td>&nbsp;</td> <td valign="top" class="main"><?php echo TEXT_SUCCESS . ' ' . $shipping_text; ?>
<?php
/*
  if ($global['global_product_notifications'] != '1') {
    echo TEXT_NOTIFY_PRODUCTS . '<br><p class="productsNotifications" >';

    $products_displayed = array();
    for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
      if (!in_array($products_array[$i]['id'], $products_displayed)) {
        echo tep_draw_checkbox_field('notify[]', $products_array[$i]['id']) . ' ' . $products_array[$i]['text'] . '<br>';
        $products_displayed[] = $products_array[$i]['id'];
      }
    }

    echo '</p>';
  } else {
    echo TEXT_SEE_ORDERS . '<br><br>' . TEXT_CONTACT_STORE_OWNER;
  }
  */
  //PWA BOF
  if (!tep_session_is_registered('customer_is_guest')){
  //PWA EOF
    if ($global['global_product_notifications'] != '1') {
      echo TEXT_NOTIFY_PRODUCTS . '<br><p class="productsNotifications">';

      $products_displayed = array();
      for ($i=0, $n=sizeof($products_array); $i<$n; $i++) {
        if (!in_array($products_array[$i]['id'], $products_displayed)) {
          echo tep_draw_checkbox_field('notify[]', $products_array[$i]['id']) . ' ' . $products_array[$i]['text'] . '<br>';
          $products_displayed[] = $products_array[$i]['id'];
		  
        }
      }
      echo '</p>';
    } else {
      echo TEXT_SEE_ORDERS . '<br><br>' . TEXT_CONTACT_STORE_OWNER;
    }
  //PWA BOF
  }
  //PWA EOF
?>
            <h3><?php echo TEXT_THANKS_FOR_SHOPPING; ?></h3></td>
		
		<td class="main" align="center">
					<img src="./images/template/logo_fffo.jpg" >
				</td></tr></table></td></tr>
         
        </table></td>
      </tr>
<?php // ###### Added CCGV Contribution #########
 require('add_checkout_success.php'); //ICW CREDIT CLASS/GV SYSTEM 
// ###### Added CCGV Contribution ######### ?>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td align="right" class="main" colspan="3"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
    <?php /*  <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td width="50%"><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_DELIVERY; ?></td>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
            <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
          </tr>*/?>
        </table></td>
      </tr>
<?php if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php'); ?>
    </table></form></td>
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
<!-- Google Code for Purchase Conversion Page -->
<script language="JavaScript" type="text/javascript">
<!--
var google_conversion_id = 1038417089;
var google_conversion_language = "en_US";
var google_conversion_format = "2";
var google_conversion_color = "ccffff";
var google_conversion_label = "L2EQCMvAbxDB-ZPvAw";
if (100.0) {
var google_conversion_value = 100.0;
}
//-->
</script>
<script language="JavaScript" src="https://www.googleadservices.com/pagead/conversion.js">
</script>
<noscript>
<img height="1" width="1" border="0" src="https://www.googleadservices.com/pagead/conversion/1038417089/?value=100.0&amp;label=L2EQCMvAbxDB-ZPvAw&amp;script=0"/>
</noscript>
</body>
</html>
<?php
tep_session_unregister('shipping_text');
 require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
