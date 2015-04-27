<?php
/*
  $Id: footer.php,v 1.26 2003/02/10 22:30:54 hpdl Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/
// START STS 4.3
if ($sts->display_template_output) {
    echo'<h1>testing2</h1>';
} else {
//END STS 4.3

  require(DIR_WS_INCLUDES . 'counter.php');echo'<h1>testing</h1>';
?>
<table border="0" width="100%" cellspacing="0" cellpadding="1">
  <tr class="footer">
    <td class="footer">&nbsp;&nbsp;<?php echo strftime(DATE_FORMAT_LONG); ?>&nbsp;&nbsp;</td>
    <td align="right" class="footer">&nbsp;&nbsp;<?php echo $counter_now . ' ' . FOOTER_TEXT_REQUESTS_SINCE . ' ' . $counter_startdate_formatted; ?>&nbsp;&nbsp;</td>
  </tr>
</table>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center" class="smallText"><h1>testing</h1>
<?php
/*
  The following copyright announcement can only be
  appropriately modified or removed if the layout of
  the site theme has been modified to distinguish
  itself from the default osCommerce-copyrighted
  theme.

  For more information please read the following
  Frequently Asked Questions entry on the osCommerce
  support site:

  http://www.oscommerce.com/community.php/faq,26/q,50

  Please leave this comment intact together with the
  following copyright announcement.
*/

  //echo FOOTER_TEXT_BODY;
?>
    </td>
  </tr>
</table>
<?php
  if ($banner = tep_banner_exists('dynamic', '468x50')) {
?>
<br>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
  <tr>
    <td align="center"><?php echo tep_display_banner('static', $banner); ?></td>
  </tr>
</table>
<?php
  }
// START STS 4.1
}

if(checkmobile2())
{
  echo'<br><a href="'.HTTP_SERVER.'?disable_mobile_site=yes">View desktop site.</a>';  
}
if($_SESSION['disable_mobile_site']=='yes')
{
  unset($_SESSION['disable_mobile_site']);
  echo'<br><a href="'.HTTP_SERVER.'?disable_mobile_site=no">View mobile site.</a>';    
}

// END STS 4.1
?>
<?php

		 if ($request_type == 'SSL') {
		?>	
		 <script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript">
		 </script>
		 <script type="text/javascript">
		   _uacct="UA-<?php echo GOOGLE_ANLYTICS_ACCOUNT; ?>";
		   urchinTracker();
		 </script>
		<?php
		} else {
		?>
		 <script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
		 </script>
		 <script type="text/javascript">
		   _uacct="UA-<?php echo GOOGLE_ANLYTICS_ACCOUNT; ?>";
		   urchinTracker();
		 </script>
		<?php
		}
		?>
