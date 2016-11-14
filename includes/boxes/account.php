<?php
/*
  $Id: account.php,v 1.16 2003/06/10 18:26:33 hpdl Exp $

CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
?>
<!-- account //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  //$info_box_contents[] = array('text' => BOX_HEADING_ACCOUNT);

  new infoBoxHeading($info_box_contents, false, false);

  $account_string = '<div class="module_box_account_info" id="manage_account">
  <h4>'.BOX_HEADING_ACCOUNT.'</h4>
  <ul class="manage_account_side_menu">';
  
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ACCOUNT_EDIT, '', 'SSL') . '">' . TEXT_MY_ACCOUNT_INFORMATION . '</a></li>';
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ADDRESS_BOOK, '', 'SSL') . '">' . TEXT_MY_ACCOUNT_ADDRESS_BOOK . '</a></li>';
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ACCOUNT_PASSWORD, '', 'SSL') . '">' . TEXT_MY_ACCOUNT_PASSWORD . '</a></li>';
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL') . '">' . TEXT_MY_ORDERS_VIEW . '</a></li>';
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ACCOUNT_NEWSLETTERS, '', 'SSL') . '">' . TEXT_EMAIL_NOTIFICATIONS_NEWSLETTERS . '</a></li>';
  $account_string .= '<li><a href="' . tep_href_link(FILENAME_ACCOUNT_NOTIFICATIONS, '', 'SSL') . '">' . TEXT_EMAIL_NOTIFICATIONS_PRODUCTS . '</a></li>';
  $account_string .= '</ul></div>';
  
  $info_box_contents = array();
  
  $info_box_contents[] = array('text' => $account_string);

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- account_eof //-->