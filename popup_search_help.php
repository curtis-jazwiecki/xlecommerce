<?php
/*
  $Id: popup_search_help.php,v 1.4 2003/06/05 23:26:23 hpdl Exp $

   CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  $navigation->remove_current_page();

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADVANCED_SEARCH);
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<title><?php echo TITLE; ?></title>
<?php
// Begin Template Check
	$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
	$check = tep_db_fetch_array($check_query);

	echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/'.$check['configuration_value'].'/stylesheet.css">';
// End Template Check
?></head>
<body marginwidth="10" marginheight="10" topmargin="10" bottommargin="10" leftmargin="10" rightmargin="10">
<?php
  //$info_box_contents = array();
  //$info_box_contents[] = array('text' => HEADING_SEARCH_HELP);

  //new infoBoxHeading($info_box_contents, true, true);

  //$info_box_contents = array();
  //$info_box_contents[] = array('text' => TEXT_SEARCH_HELP);

  //new infoBox($info_box_contents);
  echo HEADING_SEARCH_HELP;
  echo '<br><br>';
  echo TEXT_SEARCH_HELP;
?>

<p class="smallText" align="right"><?php echo '<a href="javascript:window.close()">' . TEXT_CLOSE_WINDOW . '</a>'; ?></p>

</body>
</html>
<?php require('includes/application_bottom.php'); ?>
