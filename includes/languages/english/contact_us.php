<?php
/*
  $Id: contact_us.php,v 1.7 2002/11/19 01:48:08 dgw_ Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2002 osCommerce

  Released under the GNU General Public License
*/

define('HEADING_TITLE', 'Contact Us');
define('NAVBAR_TITLE', 'Contact Us');
define('TEXT_SUCCESS', 'Your enquiry has been successfully sent to the Store Owner.');
define('EMAIL_SUBJECT', 'Enquiry from ' . STORE_NAME);

// Get selected template
  $template_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
  $rows = tep_db_fetch_array($template_query);
  $selected_template = $rows['configuration_value'];
// Get selected template
  $product_listing_template_0 = file("includes/sts_templates/".$selected_template."/contact_us.php");
  $text_display = '';

  for($p=0;sizeof($product_listing_template_0) > $p; $p++)
	{
	  $text_display .= $product_listing_template_0[$p];
	}

define('ENTRY_NAME', $text_display);

unset($text_display);
define('ENTRY_ORDER_ID','Order ID (if applicable):');
define('SEND_TO_TEXT','Send Contact Form Email To:');
define('ENTRY_EMAIL', 'E-Mail Address:');
//define('CONTACT_US_LIST', '<option value="Comments and Complaints">Comments and Complaints</option><option value=" Donations"> Donations </option><option value="Orders and Inventory">Orders and Inventory</option>');
define('CONTACT_US_LIST', 'Comments and Complaints,Donations,Orders and Inventory');

define('ENTRY_ENQUIRY', 'Inquiry:');
?>