<?php
/*
  $Id: search.php,v 1.22 2003/02/10 22:31:05 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
?>
<!-- search //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'search.jpg', BOX_HEADING_MANUFACTURERS));

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('form' => tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get'),
                               'align' => 'center',
                               'text' =>'<table cellpadding="0" cellspacing="0" border="0" width="100%"><tr><td valign="top" class="style3">' . tep_draw_input_field('keywords', '', 'size="10" maxlength="30" style="width: ' . (BOX_WIDTH-30) . 'px"') . '<br><a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH) . '"><b>' . BOX_SEARCH_ADVANCED_SEARCH . '</b></a></td><td valign="middle">' . tep_hide_session_id() . tep_image_submit('button_quick_find.gif', BOX_HEADING_SEARCH) . '<br> </td></tr></table>');

  new columnBox($info_box_contents);
?>
            </td>
          </tr>
<!-- search_eof //-->
