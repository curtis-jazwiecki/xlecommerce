<?php

/*

$Id: xsell_products.php, v1  2002/09/11

// adapted for Separate Pricing Per Customer v4 2005/02/24



  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

*/



require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_XSELL_PRODUCTS);

?>



<?php

if ($HTTP_GET_VARS['products_id']) {

$xsell_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_mediumimage, pd.products_name, p.products_tax_class_id, products_price from " . TABLE_PRODUCTS_XSELL . " xp, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where xp.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and xp.xsell_id = p.products_id and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' and (p.parent_products_model IS NULL or p.parent_products_model='') order by sort_order asc limit " . MAX_DISPLAY_ALSO_PURCHASED);



// EOF Separate Pricing Per Customer

$num_products_xsell = tep_db_num_rows($xsell_query);



// EOF Separate Pricing Per Customer

$num_products_xsell = tep_db_num_rows($xsell_query);

if ($num_products_xsell <= 0) {

    

	$cat_query = tep_db_query("select categories_id from " . TABLE_PRODUCTS_TO_CATEGORIES . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'");

	$cat = tep_db_fetch_array($cat_query);

	

	$xsell_query = tep_db_query("select distinct p.products_id, p.products_image, p.products_mediumimage, pd.products_name, p.products_tax_class_id, products_price from " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p2c.categories_id = '" . $cat['categories_id'] . "' and p2c.products_id = p.products_id and p.products_id != '" . (int)$HTTP_GET_VARS['products_id']  . "' and p.products_id = pd.products_id and pd.language_id = '" . $languages_id . "' and p.products_status = '1' and (p.parent_products_model IS NULL or p.parent_products_model='') order by rand() limit " . MAX_DISPLAY_RELATED_PRODUCT);

   $num_products_xsell = tep_db_num_rows($xsell_query);

}

?>

<!-- xsell_products //-->

<?php

if ('0' < $num_products_xsell) {

	if (MODULE_STS_DEFAULT_STATUS=='true' && MODULE_STS_TEMPLATE_FOLDER!='' && file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_01.php.html') ){

		$content = file_get_contents(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . MODULE_STS_TEMPLATE_FOLDER . '/blocks/infobox_01.php.html');

		$output = '';

		$header_bof = stripos($content, '<!--header_bof-->');

		$header_eof = stripos($content, '<!--header_eof-->');

		if ($header_bof!==false && $header_eof!==false){

			$header_exists = true;

			$header_content = substr( $content,  $header_bof, $header_eof - $header_bof );

			$header_content = substr( $header_content,  stripos( $header_content, '>' ) + 1 );

			$header_content = str_ireplace('$header', TEXT_XSELL_PRODUCTS, $header_content);

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

		

		while ($xsell = tep_db_fetch_array($xsell_query)) {
		  $xsell['products_image'] = (tep_not_null($xsell['products_mediumimage']) ? $xsell['products_mediumimage'] : $xsell['products_image']);

			$xsell['specials_new_products_price'] = tep_get_products_special_price($xsell['products_id']);



			if ($xsell['specials_new_products_price']) {

				$price =  '<s>' . $currencies->display_price($xsell['products_price'], tep_get_tax_rate($xsell['products_tax_class_id'])) . '</s><br>';

				$price .= '<span class="productSpecialPrice">' . $currencies->display_price($xsell['specials_new_products_price'], tep_get_tax_rate($xsell['products_tax_class_id'])) . '</span>';

			} else {

				$price =  $currencies->display_price($xsell['products_price'], tep_get_tax_rate($xsell['products_tax_class_id']));

			}

   

			if (tep_not_null($xsell['products_image'])) {

				$feed_status = is_xml_feed_product($xsell['products_id']);

				if ($feed_status) 

					$image = tep_small_image($xsell['products_image'], $xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

				else 

					$image = tep_image(DIR_WS_IMAGES . $xsell['products_image'], $xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

			}

			

			$link = tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $xsell['products_id']);

			$name = $xsell['products_name'];

			$entry = str_ireplace(

				array('$image', '$link', '$name', '$price'), 

				array($image, $link, $name, $price), 

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

?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">

<tr><td class="xcell">

<?php

     $info_box_contents = array();

     $info_box_contents[] = array('align' => 'left', 'text' => TEXT_XSELL_PRODUCTS);

     new contentBoxHeading($info_box_contents);



     $row = 0;

     $col = 0;

     $info_box_contents = array();

     while ($xsell = tep_db_fetch_array($xsell_query)) {

       $xsell['specials_new_products_price'] = tep_get_products_special_price($xsell['products_id']);



if ($xsell['specials_new_products_price']) {

     $xsell_price =  '<s>' . $currencies->display_price($xsell['products_price'], tep_get_tax_rate($xsell['products_tax_class_id'])) . '</s><br>';

     $xsell_price .= '<span class="productSpecialPrice">' . $currencies->display_price($xsell['specials_new_products_price'], tep_get_tax_rate($xsell['products_tax_class_id'])) . '</span>';

   } else {

     $xsell_price =  $currencies->display_price($xsell['products_price'], tep_get_tax_rate($xsell['products_tax_class_id']));

   }

   

   if (tep_not_null($xsell['products_image'])) {

    	$feed_status = is_xml_feed_product($xsell['products_id']);

  if ($feed_status) 

   $image = tep_small_image($xsell['products_image'], $xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

    else 

   $image = tep_image(DIR_WS_IMAGES . $xsell['products_image'], $xsell['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

   }

       $info_box_contents2[$row][$col] = array('align' => 'center',

                                              'params' => 'class="main" valign="top"',

                                              'text' => '<table cellpadding="0" width="100%" class="xcell"><tr><td class="xcellImage"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $xsell['products_id']) . '">' . $image . '</a></td><td class="main"><table width="100%" cellpadding="0" cellspacing="0" border="0"><tr><td><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $xsell['products_id']) . '"class="xcell"><b>' . $xsell['products_name'] .'</b></a></td></tr><tr><td class="xcellPrice">' . $xsell_price. '</td></tr></table></td></tr></table>');

       $col ++;

       if (0 < $col) {

         $col = 0;

         $row ++;

       }

     }

     new contentBox($info_box_contents2);

?>

</td></tr></table>

<?php

	}

    

?>

<!-- xsell_products_eof //-->

<?php



   }

 }

?>