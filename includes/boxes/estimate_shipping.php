<?php
/*
  $Id: estimate_shipping.php,v 1.1 2008/02/18 kymation Exp $

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
?>

<!-- information //-->
          <tr>
            <td>
<SCRIPT LANGUAGE="JavaScript">
  function estimatorpopupWindow(URL) {window.open(URL,'shippingestimator','toolbar=0,scrollbars=1,location=0,statusbar=0,menubar=0,resizable=1,width=800,height=600')}
</script>
<?php
  $info_box_contents = array();
  $info_box_contents[] = array('text' => 'Estimate Shipping');

  new infoBoxHeading($info_box_contents, false, false);
  
  $info_box_contents = array();
  $info_box_contents[] = array ('text' =>
      '<center><a href="javascript:estimatorpopupWindow(\''. tep_href_link ('ship_estimator.php', '', 'SSL') .'\')"><img src="' . tep_image_button('button_estimate_shipping.gif', IMAGE_BUTTON_SHIP_ESTIMATOR) . '" border="0"></a></center>'
                               );

  new infoBox($info_box_contents);
?>
            </td>
          </tr>
<!-- information_eof //-->
