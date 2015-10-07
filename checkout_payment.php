<?php
/*
  $Id: checkout_payment.php,v 1.113 2003/06/29 23:03:27 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');
// MVS start 
  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT);  
// If a shipping method has not been selected for all vendors, redirect the customer to the shipping method selection page

  if (SELECT_VENDOR_SHIPPING == 'true'  && $cart->content_type != 'virtual') {  // This test only works under MVS
    if (!is_array ($shipping['vendor']) || count ($shipping['vendor']) != count ($cart->vendor_shipping)) { // No shipping selected or not all selected
      tep_redirect (tep_href_link (FILENAME_CHECKOUT_SHIPPING, 'error_message=' . ERROR_NO_SHIPPING_SELECTED, 'SSL'));
    }
  } 
// MVS end

    //BOF:one_page_checkout
  if (ONEPAGE_CHECKOUT_ENABLED == 'True'){
      tep_redirect(tep_href_link(FILENAME_CHECKOUT,tep_get_all_get_params(array(tep_session_name())), 'SSL'));
  }
    //EOF:one_page_checkout

// #################### Begin Added CGV JONYO ######################
if (tep_session_is_registered('cot_gv')) tep_session_unregister('cot_gv');  //added to reset whether a gift voucher is used or not on this order
// #################### End Added CGV JONYO ######################

// if the customer is not logged on, redirect them to the login page
  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

// if there is nothing in the customers cart, redirect them to the shopping cart page
  if ($cart->count_contents() < 1) {
    tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
  }

// if no shipping method has been selected, redirect the customer to the shipping method selection page
  if (!tep_session_is_registered('shipping')) {
    tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  }

// avoid hack attempts during the checkout procedure by checking the internal cartID
  if (isset($cart->cartID) && tep_session_is_registered('cartID')) {
    if ($cart->cartID != $cartID) {
      tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
    }
  }

// if we have been here before and are coming back get rid of the credit covers variable
// #################### Added CGV ######################
	if(tep_session_is_registered('credit_covers')) tep_session_unregister('credit_covers');  // CCGV Contribution
    if(tep_session_is_registered('cot_gv')) tep_session_unregister('cot_gv'); //CCGV
// #################### End Added CGV ######################

// Stock Check
  /*if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
    $products = $cart->get_products();
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if (tep_check_stock($products[$i]['id'], $products[$i]['quantity'])) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
        break;
      }
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
  if ( (STOCK_CHECK == 'true') && (STOCK_ALLOW_CHECKOUT != 'true') ) {
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
    for ($i=0, $n=sizeof($products); $i<$n; $i++) {
      if ($product_on_hand[$products[$i]['id']] < $products[$i]['quantity']) {
        tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));
        break;
      }
    }
  }
  // end Bundled Products

// #################### Begin Added CGV JONYO ######################
// #################### THIS MOD IS OPTIONAL! ######################


// load the selected shipping module
 require(DIR_WS_CLASSES . 'shipping.php');
 $shipping_modules = new shipping($shipping);
 
 $shipping_text = TEXT_SHIPPING_DAYS;
if (!tep_session_is_registered('shipping_text')) tep_session_register('shipping_text');

// #################### End Added CGV JONYO ######################
// #################### THIS MOD WAS OPTIONAL! ######################

// if no billing destination address was selected, use the customers own address as default
  if (!tep_session_is_registered('billto')) {
    tep_session_register('billto');
    $billto = $customer_default_address_id;
  } else {
// verify the selected billing address
    $check_address_query = tep_db_query("select count(*) as total from " . TABLE_ADDRESS_BOOK . " where customers_id = '" . (int)$customer_id . "' and address_book_id = '" . (int)$billto . "'");
    $check_address = tep_db_fetch_array($check_address_query);

    if ($check_address['total'] != '1') {
      $billto = $customer_default_address_id;
      if (tep_session_is_registered('payment')) tep_session_unregister('payment');
    }
  }

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order;
// #################### Added CGV ######################
  require(DIR_WS_CLASSES . 'order_total.php');//ICW ADDED FOR CREDIT CLASS SYSTEM
  $order_total_modules = new order_total;//ICW ADDED FOR CREDIT CLASS SYSTEM
  $order_total_modules->clear_posts(); // ADDED FOR CREDIT CLASS SYSTEM by Rigadin in v5.13
// #################### End Added CGV ######################


  if (tep_not_null($HTTP_POST_VARS['membership_code'])) {
    $membership_code = tep_db_prepare_input($HTTP_POST_VARS['membership_code']);
    if (preg_match("/^[0-9]+$/",$HTTP_POST_VARS['membership_code']) && strlen($membership_code) == 7) {    	
      if (!tep_session_is_registered('member_discount')) tep_session_register('member_discount');
      } else {
		tep_session_unregister('member_discount');
		tep_redirect(tep_href_link(FILENAME_CHECKOUT_PAYMENT,'payment_error=ot_memberdiscount&error=' . MODULE_ORDER_TOTAL_MEMBERDISCOUNT_ERROR,'SSL'));
	}
  }
  if (!tep_session_is_registered('comments')) tep_session_register('comments');

  $total_weight = $cart->show_weight();
  $total_count = $cart->count_contents();
// #################### Added CGV ######################
  $total_count = $cart->count_contents_virtual(); //ICW ADDED FOR CREDIT CLASS SYSTEM
// #################### End Added CGV ######################

// load all enabled payment modules
  require(DIR_WS_CLASSES . 'payment.php');
  $payment_modules = new payment;
// MVS
  //require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CHECKOUT_PAYMENT);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_CHECKOUT_PAYMENT, '', 'SSL'));
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
var selected;
<?php // #################### Added CGV ###################### ?>
var submitter = null;
function submitFunction() {
   submitter = 1;
   }
<?php // #################### End Added CGV ###################### ?>
function selectRowEffect(object, buttonSelect) {

  // #################### Begin Added CGV JONYO ######################
  if (!document.checkout_payment.payment[0].disabled){
  // #################### End Added CGV JONYO ######################
    if (!selected) {

    if (document.getElementById) {
      selected = document.getElementById('defaultSelected');
    } else {
      selected = document.all['defaultSelected'];
    }
  }

  if (selected) selected.className = 'moduleRow';
  object.className = 'moduleRowSelected';
  selected = object;

// one button is not an array
  if (document.checkout_payment.payment[0]) {
    document.checkout_payment.payment[buttonSelect].checked=true;
  } else {
    document.checkout_payment.payment.checked=true;
  }
// #################### Begin Added CGV JONYO ######################
  }
// #################### End Added CGV JONYO ######################
}

function rowOverEffect(object) {
  if (object.className == 'moduleRow') object.className = 'moduleRowOver';
}

function rowOutEffect(object) {
  if (object.className == 'moduleRowOver') object.className = 'moduleRow';
}

<?php // #################### Begin Added CGV JONYO ###################### ?>

<?php
if (MODULE_ORDER_TOTAL_INSTALLED)
	$temp=$order_total_modules->process();
	$temp=$temp[count($temp)-1];
	$temp=$temp['value'];

	$gv_query = tep_db_query("select amount from " . TABLE_COUPON_GV_CUSTOMER . " where customer_id = '" . $customer_id . "'");
	$gv_result = tep_db_fetch_array($gv_query);

if ($gv_result['amount']>=$temp){ $coversAll=true;

?>

function clearRadeos(){
document.checkout_payment.cot_gv.checked=!document.checkout_payment.cot_gv.checked;
for (counter = 0; counter < document.checkout_payment.payment.length; counter++)
{
// If a radio button has been selected it will return true
// (If not it will return false)
if (document.checkout_payment.cot_gv.checked){
document.checkout_payment.payment[counter].checked = false;
document.checkout_payment.payment[counter].disabled=true;
//document.checkout_payment.cot_gv.checked=false;
} else {
document.checkout_payment.payment[counter].disabled=false;
//document.checkout_payment.cot_gv.checked=true;
}
}
}<? } else { $coversAll=false;?>
function clearRadeos(){
document.checkout_payment.cot_gv.checked=!document.checkout_payment.cot_gv.checked;
}<? } ?>
<?php // #################### End Added CGV JONYO ###################### ?>

//--></script>

<?php // #################### Begin Added CGV JONYO ###################### ?>
<?php // echo $payment_modules->javascript_validation(); ?>
<?php echo $payment_modules->javascript_validation($coversAll); ?>
<?php // #################### End Added CGV JONYO ###################### ?>

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
    <td width="100%" valign="top">
<?php
// #################### Added CGV JONYO ######################
// echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); 
// #################### End Added CGV JONYO ######################
?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php //echo tep_image(DIR_WS_IMAGES . 'table_background_payment.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
	  <tr><td><table cellpadding="0" cellspacing="0" border="0" width="100%">
			  <tr><td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '">'
			. tep_image(DIR_WS_IMAGES . 'template/checkout_shipping.gif') . '</a>';?></td>
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_payment_active.gif'); ?></td>
			  	<td background="images/template/checkout_bg.gif" style="background-repeat:repeat-x"><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_confirmation.gif'); ?></td>
			  	<td><?php echo tep_image(DIR_WS_IMAGES . 'template/checkout_success.gif'); ?></td>
			  </tr>
			  </table>
	</td></tr>
<?php
  if (isset($HTTP_GET_VARS['payment_error']) && is_object(${$HTTP_GET_VARS['payment_error']}) && ($error = ${$HTTP_GET_VARS['payment_error']}->get_error())) {
?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="infoBoxHeading"><b><?php echo tep_output_string_protected($error['title']); ?></b></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxNotice">
          <tr class="infoBoxNoticeContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="100%" valign="top"><?php echo tep_output_string_protected($error['error']); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
<SCRIPT language="JavaScript" type="text/javascript">
<!--hide

function newwindow()
{
window.open('cvv_help.php','jav','width=500,height=550,resizable=no,toolbar=no,menubar=no,status=no');
}
//-->
</SCRIPT>
<?php // #################### Begin Added CGV JONYO ###################### ?>
<?php // #################### THIS MOD IS OPTIONAL! ###################### ?>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
         <tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo HEADING_PRODUCTS; ?></b><? // echo ' <a href="' . tep_href_link(FILENAME_SHOPPING_CART) . '"><span class="orderEdit">(' . TEXT_EDIT . ')</span></a>'; ?></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td></tr>
          <tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
    <tr class="infoBoxContents">
            <td>
     <table border="0" width="100%" cellspacing="0" cellpadding="2">
 <?php
 //}

 for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
   echo '          <tr>' . "\n" .
        '            <td width="10%" class="main" align="right" valign="top" width="30">' . $order->products[$i]['qty'] . ' x</td>' . "\n" .
        '            <td width="60%" class="main" valign="top">' . $order->products[$i]['name'];

   if (STOCK_CHECK == 'true') {
     echo tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
   }

   if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
     for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
       echo '<br><nobr><small> <i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
     }
   }

   echo '</td>' . "\n";

   if (sizeof($order->info['tax_groups']) > 1) echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '% </td>' . "\n";

   echo '            <td width="30%"class="main" align="right" valign="top">' . $currencies->display_price($order->products[$i]['final_price'], $order->products[$i]['tax'], $order->products[$i]['qty']) . ' </td>' . "\n" .
        '          </tr>' . "\n";
 }
 ?>
                <tr><td><?php echo '<a href="' . tep_href_link(FILENAME_SHOPPING_CART,'', 'SSL') . '">' . tep_image_button('button_edit_product.gif') . '</a>'; ?></td>
            <td COLSPAN="2" valign="top" align="right">
           <table border="0" cellspacing="0" cellpadding="3">
 <?php
 if (MODULE_ORDER_TOTAL_INSTALLED) {
   //$temp=$order_total_modules->process();
   echo $order_total_modules->output();
 }
 ?>
                    </table>
         </td>
          </tr>
      </table>
   </td>
          </tr>
        </table></td>
      </tr>
 <!--              </table></td>
             </tr-->
<?php // #################### End Added CGV JONYO ###################### ?>
<?php // #################### THIS MOD WAS OPTIONAL! ###################### ?>

      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
        	  <tr>
        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?><br /><div align="center"></div></td>      
      </tr>
          <tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo TABLE_HEADING_BILLING_ADDRESS; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td></tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                <td class="main" width="50%" valign="top"><?php echo TEXT_SELECTED_BILLING_DESTINATION; ?><br><br><?php echo '<a href="' . (($customer_id==0)?tep_href_link(FILENAME_CREATE_ACCOUNT, 'guest=guest', 'SSL'):tep_href_link(FILENAME_CHECKOUT_PAYMENT_ADDRESS, '', 'SSL')) . '">' . tep_image_button('button_change_address.gif', IMAGE_BUTTON_CHANGE_ADDRESS) . '</a>'; ?></td>
                <td align="right" width="50%" valign="top"><table border="0" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="main" align="center" valign="top"><b><?php echo TITLE_BILLING_ADDRESS; ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_south_east.gif'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                    <td class="main" valign="top"><?php echo tep_address_label($customer_id, $billto, true, ' ', '<br>'); ?></td>
                    <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
                  </tr>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left">
			<?php
// #################### End Added CGV JONYO ######################
  echo tep_draw_form('checkout_payment', tep_href_link(FILENAME_CHECKOUT_CONFIRMATION, '', 'SSL'), 'post', 'onsubmit="return check_form();"'); 
// #################### End Added CGV JONYO ######################
?>
<b><?php echo TABLE_HEADING_PAYMENT_METHOD; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td>
          </tr>
        </table></td>
<?php // #################### Added CGV ###################### 
  echo $order_total_modules->credit_selection();//ICW ADDED FOR CREDIT CLASS SYSTEM
 // #################### End Added CGV ###################### ?>
      </tr>
      <tr>
        <td>



<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $selection = $payment_modules->selection();

  if (sizeof($selection) > 1) {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="50%" valign="top"><?php echo TEXT_SELECT_PAYMENT_METHOD; ?></td>
                <td class="main" width="50%" valign="top" align="right"><b><?php echo TITLE_PLEASE_SELECT; ?></b><br><?php echo tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
  } else {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main" width="100%" colspan="2"><?php echo TEXT_ENTER_PAYMENT_INFORMATION; ?></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
  }

  $radio_buttons = 0;
  for ($i=0, $n=sizeof($selection); $i<$n; $i++) {
?>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="2"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
    if ( ($selection[$i]['id'] == $payment) || ($n == 1) ) {
      echo '                  <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    } else {
      echo '                  <tr class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ')">' . "\n";
    }
?>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="3"><b><?php echo $selection[$i]['module']; ?></b></td>
                    <td class="main" align="right">
<?php
    if (sizeof($selection) > 1) {
      echo tep_draw_radio_field('payment', $selection[$i]['id']);
    } else {
      echo tep_draw_hidden_field('payment', $selection[$i]['id']);
    }
?>
                    </td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
    if (isset($selection[$i]['error'])) {
?>
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td class="main" colspan="4"><?php echo $selection[$i]['error']; ?></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
    } elseif (isset($selection[$i]['fields']) && is_array($selection[$i]['fields'])) {
?>
                  <tr>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    <td colspan="4"><table border="0" cellspacing="0" cellpadding="2">
<?php
      for ($j=0, $n2=sizeof($selection[$i]['fields']); $j<$n2; $j++) {
?>
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo $selection[$i]['fields'][$j]['title']; ?></td>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo $selection[$i]['fields'][$j]['field']; ?></td>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
<?php
      }
?>
                    </table></td>
                    <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                  </tr>
<?php
    }
?>
                </table></td>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
<?php
    $radio_buttons++;
  }

 // #################### Begin Added CGV JONYO ######################

if (tep_session_is_registered('customer_id')) {
if ($gv_result['amount']>0){
  echo ' <tr><td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td><td colspan=2>' . "\n" .
  								' <table border="0" cellpadding="2" cellspacing="0" width="100%"><tr class="moduleRow" onmouseover="rowOverEffect(this)" onclick="clearRadeos()" onmouseout="rowOutEffect(this)" >' . "\n" .
                             '   <td width="10">' .  tep_draw_separator('pixel_trans.gif', '10', '1') .'</td><td class="main">' . $gv_result['text'];

  echo $order_total_modules->sub_credit_selection();
  }
}


 // #################### End Added CGV JONYO ######################

?>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <!-- Points/Rewards Module V2.1rc2a Redeemption box bof -->
<?php
  if ((USE_POINTS_SYSTEM == 'true') && (USE_REDEEM_SYSTEM == 'true')) {
	  echo points_selection();
	  if (tep_not_null(USE_REFERRAL_SYSTEM) && (tep_count_customer_orders() == 0)) {
		  echo referral_input();
	  }
  }
?>
<!-- Points/Rewards Module V2.1rc2a Redeemption box eof -->

      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
 <tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo TABLE_HEADING_COMMENTS; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td></tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBoxl">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td><div style="padding-right: 6px"><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5'); ?></div></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
     <?php /* <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td class="main"><b><?php echo TITLE_CONTINUE_CHECKOUT_PROCEDURE . '</b><br>' . TEXT_CONTINUE_CHECKOUT_PROCEDURE; ?></td>
                <td class="main" align="right"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>*/?>
	   <tr><td class="main" align="left"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td></tr>
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
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td><?php echo tep_image(DIR_WS_IMAGES . 'checkout_bullet.gif'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
              </tr>
            </table></td>
            <td width="25%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
            <td width="25%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '100%', '1'); ?></td>
                <td width="50%"><?php echo tep_draw_separator('pixel_silver.gif', '1', '5'); ?></td>
              </tr>
            </table></td>
          </tr>
          <tr>
            <td align="center" width="25%" class="checkoutBarFrom"><?php echo '<a href="' . tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL') . '" class="checkoutBarFrom">' . CHECKOUT_BAR_DELIVERY . '</a>'; ?></td>
            <td align="center" width="25%" class="checkoutBarCurrent"><?php echo CHECKOUT_BAR_PAYMENT; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_CONFIRMATION; ?></td>
            <td align="center" width="25%" class="checkoutBarTo"><?php echo CHECKOUT_BAR_FINISHED; ?></td>
          </tr>
        </table></td>
      </tr>*/?>
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
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
