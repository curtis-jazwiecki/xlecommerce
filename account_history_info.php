<?php
/*
  $Id: account_history_info.php,v 1.100 2003/06/09 23:03:52 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

  if (!isset($HTTP_GET_VARS['order_id']) || (isset($HTTP_GET_VARS['order_id']) && !is_numeric($HTTP_GET_VARS['order_id']))) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }
  
  $customer_info_query = tep_db_query("select customers_id from " . TABLE_ORDERS . " where orders_id = '". (int)$HTTP_GET_VARS['order_id'] . "'");
  $customer_info = tep_db_fetch_array($customer_info_query);
  if ($customer_info['customers_id'] != $customer_id) {
    tep_redirect(tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ACCOUNT_HISTORY_INFO);

  $breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ACCOUNT, '', 'SSL'));
  $breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL'));
  $breadcrumb->add(sprintf(NAVBAR_TITLE_3, $HTTP_GET_VARS['order_id']), tep_href_link(FILENAME_ACCOUNT_HISTORY_INFO, 'order_id=' . $HTTP_GET_VARS['order_id'], 'SSL'));

  require(DIR_WS_CLASSES . 'order.php');
  $order = new order($HTTP_GET_VARS['order_id']);
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
<link rel="stylesheet" type="text/css" href="stylesheet.css">
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
<?php 
require(DIR_WS_INCLUDES . 'column_left.php');
$sts->template['heading_order_number'] = sprintf(HEADING_ORDER_NUMBER, $HTTP_GET_VARS['order_id']) . ' <small>(' . $order->info['orders_status'] . ')</small>'; 
$sts->template['heading_order_date'] = HEADING_ORDER_DATE . ' ' .tep_date_long($order->info['date_purchased']);
$sts->template['heading_order_total'] = HEADING_ORDER_TOTAL . ' ' . $order->info['total'];

?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
            <td class="pageHeading" align="right"><?php //echo tep_image(DIR_WS_IMAGES . 'table_background_history.gif', HEADING_TITLE, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
          <tr>
            <td class="infoBoxHeading" colspan="2"><b><?php echo sprintf(HEADING_ORDER_NUMBER, $HTTP_GET_VARS['order_id']) . ' <small>(' . $order->info['orders_status'] . ')</small>'; ?></b></td>
          </tr>
          <tr>
            <td class="smallText"><?php echo HEADING_ORDER_DATE . ' ' . tep_date_long($order->info['date_purchased']); ?></td>
            <td class="smallText" align="right"><?php echo HEADING_ORDER_TOTAL . ' ' . $order->info['total']; ?></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
<?php
  if ($order->delivery != false) {
    $sts->template['order_delivery'] =1;
    $sts->template['delivery_address_label'] = tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>');
?>
            <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo HEADING_DELIVERY_ADDRESS; ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo tep_address_format($order->delivery['format_id'], $order->delivery, 1, ' ', '<br>'); ?></td>
              </tr>
<?php
//MVS start
    $orders_shipping_id = '';
    $check_new_vendor_data_query = tep_db_query ("select orders_shipping_id, 
                                                         orders_id, 
                                                         vendors_id, 
                                                         vendors_name, 
                                                         shipping_module, 
                                                         shipping_method, 
                                                         shipping_cost 
                                                  from " . TABLE_ORDERS_SHIPPING . " 
                                                  where orders_id = '" . (int) $order_id . "'
                                                ");
    while ($checked_data = tep_db_fetch_array ($check_new_vendor_data_query)) {
      $orders_shipping_id = $checked_data['orders_shipping_id'];
    }
//MVS end
    if (tep_not_null($order->info['shipping_method'])) {
        $sts->template['shipping_method'] = $order->info['shipping_method'];
?>
              <tr>
                <td class="main"><b><?php echo HEADING_SHIPPING_METHOD; ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo $order->info['shipping_method']; ?></td>
              </tr>
<?php
    }
?>
            </table></td>
<?php
  }
  $sts->template['order_delivery'] = $order->delivery;
  
?>
            <td width="<?php echo (($order->delivery != false) ? '70%' : '100%'); ?>" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
              <tr>
<?php
//MVS start
      if (tep_not_null ($orders_shipping_id)) {
        $sts->template['order_shipping_id'] =1;
        require_once (DIR_WS_INCLUDES . 'vendor_order_data.php');
        require_once (DIR_WS_INCLUDES . 'vendor_order_info.php');
      } else {
//MVS end 
?>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  if (sizeof($order->info['tax_groups']) > 1) {
    $sts->template['tax_groups'] = $order->info['tax_groups'];
?>
                  <tr>
                    <td class="main" colspan="2"><b><?php echo HEADING_PRODUCTS; ?></b></td>
                    <td class="smallText" align="right"><b><?php echo HEADING_TAX; ?></b></td>
                    <td class="smallText" align="right"><b><?php echo HEADING_TOTAL; ?></b></td>
                  </tr>
<?php
  } else {
?>
                  <tr>
                    <td class="main" colspan="3"><b><?php echo HEADING_PRODUCTS; ?></b></td>
                  </tr>
<?php
  }
$prods = array();
  for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
    $prods[$i]['qty'] = $order->products[$i]['qty'];
    $prods[$i]['name'] = $order->products[$i]['name'];
    echo '          <tr>' . "\n" .
         '            <td class="main" align="right" valign="top" width="30">' . $order->products[$i]['qty'] . '&nbsp;x</td>' . "\n" .
         '            <td class="main" valign="top">' . $order->products[$i]['name'];
    $attribu = array();
    if ( (isset($order->products[$i]['attributes'])) && (sizeof($order->products[$i]['attributes']) > 0) ) {
      for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
        $attribu[$j]['option'] = $order->products[$i]['attributes'][$j]['option'];
        $attribu[$j]['value'] = $order->products[$i]['attributes'][$j]['value'];
        echo '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>';
      }
    }
$prods[$i]['attributes'] = $attribu;
    echo '</td>' . "\n";

    if (sizeof($order->info['tax_groups']) > 1) {
        $prods[$i]['tax'] = tep_display_tax_value($order->products[$i]['tax']) . '%';
      echo '            <td class="main" valign="top" align="right">' . tep_display_tax_value($order->products[$i]['tax']) . '%</td>' . "\n";
    }
        $prods[$i]['price'] = $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']);
        
    echo '            <td class="main" align="right" valign="top">' . $currencies->format(tep_add_tax($order->products[$i]['final_price'], $order->products[$i]['tax']) * $order->products[$i]['qty'], true, $order->info['currency'], $order->info['currency_value']) . '</td>' . "\n" .
         '          </tr>' . "\n";
  }
  $sts->template['products']=$prods;
//MVS
  }
?>
                </table></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
      
      
      <?php
        $sts->template['billing_address_label'] = tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br>');
        $sts->template['payment_method'] = $order->info['payment_method'];
      ?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="infoBoxHeading"><b><?php echo HEADING_BILLING_INFORMATION; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td width="30%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td class="main"><b><?php echo HEADING_BILLING_ADDRESS; ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo tep_address_format($order->billing['format_id'], $order->billing, 1, ' ', '<br>'); ?></td>
              </tr>
              <tr>
                <td class="main"><b><?php echo HEADING_PAYMENT_METHOD; ?></b></td>
              </tr>
              <tr>
                <td class="main"><?php echo $order->info['payment_method']; ?></td>
              </tr>
            </table></td>
            <td width="70%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
$sts->template['order_totals'] = $order->totals; 
  for ($i=0, $n=sizeof($order->totals); $i<$n; $i++) {
    echo '              <tr>' . "\n" .
         '                <td class="main" align="right" width="100%">' . $order->totals[$i]['title'] . '</td>' . "\n" .
         '                <td class="main" align="right">' . $order->totals[$i]['text'] . '</td>' . "\n" .
         '              </tr>' . "\n";
  }
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="infoBoxHeading"><b><?php echo HEADING_ORDER_HISTORY; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $statuses_query = tep_db_query("select os.orders_status_name, osh.date_added, osh.comments from " . TABLE_ORDERS_STATUS . " os, " . TABLE_ORDERS_STATUS_HISTORY . " osh where osh.orders_id = '" . (int)$HTTP_GET_VARS['order_id'] . "' and osh.orders_status_id = os.orders_status_id and os.language_id = '" . (int)$languages_id . "' order by osh.date_added");
  $status = array();
  $i=0;
  while ($statuses = tep_db_fetch_array($statuses_query)) {
    $status[$i]['date'] =   tep_date_short($statuses['date_added']);
    $status[$i]['name'] = $statuses['orders_status_name'];
    $status[$i]['comments'] = (empty($statuses['comments']) ? '&nbsp;' : nl2br(tep_output_string_protected($statuses['comments'])));
    
    echo '              <tr>' . "\n" .
         '                <td class="main" valign="top" width="70">' . tep_date_short($statuses['date_added']) . '</td>' . "\n" .
         '                <td class="main" valign="top" width="70">' . $statuses['orders_status_name'] . '</td>' . "\n" .
         '                <td class="main" valign="top">' . (empty($statuses['comments']) ? '&nbsp;' : nl2br(tep_output_string_protected($statuses['comments']))) . '</td>' . "\n" .
         '              </tr>' . "\n";
  $i++;
  }
  $sts->template['status'] = $status;
?>
            </table></td>
          </tr>
        </table></td>
      </tr>
<?php
 /*Package tRACKING mOD */
 // if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
?>
<!-- Package Tracking Plus BEGIN -->
<?php
    if ($order->info['usps_track_num'] == NULL & $order->info['usps_track_num2'] == NULL & $order->info['ups_track_num'] == NULL & $order->info['ups_track_num2'] == NULL & $order->info['fedex_track_num'] == NULL & $order->info['fedex_track_num2'] == NULL & $order->info['dhl_track_num'] == NULL & $order->info['dhl_track_num2'] == NULL & $order->info['extra_track_num'] == NULL) {
        $sts->template['and_null'] =1;
        
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
}else if ($order->info['usps_track_num'] == NULL or $order->info['usps_track_num2'] == NULL or $order->info['ups_track_num'] == NULL or $order->info['ups_track_num2'] == NULL or $order->info['fedex_track_num'] == NULL or $order->info['fedex_track_num2'] == NULL or $order->info['dhl_track_num'] == NULL or $order->info['dhl_track_num2'] == NULL or $order->info['extra_track_num'] == NULL) {
    $sts->template['or_null'] =1;
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="infoBoxHeading"><b><?php echo HEADING_TRACKING; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
      if ($order->info['usps_track_num1'] == NULL) {
        $sts->template['usps_num_present1']=0;
}else{
    $sts->template['usps_num_present1']=1;
    $sts->template['usps_track_num1'] = $order->info['usps_track_num1'];
?>
              <tr>
			    <td class="main" align="left">USPS(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><?php echo $order->info['usps_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
				
<?php
}
      if ($order->info['usps_track_num2'] == NULL) {
        $sts->template['usps_num_present2']=0;
}else{
    $sts->template['usps_num_present2']=1;
    $sts->template['usps_track_num2'] = $order->info['usps_track_num2'];
?>
              <tr>
			    <td class="main" align="left">USPS(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num2']; ?>"><?php echo $order->info['usps_track_num2']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num2']; ?>"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
              </tr>
<?php
}
      if ($order->info['ups_track_num'] == NULL) {
        $sts->template['ups_num_present']=0;
}else{
    $sts->template['ups_num_present']=1;
    $sts->template['ups_track_num'] = $order->info['ups_track_num'];
?>
              <tr>
			    <td class="main" align="left">UPS(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo $order->info['ups_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
}
      if ($order->info['ups_track_num2'] == NULL) {
        $sts->template['ups_num_present2']=0;
}else{
    $sts->template['ups_num_present2']=1;
    $sts->template['ups_track_num2'] = $order->info['ups_track_num2'];
?>
              <tr>
			    <td class="main" align="left">UPS(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num2']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num2']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
}
      if ($order->info['fedex_track_num'] == NULL) {
        $sts->template['fedex_num_present']=0;
}else{
    $sts->template['fedex_num_present']=1;
    $sts->template['fedex_track_num']=$order->info['fedex_track_num'];
?>
              <tr>
			    <td class="main" align="left">Fedex(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['fedex_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
}
      if ($order->info['fedex_track_num2'] == NULL) {
        $sts->template['fedex_num_present2']=0;
}else{
    $sts->template['fedex_num_present2']=1;
    $sts->template['fedex_track_num2']=$order->info['fedex_track_num2'];
?>
              <tr>
			    <td class="main" align="left">Fedex(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['fedex_track_num2']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num2']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
}
      if ($order->info['dhl_track_num'] == NULL) {
        $sts->template['dhl_num_present1']=0;
}else{
    $sts->template['dhl_num_present1']=1;
    $sts->template['dhl_track_num']=$order->info['dhl_track_num'];
?>
              <tr>
			    <td class="main" align="left">DHL(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['dhl_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
}
      if ($order->info['dhl_track_num2'] == NULL) {
        $sts->template['dhl_num_present2']=0;
}else{
    $sts->template['dhl_num_present2']=1;
    $sts->template['dhl_track_num2']=$order->info['dhl_track_num2'];
?>
              <tr>
			    <td class="main" align="left">DHL(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['dhl_track_num2']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num2']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
<?php
} 
 if ($order->info['extra_track_num'] == NULL) {
    $sts->template['extra_num_present']=0;
}else{
    $sts->template['extra_num_present']=1;
    $sts->template['extra_track_num']=$order->info['extra_track_num'];
?>
              <tr>
			    <td class="main" align="left"><?=EXTRA_TRACKING_NUMBER;?></td>
                <td class="main" align="left"><?php echo $order->info['extra_track_num']; ?></td>
             </tr>
<?php
}
?>

            </table></td>
          </tr></table>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
}else if ($order->info['usps_track_num'] != NULL & $order->info['usps_track_num2'] != NULL & $order->info['ups_track_num'] != NULL & $order->info['ups_track_num2'] != NULL & $order->info['fedex_track_num'] != NULL & $order->info['fedex_track_num2'] != NULL & $order->info['dhl_track_num'] != NULL & $order->info['dhl_track_num2'] != NULL & $order->info['extra_track_num'] != NULL) {
    $sts->template['no_null'] = 1;
    $sts->template['usps_track_num1'] = $order->info['usps_track_num1'];
$sts->template['usps_track_num2'] = $order->info['usps_track_num2'];
 $sts->template['ups_track_num'] = $order->info['ups_track_num'];
$sts->template['ups_track_num2'] = $order->info['ups_track_num2'];
$sts->template['fedex_track_num']=$order->info['fedex_track_num'];
$sts->template['fedex_track_num2']=$order->info['fedex_track_num2'];
$sts->template['dhl_track_num']=$order->info['dhl_track_num'];
$sts->template['dhl_track_num2']=$order->info['dhl_track_num2'];
$sts->template['extra_track_num']=$order->info['extra_track_num'];
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="infoBoxHeading"><b><?php echo HEADING_TRACKING; ?></b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
			    <td class="main" align="left">USPS(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><?php echo $order->info['usps_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
              <tr>
			    <td class="main" align="left">USPS(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num']; ?>"><?php echo $order->info['usps_track_num2']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://trkcnfrm1.smi.usps.com/PTSInternetWeb/InterLabelInquiry.do?origTrackNum=<?php echo $order->info['usps_track_num2']; ?>"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
              </tr>
			  <tr>
			    <td class="main" align="left">UPS(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo $order->info['ups_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
              <tr>
			    <td class="main" align="left">UPS(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num2']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><?php echo $order->info['ups_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://wwwapps.ups.com/etracking/tracking.cgi?InquiryNumber1=<?php echo $order->info['ups_track_num2']; ?>&InquiryNumber2=&InquiryNumber3=&InquiryNumber4=&InquiryNumber5=&TypeOfInquiryNumber=T&UPS_HTML_Version=3.0&IATA=us&Lang=en&submit=Track+Package"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
              </tr>
              <tr>
			    <td class="main" align="left">Fedex(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['fedex_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
			  </tr>
              <tr>
			    <td class="main" align="left">Fedex(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['fedex_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://www.fedex.com/Tracking?tracknumbers=<?php echo $order->info['fedex_track_num2']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
              </tr>
              <tr>
			    <td class="main" align="left">DHL(1):</td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['dhl_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
                </tr>
              <tr>
			    <td class="main" align="left">DHL(2):</td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num2']; ?>&action=track&language=english&cntry_code=us"><?php echo $order->info['dhl_track_num']; ?></a></td>
                <td class="main" align="left"><a target="_blank" href="http://track.dhl-usa.com/atrknav.asp?ShipmentNumber=<?php echo $order->info['dhl_track_num2']; ?>&action=track&language=english&cntry_code=us"><img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0"></a></td>
              </tr>
              <tr>
			    <td class="main" align="left"><?=EXTRA_TRACKING_NUMBER;?></td>
                <td class="main" align="left"><?php echo $order->info['extra_track_num'];?></td>                
              </tr>
            </table></td>
          </tr></table>
        </td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
}

	$tracking_data = getAllTrackingDetails((int)$HTTP_GET_VARS['order_id']);
	$sts->template['tracking_data'] = $tracking_data;
	if(count($tracking_data) > 0){ ?>
	
	  <tr>
        <td class="main"><b>Track Packages</b></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      
	
	
		
		 <tr>
         	<td>
            	<table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                <?php
		foreach($tracking_data as $tracking){?>
                
                <tr>
                    <td><?php echo $tracking['title']; ?></td>
                    <td>
                    <a target="_blank" href="<?php echo $tracking['link']; ?>">
                        <img src="<?=DIR_WS_LANGUAGES . $language . '/images/buttons/';?>button_track.gif" alt="Track Packages" border="0">
                    </a>
                    </td>
				</tr>	
                <?php
    } ?>
                </table>
            </td>
         </tr>
         
         
         
	<?php
    
	
    } 
	
	


$sts->template['download_enabled'] = DOWNLOAD_ENABLED;
  if (DOWNLOAD_ENABLED == 'true') include(DIR_WS_MODULES . 'downloads.php');
?>
<!-- Package Tracking Plus END -->

      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
          <tr class="infoBoxContents">
            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
              <tr>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <!-- // Points/Rewards Module V2.1rc2a history_back_bof  //-->
                <td><a href="javascript:history.go(-1)"><?php echo tep_image_button('button_back.gif', IMAGE_BUTTON_BACK); ?></a></td>
                <!-- // Points/Rewards Module V2.1rc2a history_back_eof //-->

                <?php /*<td><?php echo '<a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, tep_get_all_get_params(array('order_id')), 'SSL') . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>*/?>
                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
              </tr>
            </table></td>
          </tr>
        </table></td>
      </tr>
    </table></td>
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
