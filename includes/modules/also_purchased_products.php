<?php
/*
  $Id: also_purchased_products.php,v 1.21 2003/02/12 23:55:58 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
//echo'<h1>abc111</h1>'; 
  //if (isset($HTTP_GET_VARS['products_id'])) {
if ( isset($HTTP_GET_VARS['products_id']) || !empty($cart_items) ) {
  	//Categories Status MOD by FIW
      
//echo "select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and opa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$HTTP_GET_VARS['products_id'] . "' and opb.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED;
//echo'<h1>abc222</h1>'; 
    //$orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and opa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$HTTP_GET_VARS['products_id'] . "' and opb.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    if ( !empty($cart_items) ){
        $orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and opa.products_id in (" . $cart_items . ") and opa.orders_id = opb.orders_id and opb.products_id not in (" . $cart_items . ") and opb.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    } else {
        $orders_query = tep_db_query("select p.products_id, p.products_image from " . TABLE_ORDERS_PRODUCTS . " opa, " . TABLE_ORDERS_PRODUCTS . " opb, " . TABLE_ORDERS . " o, " . TABLE_PRODUCTS . " p, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and opa.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and opa.orders_id = opb.orders_id and opb.products_id != '" . (int)$HTTP_GET_VARS['products_id'] . "' and opb.products_id = p.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and opb.orders_id = o.orders_id and p.products_status = '1' group by p.products_id order by o.date_purchased desc limit " . MAX_DISPLAY_ALSO_PURCHASED);
    }

    //Categories Status MOD by FIW
    $num_products_ordered = tep_db_num_rows($orders_query);
    if ($num_products_ordered >= MIN_DISPLAY_ALSO_PURCHASED) {
        if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_07.php.html') ){
            $content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_07.php.html');
            $output = '';
            $header_bof = stripos($content, '<!--header_bof-->');
            $header_eof = stripos($content, '<!--header_eof-->');
            if ($header_bof!==false && $header_eof!==false){
                    $header_exists = true;
                    $header_content = substr( $content,  $header_bof, $header_eof - $header_bof );
                    $header_content = substr( $header_content,  stripos( $header_content, '>' ) + 1 );
                    $header_content = str_ireplace('$header', 'Also Purchased', $header_content);
            } else {
                    $header_exists = false;
                    $header = '';
                    $header_content = '';
            }
            $output .= $header_content;
            
            $block_bof = stripos($content, '<!--block_bof-->');
            $block_eof = stripos($content, '<!--block_eof-->');
            if ($block_bof!==false && $block_eof!==false){
                    $block_exists = true;
                    $block_content = substr( $content,  $block_bof, $block_eof - $block_bof );
                    $block_content = substr( $block_content,  stripos( $block_content, '>' ) + 1 );
            } else {
                    $block_exists = false;
                    $block_content = '';
            }
            while ($orders = tep_db_fetch_array($orders_query)) {
                $link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']);
                $name = tep_get_products_name($orders['products_id']);
                $image = tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT);
                $entry = str_ireplace(
                        array('$image', '$link', '$name'), 
                        array($image, $link, $name), 
                        $block_content
                );
                $output .= $entry;
            }
            
            $footer_bof = stripos($content, '<!--footer_bof-->');
            $footer_eof = stripos($content, '<!--footer_eof-->');
            if ($footer_bof!==false && $footer_eof!==false){
                    $footer_exists = true;
                    $footer_content = substr( $content,  $footer_bof, $footer_eof - $footer_bof );
                    $footer_content = substr( $footer_content,  stripos( $footer_content, '>' ) + 1 );
                    $footer_content = str_ireplace('$footer', '', $footer_content);
                    $footer = '';
            } else {
                    $footer_exists = false;
                    $footer = '';
            }
            $output .= $footer_content;
            echo $output;
            
        } else {
      $info_box_contents = array();
      $info_box_contents[] = array('text' => TEXT_ALSO_PURCHASED_PRODUCTS);

      new contentBoxHeading($info_box_contents);

      $row = 0;
      $col = 0;
      $info_box_contents = array();
      while ($orders = tep_db_fetch_array($orders_query)) {
        $orders['products_name'] = tep_get_products_name($orders['products_id']);
        $info_box_contents[$row][$col] = array('align' => 'center',
                                               'params' => 'class="smallText" width="33%" valign="top"',
                                               'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">' . tep_image(DIR_WS_IMAGES . $orders['products_image'], $orders['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT) . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $orders['products_id']) . '">' . $orders['products_name'] . '</a>');

        $col ++;
        if ($col > 2) {
          $col = 0;
          $row ++;
        }
      }

      new contentBox($info_box_contents);
        }
       // echo'<h1>abc333</h1>'; 
?>
<!-- also_purchased_products //-->
<!-- also_purchased_products_eof //-->
<?php
    }
  }
?>
