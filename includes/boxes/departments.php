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
  $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'store_departments.jpg', 'store Departments'));

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('text' => tep_department_show_category());

  new columnBox($info_box_contents);
?>
            </td>
          </tr>
<!-- information_eof //-->
