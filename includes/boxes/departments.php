<?php
/*
  $Id: information.php,v 1.6 2003/02/10 22:31:00 hpdl Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
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
