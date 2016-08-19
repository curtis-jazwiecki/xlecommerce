<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
 /*
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td width="50%"><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5', $comments); ?></td>
  <td width="50%"><div class="finalProducts"></div><br><div style="float:right" class="orderTotals"><?php echo (MODULE_ORDER_TOTAL_INSTALLED ? '<table cellpadding="2" cellspacing="0" border="0">' . $order_total_modules->output() . '</table>' : '');?></div></td>
 </tr>
</table> 
*/ ?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td><?php echo tep_draw_textarea_field('comments', 'soft', '60', '5', $comments); ?></td>
</tr>
<tr>
  <td align="right"><div class="finalProducts"></div><br><div style="float:right" class="orderTotals"><?php echo (MODULE_ORDER_TOTAL_INSTALLED ? '<table cellpadding="2" cellspacing="0" border="0">' . $order_total_modules->output() . '</table>' : '');?></div></td>
 </tr>
</table> 
