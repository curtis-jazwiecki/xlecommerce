<?php
/*
  $Id: checkout_confirmation.php,v 1.139 2003/06/11 17:34:53 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot(array('mode' => 'SSL', 'page' => FILENAME_CHECKOUT_PAYMENT));
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!tep_session_is_registered('shipping')) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }

  if (!tep_session_is_registered('payment')) tep_session_register('payment');
  if (isset($HTTP_POST_VARS['payment'])) $payment = $HTTP_POST_VARS['payment'];

  if (!tep_session_is_registered('comments')) tep_session_register('comments');
  if (tep_not_null($HTTP_POST_VARS['comments'])) {
    $comments = tep_db_prepare_input($HTTP_POST_VARS['comments']);
  }

// load the selected payment module
  require(DIR_WS_CLASSES . 'payment.php');
// ################# Added CGV Contribution ##################"
  if ($credit_covers) $payment=''; 
// ################# End Added CGV Contribution ##################"
  $payment_modules = new payment($payment);
// ################# Added CGV Contribution ##################"
  require(DIR_WS_CLASSES . 'order_total.php');
// ################# End Added CGV Contribution ##################"

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;

  $payment_modules->update_status();
// ################# Added CGV Contribution ##################"
// CCGV Contribution
  $order_total_modules = new order_total;
  $order_total_modules->collect_posts();
  $order_total_modules->pre_confirmation_check();

// >>> FOR ERROR gv_redeem_code NULL 
//if (isset($_POST['gv_redeem_code']) && ($_POST['gv_redeem_code'] == null)) {tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));} 
// <<< end for error

//  if ( ( is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($$payment) ) || (is_object($$payment) && ($$payment->enabled == false)) ) {
  /*if ( (is_array($payment_modules->modules)) && (sizeof($payment_modules->modules) > 1) && (!is_object($$payment)) && (!$credit_covers) ) {
// ################# End Added CGV Contribution ##################"
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }*/
  ##### Points/Rewards Module V2.1rc2a check for error BOF #######
  if ((USE_POINTS_SYSTEM == 'true') && (USE_REDEEM_SYSTEM == 'true')) {
	  if (isset($_POST['customer_shopping_points_spending']) && is_numeric($_POST['customer_shopping_points_spending']) && ($_POST['customer_shopping_points_spending'] > 0)) {
		  $customer_shopping_points_spending = false;
		  if (tep_calc_shopping_pvalue($_POST['customer_shopping_points_spending']) < $order->info['total'] && !is_object($$payment)) {
			  $customer_shopping_points_spending = false;
			  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(REDEEM_SYSTEM_ERROR_POINTS_NOT), 'SSL'));
		  } else {
			  $customer_shopping_points_spending = $_POST['customer_shopping_points_spending'];
			  if (!tep_session_is_registered('customer_shopping_points_spending')) tep_session_register('customer_shopping_points_spending');
		  }
	  }
	  
	  if (tep_not_null(USE_REFERRAL_SYSTEM)) {
		  if (isset($_POST['customer_referred']) && tep_not_null($_POST['customer_referred'])) {
			  $customer_referral = false;
			  $check_mail = trim($_POST['customer_referred']);
			  if (tep_validate_email($check_mail) == false) {
				  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(REFERRAL_ERROR_NOT_VALID), 'SSL'));
			  } else {
				  $valid_referral_query = tep_db_query("select customers_id from " . TABLE_CUSTOMERS . " where customers_email_address = '" . $check_mail . "' limit 1");
				  $valid_referral = tep_db_fetch_array($valid_referral_query);
				  if (!tep_db_num_rows($valid_referral_query)) {
					  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(REFERRAL_ERROR_NOT_FOUND), 'SSL'));
				  }
				  
				  if ($check_mail == $order->customer['email_address']) {
					  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(REFERRAL_ERROR_SELF), 'SSL'));
				  } else {
					  $customer_referral = $valid_referral['customers_id'];
					  if (!tep_session_is_registered('customer_referral')) tep_session_register('customer_referral');
				  }
			  }
		  }
	  }
  }

  if ( ( is_array($payment_modules->modules) && (sizeof($payment_modules->modules) > 1) && !is_object($$payment) ) && (!$credit_covers) && (!$customer_shopping_points_spending)  ) {
	  tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT, 'error_message=' . urlencode(ERROR_NO_PAYMENT_MODULE_SELECTED), 'SSL'));
  }
########  Points/Rewards Module V2.1rc2a EOF #################*/

  if (is_array($payment_modules->modules)) {
    $payment_modules->pre_confirmation_check();
  }

// load the selected shipping module
//MVS start
  if (($total_weight > 0 ) || (SELECT_VENDOR_SHIPPING == 'true') ) {
    include_once (DIR_WS_CLASSES . 'vendor_shipping.php');
  } elseif ( ($total_weight > 0 ) || (SELECT_VENDOR_SHIPPING == 'false') ) {
    include_once (DIR_WS_CLASSES . 'shipping.php');
  }
//MVS End
  $shipping_modules = new shipping($shipping);
// ################# Added CGV Contribution ##################"

//  require(DIR_WS_CLASSES . 'order_total.php');
//  $order_total_modules = new order_total;
// ################# End Added CGV Contribution ##################"

// Stock Check
  /*$any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      if (tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty'])) {
        $any_out_of_stock = true;
      }
    }*/
  // begin Bundled Products
  $any_bundle_only = false;
  $products = $cart->get_products();
  for ($i=0, $n=sizeof($products); $i<$n; $i++) {
    if ($products[$i]['sold_in_bundle_only'] == 'yes') $any_bundle_only = true;
  }
  if ($any_bundle_only) tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
// Stock Check
  $any_out_of_stock = false;
  if (STOCK_CHECK == 'true') {
    $bundle_contents = array();
    $bundle_values = array();
    $base_product_ids_in_order = array();
    $bundle_qty_ordered = array();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if ($products[$i]['bundle'] == "yes") {
        $tmp = get_all_bundle_products($products[$i]['id']);
        $bundle_values[$products[$i]['id']] = $products[$i]['final_price'];
        $bundle_contents[$products[$i]['id']] = $tmp;
        $bundle_qty_ordered[$products[$i]['id']] = $products[$i]['quantity'];
        foreach ($tmp as $id => $qty) {
          if (!in_array($id, $base_product_ids_in_order)) $base_product_ids_in_order[] = $id; // save unique ids
        }
      } else {
        if (!in_array($products[$i]['id'], $base_product_ids_in_order)) $base_product_ids_in_order[] = $products[$i]['id']; // save unique ids
      }
    }
    $product_on_hand = array();
    foreach ($base_product_ids_in_order as $id) {
      // get quantity on hand for every unique product contained in this order except bundles
      $product_on_hand[$id] = tep_get_products_stock($id);
    }
    if (!empty($bundle_values)) { // if bundles exist in order
      arsort($bundle_values); // sort array so bundle ids with highest value come first
      foreach ($bundle_values as $bid => $bprice) {
        $bundles_available = array();
        foreach ($bundle_contents[$bid] as $pid => $qty) {
          $bundles_available[] = intval($product_on_hand[$pid] / $qty);
        }
        $product_on_hand[$bid] = min($bundles_available); // max number of this bundle we can make with product on hand
        $deduct = min($product_on_hand[$bid], $bundle_qty_ordered[$bid]); // assume we sell as many of the bundle as possible
        foreach ($bundle_contents[$bid] as $pid => $qty) {
          // reduce product left on hand by number sold in this bundle before checking next less expensive bundle
          // also lets us know how many we have left to sell individually
          $product_on_hand[$pid] -= ($deduct * $qty);
        }
      }
    }
    for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
      if ($product_on_hand[$order->products[$i]['id']] < $order->products[$i]['qty']) {
        $any_out_of_stock = true;
      }
    }
    // end Bundled Products


    // Out of Stock
    if ( (STOCK_ALLOW_CHECKOUT != 'true') && ($any_out_of_stock == true) ) {
      tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
    }
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_CONFIRMATION);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2);
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
    <td width="100%" valign="top" colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0" class="Order Confirmation">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php //echo tep_image(DIR_WS_IMAGES . 'table_background_confirmation.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr><td colspan="3"><table cellpadding="0" cellspacing="0" border="0" width="100%">
			  <tr><td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x" class="image_progress_bar"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">'
			. tep_image(DIR_WS_IMAGES . 'template/checkout_shipping.gif','','100%','auto') . '</a>';?></td>
			  
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x" class="image_progress_bar"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '">'.tep_image(DIR_WS_IMAGES . 'template/checkout_payment.gif','','100%','auto') . '</a>';?></td>
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x" class="image_progress_bar"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_confirmation_active.gif','','100%','auto'); ?></td>
			  	<td class="image_progress_bar"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_success.gif','','100%','auto'); ?></td>
			  </tr>
			  </table>
	</td></tr>
	    <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td width="5" height="20" align="left" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b>Shipping Information</b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgR.jpg"></td>
      </tr>
      <tr>
        <td colspan="3"><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
          <tr class="infoBoxContents">
<?php
  if ($sendto != false) {
?>
            <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
			<?php /* ?>
              <tr>
                <td class="main"><?php echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b>' . (($customer_id>0 || (defined('PURCHASE_WITHOUT_ACCOUNT_SEPARATE_SHIPPING') && PURCHASE_WITHOUT_ACCOUNT_SEPARATE_SHIPPING=='yes') )? '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>':''); ?></td>
              </tr>
			  <?php */ ?>
              <tr><!-- PWA BOF -->
                <td class="main"><?php echo '<b>' . HEADING_DELIVERY_ADDRESS . '</b>'.(((! tep_session_is_registered('customer_is_guest')) || (defined('PURCHASE_WITHOUT_ACCOUNT_SEPARATE_SHIPPING') && PURCHASE_WITHOUT_ACCOUNT_SEPARATE_SHIPPING=='yes') )? ' <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING_ADDRESS, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>':''); ?></td>
              </tr><!-- PWA EOF -->
              <tr>
                <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td>
              </tr>
				<?php
                    if ($order->info['shipping_method']) {?>
                      <tr>
                        <td class="main"><?php echo '<b>' . HEADING_SHIPPING_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                      </tr>
                      <tr>
                        <td class="main"><?php echo $order->info['shipping_method']; ?></td>
                      </tr>
                <?php
                    }
                ?>
                
                <?php
                    if ( (isset($_SESSION['ffl_selected']) && (count($_SESSION['ffl_selected']) > 0) ) ) {?>
						<tr>
                            <td class="main" style="padding-top:5px;" ><b><?php echo HEADING_FFL_SELECTED; ?></b></td>
                        </tr>
                        <tr>
                        	<td class="main">&nbsp;</td>
                        </tr>
                          
						<?php
						foreach($_SESSION['ffl_selected'] as $vID => $fflID) {?>
                          
                          <tr>
                            <td class="main"><span style="font-weight:bold;"><?php echo getFFLDealerDetails($fflID); ?></span></td>
                          </tr>
                          
                <?php
						}
                    }
					
					
                ?>
                
                
                
                
                
                
            
            
            
            </table></td>
<?php
  }
?>
            <td width="<?php echo (($sendto != false) ? '70%' : '100%'); ?>" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
?>
                  <tr>
                    <td class="main" colspan="2"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                    <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td>
                    <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td>
                  </tr>
<?php
  } else {
?>
                  <tr>
                    <td class="main" colspan="3"><?php echo '<b>' . HEADING_PRODUCTS . '</b> <a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
                  </tr>
<?php
  }

  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    echo '          <tr>' . "\n" .
         '            <td class="main" align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '            <td class="main" valign="top">' . $order->products[$i]['name'];

    /*if (STOCK_CHECK == 'true') {
      echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
    }*/
    if (STOCK_CHECK == 'true') {
      // begin Bundled Products
      // check against product left on hand after bundles have been sold
      $stock_check = '';
      if ($product_on_hand[$order->products[$i]['id']] <= 0) {
        $stock_check = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '<br>' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . TEXT_NOT_AVAILABLEINSTOCK . '</span>';
      } elseif ($product_on_hand[$order->products[$i]['id']] < $order->products[$i]['qty']) {
        $stock_check = '<span class="markProductOutOfStock">' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . '<br>' . STOCK_MARK_PRODUCT_OUT_OF_STOCK . TEXT_ONLY_THIS_AVAILABLEINSTOCK1 . $product_on_hand[$order->products[$i]['id']] . TEXT_ONLY_THIS_AVAILABLEINSTOCK2 . '</span>';
      }
      echo $stock_check;
      // end Bundled Products
    }


    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }

    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";

    echo '            <td class="main" align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
?>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?><br /><!--<div align="center"><h2 style="color:#FF0000">Site for demonstration purposes only. No orders will be processed or shipped.</h2></div>--></td>
      </tr>
      <tr>
        <td width="5" height="20" align="left" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo HEADING_BILLING_INFORMATION; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgR.jpg"></td>
      </tr>
      <tr>
        <td colspan="3"><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
          <tr class="infoBoxContents">
            <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
			<?php /* ?>
              <tr>
                <td class="main"><?php echo '<b>' . HEADING_BILLING_ADDRESS . '</b> <a href="' . (($customer_id==0)?tep_href_link(FILENAME_CREATE_ACCOUNT, 'guest=guest', 'SSL'):tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL')) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
              </tr>
			  <?php */ ?>
              <tr><!-- PWA BOF -->
                <td class="main"><?php echo '<b>' . HEADING_BILLING_ADDRESS . '</b> <a href="' . ((tep_session_is_registered('customer_is_guest'))?tep_href_link(FILENAME_CREATE_ACCOUNT, 'guest=guest', 'SSL'):tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL')) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
              </tr><!-- PWA EOF -->
              <tr>
                <td class="main"><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br>'); ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo '<b>' . HEADING_PAYMENT_METHOD . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
              </tr>
              <tr>
                <td class="main"><?php echo $order->info['payment_method']; ?></td>
              </tr>
            </table></td>
            <td width="70%" valign="top" align="right"><table border="0" cellspacing="0" cellpadding="2">
<?php
  if (MODULE_ORDER_TOTAL_INSTALLED) {
    $order_total_modules->process();
    echo $order_total_modules->output();
  }
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
  if (is_array($payment_modules->modules)) {
    if ($confirmation = $payment_modules->confirmation()) {
?>
      <tr bgcolor="#FFFFFF">
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td width="5" height="20" align="left" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo HEADING_PAYMENT_INFORMATION; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg" class="heading_background_img"><img src="images/template/infoboxbgR.jpg"></td>
      </tr>
      
        
<?php ///////////////////////////////////////////////////////////////////////////// ?>
<?php // Start Template Area - strip all HTML tags //////////////////////////////// ?>
<?php ///////////////////////////////////////////////////////////////////////////// ?>
          <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
            <tr class="infoBoxContents">
              <td>
                <table border="0" cellspacing="0" cellpadding="2">
<?php
			for ($i=0, $n=sizeof($confirmation['fields']); $i<$n; $i++) {
?>
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main"><?php echo $confirmation['fields'][$i]['title']; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main"><?php echo $confirmation['fields'][$i]['field']; ?></td>
                  </tr>
<?php
			}
?>
                </table>
              </td>
            </tr>
          </table>
		  <table border="0" width="100%" cellspacing="0" cellpadding="2" class="infoBoxl">

<?php
    }
  }
?>
      		<tr bgcolor="#FFFFFF">
		      <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
		    </tr>
<?php
  if (tep_not_null($order->info['comments'])) {
?>
		    <tr>
        	  <td class="main"><?php echo '<b>' . HEADING_ORDER_COMMENTS . '</b> <a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td>
		    </tr>
	        <tr bgcolor="#FFFFFF">
	          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	        </tr>
		    <tr bgcolor="#FFFFFF">
	          <td>
	            <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
	              <tr class="infoBoxContents">
	                <td>
	                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
		                <tr>
	                      <td class="main"><?php echo nl2br(tep_output_string_protected($order->info['comments'])) . tep_draw_hidden_field('comments', $order->info['comments']); ?></td>
	                    </tr>
	                  </table>
	                </td>
	              </tr>
	            </table>
	          </td>
		    </tr>
	      	<tr bgcolor="#FFFFFF">
	          <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	        </tr>
<?php
  }
?>
	        <tr bgcolor="#FFFFFF">
	          <td>
	            <table border="0" width="100%" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF">
	              <tr>
	                <td align="right">
<?php
  if (isset($$payment->form_action_url)) {
    $form_action_url = $$payment->form_action_url;
  } else {
    $form_action_url = tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL');
  }

  echo tep_draw_form('checkout_confirmation', $form_action_url, 'post');
// ################# Added CGV
  echo tep_draw_hidden_field('gv_redeem_code', $HTTP_POST_VARS['gv_redeem_code']); 
// ################# End Added CGV
  if (is_array($payment_modules->modules)) {
    echo $payment_modules->process_button();
  }

  echo tep_image_submit('button_confirm_order.gif', IMAGE_BUTTON_CONFIRM_ORDER) . '</form>' . "\n";
?>
	              </td>
	            </tr>
	          </table>
            </td>
	      </tr>
    	  <tr bgcolor="#FFFFFF">
	        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
	      </tr>
     <?php /* <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%" align="right"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_PAYMENT . '</a>'; ?></td>
            <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
          </tr>
        </table></td>
      </tr>*/?>
          </table>
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