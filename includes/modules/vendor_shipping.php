<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$cart->vendor_shipping();
$vendor_shipping = $cart->vendor_shipping;
//echo '<pre>';
//print_r($vendor_shipping);
//Display a notice if we are shipping by multiple methods

if (count($vendor_shipping) >= 1) {?>

    <tr>
        <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox infoBox_text">

                <tr class="infoBoxContents">

                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

                            <tr>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                <td class="main"><?php echo TEXT_MULTIPLE_SHIPPING_METHODS; ?></td>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                            </tr>

                        </table></td>

                </tr>

            </table></td>

    </tr>

<?php
}



//Draw a selection box for each shipping_method
$always_show = '';
$_SESSION['always_show'] = '';
foreach ($vendor_shipping as $vendor_id => $vendor_data) {

    $total_weight = $vendor_data['weight'];

    $shipping_weight = $total_weight;

    $cost = $vendor_data['cost'];

    //$ship_tax = $shipping_tax;   //for taxes
	$ship_tax = 0;
    
	$total_count = $vendor_data['qty'];

	//  Much of the code from the top of the main page has been moved here, since
	//  it has to be executed for each vendor

    if (defined('MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING') && (MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING == 'true')) {

        $pass = false;

        switch (MODULE_ORDER_TOTAL_SHIPPING_DESTINATION) {

            case 'national':

                if ($order->delivery['country_id'] == STORE_COUNTRY) {

                    $pass = true;
                }

                break;

            case 'international':

                if ($order->delivery['country_id'] != STORE_COUNTRY) {

                    $pass = true;
                }

                break;

            case 'both':

                $pass = true;

                break;
        }



        $free_shipping = false;

        if (($pass == true) && ($order->info['total'] >= MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) {

            $free_shipping = true;

            include(DIR_WS_LANGUAGES . $language . '/modules/order_total/ot_shipping.php');
        }
	
	} else {
        $free_shipping = false;
    }

	//print "<br>Vendor_id in Shipping: " . $vendor_id;
	//Get the quotes array

    $quotes = $shipping_modules->quote('', '', $vendor_id);

	// Free shipping start
	// See if free.php is amoung them
	
    for ($i = 0, $n = count($quotes); $i < $n; $i++) {

        if (( ($quotes[$i]['id']) == 'free' ) && ($order->info['total'] >= $quotes[$i]['minpurchase'])) {

            $free_shipping = true;

            $minpurchase = $quotes[$i]['minpurchase'];
        }
    }

	// Free shipping end
	// if no shipping method has been selected, automatically select the cheapest method.
	// if the modules status was changed when none were available, to save on implementing
	// a javascript force-selection method, also automatically select the cheapest shipping
	// method if more than one module is now enabled

    if (!tep_session_is_registered('shipping') || ( tep_session_is_registered('shipping') && ($shipping == false) && (tep_count_shipping_modules() > 1) )) {

        $shipping = $shipping_modules->cheapest($vendor_id);
    }
    ?>

    <td><table border=0 width="100%" cellspacing="1" cellpadding="2" class="infoBox infoBox_text">

            <tr class="infoBoxContents">

                <td><table border=0 width="100%" cellspacing="0" cellpadding="2">

                        <tr>

                            <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                            <td class="main" width="50%" valign="top"><b><?php echo TEXT_PRODUCTS; ?></b></td>

                            <td class="main" width="50%" valign="top">&nbsp;</td>

                            <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                        </tr>

    <?php
    $products_ids = $vendor_data['products_id'];

    foreach ($products_ids as $product_id) {

        $products_query = tep_db_query("select products_name 

			                                  from " . TABLE_PRODUCTS_DESCRIPTION . " 

			                                  where products_id = '" . (int) $product_id . "' 

			                                    and language_id = '" . (int) $languages_id . "'"
        );

        $products = tep_db_fetch_array($products_query);
        ?>

                            <tr>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                <td class="main" width="50%" valign="top">

        <?php
        echo tep_draw_separator('pixel_trans.gif', '10', '1');

        echo $products['products_name'];
        ?>

                                </td>

                                <td class="main" width="50%" valign="top" align="right"><?php; ?></td>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                            </tr>

                            <?php
                        
	}//foreach
    ?>

        <tr>
            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
        </tr>

    <?php
    if (count($quotes) > 1) { ?>

                            <tr>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                <td class="main" width="50%" valign="top"><?php echo TEXT_CHOOSE_SHIPPING_METHOD; ?></td>

                                <td class="main" width="50%" valign="top" align="right"><?php echo '<b>' . TITLE_PLEASE_SELECT . '</b><br>' . tep_image(DIR_WS_IMAGES . 'arrow_east_south.gif'); ?></td>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                            </tr>

    <?php
    } elseif ($free_shipping == false) {
		 ?>

                            <tr>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                <td class="main" width="100%" colspan="2"><?php echo TEXT_ENTER_SHIPPING_INFORMATION; ?></td>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                            </tr>

    <?php
    
	}

    if ($free_shipping == true) {
		 ?>

                            <tr>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                <td colspan="2" width="100%"><table border="0" width="100%" cellspacing="0" cellpadding="2">

                                        <tr>

                                            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                            <td class="main" colspan="3"><b><?php echo FREE_SHIPPING_TITLE; ?></b>&nbsp;<?php echo $quotes[$i]['icon']; ?></td>

                                            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                        </tr>

                                        <tr id="defaultSelected" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, 0)">

                                            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                            <td class="main" width="100%"><?php echo sprintf(FREE_SHIPPING_DESCRIPTION, $currencies->format(MODULE_ORDER_TOTAL_SHIPPING_FREE_SHIPPING_OVER)) . tep_draw_hidden_field('shipping', 'free_free'); ?></td>

                                            <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                                        </tr>

                                    </table></td>

                                <td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 

                            </tr>

                            <?php
                        
	} else {
		
        $radio_buttons = 0;

		for ($i = 0, $n = count($quotes); $i < $n; $i++) {

			if (!isset($quotes[$i]['error'])) {?>
	
					<tr>
				<td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
				<td colspan="2">
                  <table border="0" width="100%" cellspacing="0" cellpadding="2">
                    <tr>
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      <td class="main" colspan="3"><b><?php echo $quotes[$i]['module']; ?></b>&nbsp;
                        <?php 
						if (isset($quotes[$i]['icon']) && tep_not_null($quotes[$i]['icon'])) {
							echo $quotes[$i]['icon'];
						} ?>
                       </td>
                      <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                    </tr>
                    <?php
						for ($j = 0, $n2 = sizeof($quotes[$i]['methods']); $j < $n2; $j++) {
							
								
								if (isset($shipping['vendor'][$vendor_id]['id'])) {
				
									$checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $shipping['vendor'][$vendor_id]['id']) ? true : false);
								} else {
				
									$checked = (($quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] == $shipping['id']) ? true : false);
								}
				
				// Start Javascript patch
				
								if (($checked == true) || ($n == 1 && $n2 == 1)) {
				
									echo ' <tr id="defaultSelected_' . $vendor_id . '_' . $radio_buttons . '" class="moduleRowSelected" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ', ' . $vendor_id . ')">' . "\n";
								} else {
				
									echo ' <tr id="defaultSelected_' . $vendor_id . '_' . $radio_buttons . '" class="moduleRow" onmouseover="rowOverEffect(this)" onmouseout="rowOutEffect(this)" onclick="selectRowEffect(this, ' . $radio_buttons . ', ' . $vendor_id . ')">' . "\n";
								}
								
								// End Javascript patch
								
								$shipping_actual_tax = $quotes[$i]['tax'] / 100;
				
								$shipping_tax = $shipping_actual_tax * $quotes[$i]['methods'][$j]['cost'];
												?>
						  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
						  <td class="main" width="75%"><?php echo $quotes[$i]['methods'][$j]['title']; ?></td>
						  <?php
								if (($n > 1) || ($n2 > 1)) {?>
									<td class="main"><?php echo $currencies->format($quotes[$i]['methods'][$j]['cost']); ?></td>
									<td class="main" align="right"><?php
									
									// Javascript patch
									
									
									$str_onepage_html = '';
									
									
									if(ONEPAGE_CHECKOUT_ENABLED == 'True'){
										$str_onepage_html =  'onePage.setShippingMethod($(this));onePage.updatePayment();onePage.updateOrderTotals();';
									}
									
									if( ($quotes[$i]['id'] == 'ffldealershipping') && ($checked)){
										$always_show = $vendor_id;
										$_SESSION['always_show'] = $vendor_id;
									}
										
									echo tep_draw_radio_field('shipping_' . $vendor_id, $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_' . $shipping_tax, $checked, 'id="shipping_radio_' . $radio_buttons . '_' . $vendor_id . '" onclick="'.$str_onepage_html.'"');
										
									echo tep_draw_hidden_field('products_' . $vendor_id, implode("_", $products_ids));
									
									
									
									?></td>
									
								<?php	
								} else {?>
						  			<td class="main"><?php echo $currencies->format($quotes[$i]['methods'][$j]['cost']) . tep_draw_hidden_field('shipping', $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_' . $shipping_tax); ?></td>
                                    <td class="main" align="right">
									
									<?php
										echo tep_draw_radio_field('shipping_' . $vendor_id, $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_' . $shipping_tax, $checked, 'id="shipping_radio_' . $radio_buttons . '_' . $vendor_id . '" ');
										//echo tep_draw_hidden_field('shipping_' . $vendor_id, $quotes[$i]['id'] . '_' . $quotes[$i]['methods'][$j]['id'] . '_' . $shipping_tax);
										echo tep_draw_hidden_field('products_' . $vendor_id, implode("_", $products_ids));?>
                                    </td>
						  <?php
															
								}
						  ?>
						  <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
						</tr>
			<?php
				$radio_buttons++;
			}?>
                  </table>
                </td>
	
				<td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td> 
	
			</tr>
            
            <?php
			
			if($quotes[$i]['id'] == 'ffldealershipping'){?>
			
            <tr>
            	<td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                <td colspan="2" id="selected_ffl_<?php echo $vendor_id; ?>" class="main"></td>
            </tr>
            
            
            
            <tr id="ffl_dealers_html_<?php echo $vendor_id; ?>" style="display:none;">
				<td><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
				<td colspan="2">
                	<iframe width="100%" height="800" src="ffl_dealers.php?vendors_id=<?php echo $vendor_id; ?>" id="ffl_dealers_iframe_<?php echo $vendor_id; ?>" scrolling="yes" frameborder="0"></iframe>
                </td>
            </tr>
			<?php
			if(sizeof($quotes) == 1){
				
				$always_show = $vendor_id;
				$_SESSION['always_show'] = $vendor_id;
			}
			
			} ?>
            
            <?php
			}
        }
	} ?>

    </table></td>
    </tr>
    </table></td>
    </tr>
    <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
    </tr>
	<?php
    }
?>

<script type="text/javascript">
function hidefflData(vendor_id){
	
	jQuery('#ffl_dealers_html_'+vendor_id).hide('slow');
	
	jQuery.ajax({
		url: 'ffl_dealers.php',
		type: 'post',
		data: "action=removeDealersDetails&datay="+vendor_id,
		dataType: 'html',
		beforeSend: function() {
		},
		success: function(response) {
			jQuery('#selected_ffl_'+vendor_id).html('');
		}
	});
}

jQuery(document).ready(function(e) {
	<?php
if (ONEPAGE_CHECKOUT_ENABLED == 'True') {
	if ( (isset($_SESSION['always_show'])) && ($_SESSION['always_show'] != '') ) { ?>
		showfflData(<?php echo $_SESSION['always_show']; ?>);
<?php 
	} 
}?>
    
});


function rowOverEffect(x){
}

function rowOutEffect(x){
}
</script>