<table border="0" width="100%" cellspacing="0" cellpadding="0"><?php
  while ($banner = tep_catbanner_exists('dynamic', $cat_banners)) {
  	$cat_banners[] = $banner['banners_id'];
?>
  <tr>
    <td align="right" valign="top"><?php echo tep_display_banner('static', $banner); ?></td>
  </tr>
   <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
  ?>
 </table> 