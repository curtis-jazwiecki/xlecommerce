<div id="shoppingCart">	
<div class="block"><h2 class="margin">SHOPPING CART</h2>


	
	
	<div class="infoBox2">
	<table border="0" width="100%" cellspacing="0" cellpadding="2" >
		<tr>
			<!--<td class="smallText"><b><?php echo TABLE_HEADING_PRODUCTS_MODEL;?></b></td>-->
			<td width="60%" class="smallText"><b>
			<?php echo TABLE_HEADING_PRODUCTS_NAME;?></b></td>
			<td width="10%" class="smallText"><b><?php echo TABLE_HEADING_PRODUCTS_QTY;?></b></td>
			<td width="15%" class="smallText" align="right"><b><?php echo TABLE_HEADING_PRODUCTS_PRICE;?></b></td>
			<td width="15%" class="smallText" align="right"><b><?php echo TABLE_HEADING_PRODUCTS_FINAL_PRICE;?></b></td>
 		</tbody>
		<?php
 for ($i=0, $n=sizeof($order->products); $i<$n; $i++) {
	 $stockCheck = '';
	 if (STOCK_CHECK == 'true') {
		 $stockCheck = tep_check_stock($order->products[$i]['id'], $order->products[$i]['qty']);
	 }

	 $productAttributes = '';
	 if (isset($order->products[$i]['attributes']) && sizeof($order->products[$i]['attributes']) > 0) {
		 for ($j=0, $n2=sizeof($order->products[$i]['attributes']); $j<$n2; $j++) {
			 $productAttributes .= '<br><nobr><small>&nbsp;<i> - ' . $order->products[$i]['attributes'][$j]['option'] . ': ' . $order->products[$i]['attributes'][$j]['value'] . '</i></small></nobr>' . tep_draw_hidden_field('id[' . $order->products[$i]['id'] . '][' . $order->products[$i]['attributes'][$j]['option_id'] . ']', $order->products[$i]['attributes'][$j]['value_id']);

		 }
	 }
?> 
		<tr>
			<!--<td class="main" valign="top"><?php echo $order->products[$i]['model'];?></td>-->
			<td class="main" valign="top"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $order->products[$i]['id']) . '">'.$order -> products[$i]['name'] . $stockCheck . $productAttributes.'</a>';?></td>
			<td class="main" valign="top"><?php
			echo tep_draw_input_field('qty[' . $order -> products[$i]['id'] . ']', $order -> products[$i]['qty'], 'size="3" onkeyup="$(\'input[name^=qty]\').attr(\'readonly\', true); $(\'#updateCartButton\').trigger(\'click\')"');
			?></td>
			<td class="main" align="right" valign="top"><?php
			echo $currencies -> display_price($order -> products[$i]['final_price'], $order -> products[$i]['tax']);
			?></td>
			<td class="main" align="right" valign="top"><?php
			echo $currencies -> display_price($order -> products[$i]['final_price'], $order -> products[$i]['tax'], $order -> products[$i]['qty']);
			?></td>
			<td class="main" align="right" valign="top"><a linkData="action=removeProduct&pID=<?php echo $order -> products[$i]['id'];?>" class="removeFromCart" href="Javascript:void();" ><img border="0" src="<?php echo DIR_WS_IMAGES;?>icons/cross.gif"></a></td>
		</tr>
		<?php
		}
		?>
	</table>
	</div>
</div>

</div>