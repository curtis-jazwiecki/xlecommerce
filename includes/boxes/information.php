<?php
/*
  $Id: information.php,v 1.6 2003/02/10 22:31:00 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
?>
<!-- information //-->
          <tr>
            <td>
<?php
 require_once(DIR_WS_FUNCTIONS . 'information.php');
  $info_box_contents = array();
  $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'information.jpg', BOX_HEADING_INFORMATION));

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => tep_information_show_category() .
// ###### Added CCGV Contribution #########
//                                         '<a href="' . tep_href_link(FILENAME_GV_FAQ, '', 'NONSSL') . '">' . BOX_INFORMATION_GV . '</a><br>' .
// ###### end CCGV Contribution #########
										 '<a href="' . tep_href_link(FILENAME_CONTACT_US) . '">' . BOX_INFORMATION_CONTACT . '</a><br/>'.
      '<a href="' . tep_href_link(FILENAME_MY_POINTS_HELP) . '">' . BOX_INFORMATION_MY_POINTS_HELP . '</a><br />'  // Points/Rewards Module V2.1rc2a
      );

  new columnBox($info_box_contents);
?>
            </td>
          </tr>
<!-- information_eof //-->
