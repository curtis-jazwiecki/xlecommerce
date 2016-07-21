<?php
/*
  $Id: popup_image.php,v 1.18 2003/06/05 23:26:23 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

  $products_query = tep_db_query("select pd.products_name, p.products_image, p.products_mediumimage, p.products_largeimage from " . TABLE_PRODUCTS . " p left join " . TABLE_PRODUCTS_DESCRIPTION . " pd on p.products_id = pd.products_id where p.products_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['pID'] . "' and pd.language_id = '" . (int)$languages_id . "'");
  $products = tep_db_fetch_array($products_query);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo $products['products_name']; ?></title>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<script language="javascript"><!--
var i=0;
function resize() {
  if (navigator.appName == 'Netscape') i=40;
  if (document.images[0]) window.resizeTo(document.images[0].width +30, document.images[0].height+120-i);
  self.focus();
}
//--></script>
</head>
<body onload="resize();">
<?php 
	$feed_status = is_xml_feed_product($HTTP_GET_VARS['pID']);
  if ($feed_status) 
    echo tep_large_image($products['products_largeimage'], $products['products_name']);
	 else 
	 echo tep_image(DIR_WS_IMAGES . ((tep_not_null($products['products_largeimage']))? $products['products_largeimage']:((tep_not_null($products['products_mediumimage'])) ? $products['products_mediumimage'] : $products['products_image'])), $products['products_name']);
	 ?>
</body>
</html>
<?php require('includes/application_bottom.php'); ?>
