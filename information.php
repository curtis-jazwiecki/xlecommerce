<?php
  /*
  Module: Information Pages Unlimited
          File date: 2007/02/17
          Based on the FAQ script of adgrafics
          Adjusted by Joeri Stegeman (joeri210 at yahoo.com), The Netherlands

 
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

  */
require('includes/application_top.php');

function get_page($url) {
	$url = str_replace(" ", "+", $url);
	$fp = fopen($url, 'r');
	$file = "";
	while ($data = fread($fp, 4096)) {
		$file .= $data;
	}
	fclose($fp);
	return $file;
}


$_GET = $HTTP_GET_VARS;

// Include current language file, if not exists, fall back to English (Why is this not a standard procedure in OsCommerce)
if(file_exists(DIR_WS_LANGUAGES . $language . '/' . FILENAME_INFORMATION)) {
	include(DIR_WS_LANGUAGES . $language . '/' . FILENAME_INFORMATION);
}
else {
	include(DIR_WS_LANGUAGES . 'english/' . FILENAME_INFORMATION);
}

// Added for information pages
function information_page_not_found() {
	global $title, $breadcrumb, $description;
	$breadcrumb->add(INFORMATION_PAGE404_TITLE, tep_href_link(FILENAME_INFORMATION, 'info_id=' . $_GET['info_id'], 'NONSSL'));
	$title = INFORMATION_PAGE404_TITLE;
	$description = INFORMATION_PAGE404_DESCRIPTION;
}

if(!isset($_GET['info_id']) || !tep_not_null($_GET['info_id']) || !is_numeric($_GET['info_id']) ) 
{
	information_page_not_found();
} 
else 
{
	$info_id = intval($_GET['info_id']);
	$information_query = tep_db_query("SELECT information_title, information_description FROM " . TABLE_INFORMATION . " WHERE visible='1' AND information_id='" . (int)$info_id . "' AND language_id = '" . (int)$languages_id . "'");
	$information = tep_db_fetch_array($information_query);
	if(count($information) > 1) {
		$title = stripslashes($information['information_title']);
		$description = stripslashes($information['information_description']);

		// Added as noticed by infopages module
		if (!preg_match("/([\<])([^\>]{1,})*([\>])/i", $description)) 
		{
		  	$description = str_replace("\r\n", "<br>\r\n", $description); 
		}
	  	$breadcrumb->add($title, tep_href_link(FILENAME_INFORMATION, 'info_id=' . $info_id, 'NONSSL'));
	}
	else {
		information_page_not_found();
	} 
}


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
<base href="<?php echo (getenv('HTTPS') == 'on' ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<?php
// Begin Template Check
	$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
	$check = tep_db_fetch_array($check_query);

	echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/'.$check['configuration_value'].'/stylesheet.css">';
// End Template Check
?></head>
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
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading"><?php echo $title; ?></td>
            <td align="right"></td>
          </tr>
        </table></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main info_page_content"><BR>
		<?php 
	if (preg_match("/[CODE]/", $description)) {
		$description = htmlspecialchars_decode($description);
		$description = str_replace("[CODE]", "<?php ", $description);
		$description = str_replace("[/CODE]", "?>", $description);
		eval('?>' . $description . '<?php ');
	} else {
		echo $description; 
		}
		?></td>
			</tr>
	</table>
	
</td>
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