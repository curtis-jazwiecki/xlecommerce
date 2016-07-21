<!--
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
-->
<div id="shippingAddress"><?php
 if (tep_session_is_registered('customer_id') && ONEPAGE_CHECKOUT_SHOW_ADDRESS_INPUT_FIELDS == 'False'){
	 if((int)$sendto<1)	 	$sendto = $billto;
	 echo tep_address_label($customer_id, $sendto, true, ' ', '<br>');
 }else{
	 if (tep_session_is_registered('onepage')){
		 $shippingAddress = $onepage['delivery'];
	 }
?>
<table border="0" width="100%" cellspacing="0" cellpadding="2">
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
	<td class="main" width="50%"><?php echo ENTRY_FIRST_NAME; ?></td>
	</tr>
   <tr>
	<td class="main" width="50%"><?php echo tep_draw_input_field('shipping_firstname', $shippingAddress['firstname'], 'class="required" style="width:80%;float:left;"'); ?></td>
	
   </tr>
   <tr>
	<td class="main" width="50%"><?php echo ENTRY_LAST_NAME; ?></td>
	</tr>
   <tr>
	<td class="main" width="50%"><?php echo tep_draw_input_field('shipping_lastname', $shippingAddress['lastname'], 'class="required" style="width:80%;float:left;"'); ?></td>
   </tr>
  </table></td>
 </tr>
<?php
  if (ACCOUNT_COMPANY == 'true') {
?>
 <tr>
  <td class="main"><?php echo ENTRY_COMPANY; ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_company', '', 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td class="main"><?php echo ENTRY_COUNTRY; ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_get_country_list('shipping_country', (isset($shippingAddress['country_id']) ? $shippingAddress['country_id'] : ONEPAGE_DEFAULT_COUNTRY), 'class="required" style="width:83%;float:left;"'); ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo ENTRY_STREET_ADDRESS; ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_street_address', $shippingAddress['street_address'], 'class="required" style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  if (ACCOUNT_SUBURB == 'true') {
?>
 <tr>
  <td class="main"><?php echo ENTRY_SUBURB; ?></td>
 </tr>
 <tr>
  <td class="main"><?php echo tep_draw_input_field('shipping_suburb', $shippingAddress['suburb'], 'style="width:80%;float:left;"'); ?></td>
 </tr>
<?php
  }
?>
 <tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
	<td class="main" width="33%"><?php echo ENTRY_CITY; ?></td>
	</tr>
	<tr>
	<td class="main" width="33%"><?php echo tep_draw_input_field('shipping_city', $shippingAddress['city'], 'class="required" style="width:80%;float:left;"'); ?></td></tr>
<?php
  if (ACCOUNT_STATE == 'true') {
?>
<tr>
	<td class="main" width="33%"><?php echo ENTRY_STATE; ?></td>
	</tr>
<?php
  }
  if (ACCOUNT_STATE == 'true') {
	$defaultCountry = (isset($shippingAddress) && tep_not_null($shippingAddress['country_id']) ? $shippingAddress['country_id'] : ONEPAGE_DEFAULT_COUNTRY);
?>
<tr>
	<td class="main" width="33%" id="stateCol_delivery"><?php echo $onePageCheckout->getAjaxStateField($defaultCountry, 'delivery');?>
	<div <?php if(tep_not_null($shippingAddress['zone_id']) || tep_not_null($shippingAddress['state'])){ ?>class= "success_icon ui-icon-green ui-icon-circle-check" <?php }else{?> class="required_icon ui-icon-red ui-icon-gear" <?php } ?> style="margin-left: 3px; margin-top: 1px; float: left;" title="Required" /></div>
	</td>
	</tr>
<?php
  }
?>
<?php
if(ONEPAGE_ZIP_BELOW == 'False'){
?>
<tr>
	<td class="main" width="33%"><?php echo ENTRY_POST_CODE; ?></td>
	</tr>
<?php
}
?>
   
  
	

<?php
if(ONEPAGE_ZIP_BELOW == 'False'){
?><tr>
	<td class="main" width="33%"><?php echo tep_draw_input_field('shipping_zipcode', $shippingAddress['postcode'], 'class="required" style="width:80%;float:left;"'); ?></td></tr>
<?php
}
?>
   
  </table></td>
 </tr>
  <?php
if(ONEPAGE_ZIP_BELOW == 'True'){
?>
<tr>
  <td><table cellpadding="0" cellspacing="0" border="0" width="100%">
   <tr>
<td class="main" width="50%">&nbsp;</td>
<td class="main"  width="50%" ><span style="float:left;"><?php echo ENTRY_POST_CODE.'&nbsp;&nbsp;' ?></span>
<?php echo tep_draw_input_field('shipping_zipcode', $shippingAddress['postcode'], 'class="required" style="width:42%;float:left;"'); ?>
</td>
</tr>
</table>
</td>
</tr>
<?php
}
?>
 
</table><?php
 }
?></div>