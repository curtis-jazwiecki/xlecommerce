<?php

require ('includes/application_top.php');



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

        'product_quantity' => 0, 

    ); 

    //if ($all_filters_selected){

        if (count($temp)){

            //$query = "select products_model, products_price, products_tax_class_id, products_quantity, products_image, products_mediumimage, products_largeimage from products where products_id='" . (int)$temp[0] . "'";

            

			// modified on 19-10-2015 to include EAN number #start

			$query = "select products_model, products_price, products_tax_class_id, products_quantity, products_image, products_mediumimage, products_largeimage, upc_ean, min_acceptable_price from products left join products_extended on (products.products_id= products_extended.osc_products_id) where products_id='" . (int)$temp[0] . "'";

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

                $data[0]['image'] = ((tep_not_null($info['products_largeimage'])) ? $info['products_largeimage'] : ((tep_not_null($info['products_mediumimage'])) ? $info['products_mediumimage'] : $info['products_image']));
                
                if (strpos($data[0]['image'], 'http://') === false) {
                    $data[0]['image'] = DIR_WS_IMAGES . $data[0]['image'];
                }

				$data[0]['upc_ean'] = $info['upc_ean']; // added on 19-10-2015

				$data[0]['min_acceptable_price'] = $info['min_acceptable_price']; // added on 19-10-2015

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



echo $sts->template['json'] = json_encode($data);
exit;


require (DIR_WS_INCLUDES . 'application_bottom.php');

?>