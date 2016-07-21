<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require ('includes/application_top.php');
define('TEXT_PRODUCTS_BY_BUNDLE', 'This product contains the following items:');
define('TEXT_RATE_COSTS', 'Cost of separate parts:');
define('TEXT_IT_SAVE', 'You save');
define('TEXT_SOLD_IN_BUNDLE', 'This product may be purchased only as a part of the following bundle(s):');
function display_bundle($bundle_id, $bundle_price) {

   global $languages_id, $product_info, $currencies;
   $return_str = '';
   $return_str .= '<table border="0" width="95%" cellspacing="1" cellpadding="2" class="infoBox"> <tr class="infoBoxContents"> <td> <table border="0" width="100%" cellspacing="0" cellpadding="2"> <tr> <td class="main" colspan="5"><b>';
   $bundle_sum = 0;
   $return_str .= TEXT_PRODUCTS_BY_BUNDLE . "</b></td></tr>\n";
   
   $bundle_products = array(); 
   
   $bundle_query = tep_db_query("select pb.*, p.products_bundle, p.products_id, p.products_model, p.products_price, p.products_image, pd.products_name from products_bundles pb inner join products p on pb.subproduct_id=p.products_id inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='" . (int)$languages_id . "') where pb.bundle_id='" . (int)$bundle_id . "'");
   while ($bundle_data = tep_db_fetch_array($bundle_query)) {
     $return_str .= "<tr><td class=main valign=top style='padding-top:10px;'>";
     
	 $return_str .= '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $bundle_data['products_id']) . '" target="_blank">' . tep_small_image( $bundle_data['products_image'], $bundle_data['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="1" vspace="1"') . '</a></td>';

     // comment out the following line to hide the subproduct qty
     $return_str .= "<td class=main align=right><b>" . $bundle_data['subproduct_qty'] . "&nbsp;x&nbsp;</b></td>";

     $return_str .= '<td class=main><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, ($cPath ? 'cPath=' . $cPath . '&' : '') . 'products_id=' . $bundle_data['products_id']) . '" target="_blank"><b>&nbsp;(' . $bundle_data['products_model'] . ') ' . $bundle_data['products_name'] . '</b></a>';

     if ($bundle_data['products_bundle'] == "yes")
          display_bundle($bundle_data['subproduct_id'], $bundle_data['products_price']);
          $return_str .= '</td>';
          $return_str .= '<td align=right class=main><b>&nbsp;' . $currencies->display_price($bundle_data['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . "</b></td></tr>\n";
          $bundle_sum += $bundle_data['products_price'] * $bundle_data['subproduct_qty'];
	}

   $bundle_saving = $bundle_sum - $bundle_price;

   $bundle_sum = $currencies->display_price($bundle_sum, tep_get_tax_rate($product_info['products_tax_class_id']));

   $bundle_saving = $currencies->display_price($bundle_saving, tep_get_tax_rate($product_info['products_tax_class_id']));
   // comment out the following line to hide the "saving" text

    $return_str .= "<tr><td colspan=5 class=main><p><b>" . TEXT_RATE_COSTS . '&nbsp;' . $bundle_sum . '</b></td></tr><tr><td class=main colspan=5><font color="red"><b>' . TEXT_IT_SAVE . '&nbsp;' . $bundle_saving . "</font></b></td></tr>\n";

    $return_str .= '</table></td> </tr> </table>';

    return $return_str;

}



$data = array();
$stock_message = '';

if (isset($_POST['action'])){

    switch($_POST['action']){

        case 'set_initial_price':

            exit;

			$product_id = $_POST['product_id'];

            $query = "select p1.products_id, p1.products_model, p1.products_price, p1.products_tax_class_id, p1.products_quantity from products p1 inner join products p2 on p1.parent_products_model=p2.products_model where p2.products_id='" . (int)$product_id . "' and p1.products_status='1' limit 0, 1";

            $sql = tep_db_query($query);



            if (tep_db_num_rows($sql)){

                $info = tep_db_fetch_array($sql);



                $data[0]['product_id'] = $info['products_id'];

                $data[0]['product_model'] = $info['products_model']; 

                $data[0]['product_quantity'] = $info['products_quantity']; 

                $data[0]['product_price'] = $currencies->display_price($info['products_price'], tep_get_tax_rate($info['products_tax_class_id'])); 

                if (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'false') {

                    $stock_message = ($info['products_quantity'] > 0) ? 'In Stock' : STORE_STOCK_OUT_OF_STOCK_MESSAGE;

                } elseif (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'true') {

                    if ($info['products_quantity'] <= STORE_STOCK_LOW_INVENTORY_QUANTITY && $info['products_quantity'] > 0)

                        $stock_message = STORE_STOCK_LOW_INVENTORY_MESSAGE;

                    elseif ($info['products_quantity'] > STORE_STOCK_LOW_INVENTORY_QUANTITY)

                        $stock_message = 'In Stock';

                    else

                        $stock_message = STORE_STOCK_OUT_OF_STOCK_MESSAGE;

                }

                $data[0]['product_stock'] = $stock_message; 

                

                $options_query = tep_db_query("select distinct options_id from products_attributes where products_id='" . (int)$data[0]['product_id'] . "'");

                while($option = tep_db_fetch_array($options_query)){

                    $option_values_query = tep_db_query("select options_values_id from products_attributes where products_id='" . (int)$data[0]['product_id'] . "' and options_id='" . (int)$option['options_id'] . "' limit 0, 1");

                    if (tep_db_num_rows($option_values_query)){

                        $value = tep_db_fetch_array($option_values_query);

                        $data[0]['filters'][] = array('option' => $option['options_id'], 'value' => $value['options_values_id']);

                    }

                }

            }

            break;

    }

} else {

    $specs = array();

    $sql_part = '';

    $filtered_options = array();

    $product_id = $_POST['product_id'];

    $filters = explode('|', $_POST['filters']);

    $all_filters_selected = isset($_POST['all_filters_selected']) && $_POST['all_filters_selected']=='1' ? true : false;

    $modified_option_id = null;

    $modified_value_id = null;

    foreach($filters as $entry){

        list($option_id, $option_value_id) = explode('_', $entry);

        if (substr($option_id, 0, 1)=='*'){

            $option_id = substr($option_id, 1);

            $modified_option_id = $option_id;

            $modified_value_id = $option_value_id;

        }

        $specs[$option_id] = $option_value_id;

    }

    $temp = array();

    $temp1 = array();

    foreach($specs as $option_id => $option_value_id){

        $query = "select pa.products_id from products_attributes pa inner join products p1 on pa.products_id=p1.products_id inner join products p2 on p1.parent_products_model=p2.products_model where p2.products_id='" . (int)$product_id . "' " . (!empty($temp) ? " and p1.products_id in (" . implode(', ', $temp) . ") " : "") . " and options_id='" . (int)$option_id . "' and options_values_id='" . (int)$option_value_id . "'";

        $temp1 = $temp;

        $temp = array();

        $query_resultset = tep_db_query($query);

        while($entry = tep_db_fetch_array($query_resultset)){

            $temp[] = $entry['products_id'];

        }

        if (empty($temp)){

            $temp = $temp1;

            break;

        }

    }

    $data[] = array(

        'product_id' => 0, 

        'product_model' => '--', 

        //'product_price' => $currencies->display_price(0),

        'product_price' => '',

        'modified_option_id' => $modified_option_id, 

        'modified_value_id' => $modified_value_id, 

        'all_filters_selected' => $all_filters_selected,

        'filtered_options' => array(),

        'product_stock' => '', 
		
		'display_products_stock_availability' => '',

        'product_quantity' => 0, 

    ); 

    //if ($all_filters_selected){

        if (count($temp)){

            //$query = "select products_model, products_price, products_tax_class_id, products_quantity, products_image, products_mediumimage, products_largeimage from products where products_id='" . (int)$temp[0] . "'";

			// modified on 19-10-2015 to include EAN number #start

			$query = "select products_model, products_price, products_tax_class_id, products_quantity, products_image, products_mediumimage, products_largeimage, upc_ean, min_acceptable_price,products_bundle,store_quantity from products join products_extended on (products.products_id= products_extended.osc_products_id) where products_id='" . (int)$temp[0] . "'";

			// modified on 19-10-2015 to include EAN number #ends

			$sql = tep_db_query($query);

            if (tep_db_num_rows($sql)){

                $info = tep_db_fetch_array($sql);

                $data[0]['product_id'] = $temp[0];

                $data[0]['product_model'] = $info['products_model']; 

                $data[0]['product_price'] = $currencies->display_price($info['products_price'], tep_get_tax_rate($info['products_tax_class_id'])); 

                $data[0]['product_quantity'] = $info['products_quantity']; 

                if (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'false') {

                    $stock_message = ($info['products_quantity'] > 0) ? 'In Stock' : STORE_STOCK_OUT_OF_STOCK_MESSAGE;

                } elseif (STORE_STOCK == 'true' && STORE_STOCK_LOW_INVENTORY == 'true') {

                    if ($info['products_quantity'] <= STORE_STOCK_LOW_INVENTORY_QUANTITY && $info['products_quantity'] > 0)

                        $stock_message = STORE_STOCK_LOW_INVENTORY_MESSAGE;

                    elseif ($info['products_quantity'] > STORE_STOCK_LOW_INVENTORY_QUANTITY)

                        $stock_message = 'In Stock';

                    else

                        $stock_message = STORE_STOCK_OUT_OF_STOCK_MESSAGE;

                }

                $data[0]['product_stock'] = $stock_message; 
				
				// code added on 19-04-2016 #start
				 $data[0]['display_products_stock_availability'] = '';
				
				 if ($info['products_bundle'] != "yes") {
					// re-calculate stock
					$total_quantity = $info['products_quantity'] + $info['store_quantity'];
					
					recalculate_stock_status($data[0]['product_stock'],$total_quantity); 
				 }
				 
				 if($info['store_quantity'] > 0){
					$data[0]['display_products_stock_availability'] = 'In Store Availability: <b>In Stock</b>';
				 }else{
					$data[0]['display_products_stock_availability'] = 'In Store Availability: <b>'.STORE_STOCK_OUT_OF_STOCK_MESSAGE.'</b>';
				 }
				// code added on 19-04-2016 #ends
				
				
				
				
				

                $data[0]['image'] = ((tep_not_null($info['products_largeimage'])) ? $info['products_largeimage'] : ((tep_not_null($info['products_mediumimage'])) ? $info['products_mediumimage'] : $info['products_image']));
                
                if (strpos($data[0]['image'], 'http://') === false) {
                    $data[0]['image'] = DIR_WS_IMAGES . $data[0]['image'];
                }

				$data[0]['upc_ean'] = $info['upc_ean']; // added on 19-10-2015

				$data[0]['min_acceptable_price'] = $info['min_acceptable_price']; // added on 19-10-2015
				
				$data[1]['html'] = '';
				if ($info['products_bundle'] == "yes") {
					$data[1]['html'] = display_bundle($data[0]['product_id'], $info['products_price']);
				}

            }
			
			
			
			
			
			

        }

    //} else {

        //$query = "select distinct options_id, options_values_id from products_attributes where products_id in (" . implode(', ', $temp) . ")";

        /*$query = "select distinct options_id, options_values_id from products_attributes where products_id='" . (int)$temp[0] . "'";

        $options_query = tep_db_query($query);

        while($option = tep_db_fetch_array($options_query)){

            $temp_option_id = $option['options_id'];

            $temp_option_value_id = $option['options_values_id'];

            if (!array_key_exists($temp_option_id, $filtered_options)){

                $filtered_options[$temp_option_id] = array();

            }

            if (!in_array($temp_option_value_id, $filtered_options[$temp_option_id])){

                $filtered_options[$temp_option_id][] = $temp_option_value_id;

            }

        }



        foreach($filtered_options as $option => $values){

            $data[0]['filtered_options'][] = array('option' => $option, 'values' => $values);

        }*/

    }  

//}

$sts->template['json'] = json_encode($data);
require (DIR_WS_INCLUDES . 'application_bottom.php');
?>