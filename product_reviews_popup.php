<?php
/*
  $Id: product_reviews.php,v 1.50 2003/06/09 23:03:55 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  require('includes/application_top.php');

  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and pd.language_id = '" . (int)$languages_id . "'");

  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link(FILENAME_REVIEWS));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }


  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br><span class="smallText">[' . $product_info['products_model'] . ']</span>';
  } else {
    $products_name = $product_info['products_name'];
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS);

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<?php
// BOF: Header Tag Controller v2.6.0
if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {
  require(DIR_WS_INCLUDES . 'header_tags.php');
} else {
?> 
  <title><?php echo TITLE; ?></title>
<?php
}
// EOF: Header Tag Controller v2.6.0
?>
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<?php
// Begin Template Check
	$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
	$check = tep_db_fetch_array($check_query);

	echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/'.$check['configuration_value'].'/stylesheet.css">';
// End Template Check
?></head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr><td>
			<table cellpadding="5" cellspacing="0" width="100%" border="0">
				<tr>
				<td align="left" class="smallText">
<?php
 if (tep_not_null($product_info['products_image'])) {
   $feed_status = is_xml_feed_product($product_info['products_id']);
  if ($feed_status) 
   $image = tep_small_image($product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');

 echo  $image ; 
  }
?>
 </td>
 <td valign="top" class="main"><table border="0" cellspacing="0" cellpadding="2" align="left">
            <tr>
              <td align="center" class="smallText">
					<?php echo tep_image(DIR_WS_IMAGES . 'template/logo_popup.gif') .  tep_draw_separator('pixel_trans.gif', '10', '1'); ?>
					 </td>
            </tr>
          </table><p><b><?php echo $product_info['products_name'];?></b></p></td>
					<td align="right"><a href="javascript:window.close()" style="color:#4b773c; text-decoration:underline;">Window Close</a></td></tr>
			</table></td></tr>

   
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top"><table border="0" width="100%" cellspacing="0" cellpadding="2">
<?php
  $reviews_query_raw = "select r.reviews_id, rd.reviews_text, r.reviews_rating, r.date_added, r.customers_name, r.reviews_title, r.customers_nickname, r.customers_id, ab.entry_city, ab.entry_state, ab.entry_country_id, ab.entry_zone_id from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, customers c, address_book ab where r.products_id = '" . (int)$product_info['products_id'] . "' and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and r.customers_id = c.customers_id and c.customers_default_address_id = ab.address_book_id order by r.reviews_id desc";
  $reviews_split = new splitPageResults($reviews_query_raw, MAX_DISPLAY_NEW_REVIEWS);

  if ($reviews_split->number_of_rows > 0) {
?>

  			  <tr>
			<td class="main" align="left"><b>Overall Customer Rating:</b></td>
		</tr>
		<tr>
			<td class="main" align="left">
				<?php
					$sql = tep_db_query("select sum(reviews_rating)/count(*) as rating from " . 
										TABLE_REVIEWS . " where products_id='" . (int)$HTTP_GET_VARS['products_id'] . "'");
					$sql_info = tep_db_fetch_array($sql);
					$rating = ceil($sql_info['rating']);
					if (!$rating){
						$rating = 1;
					}
					echo '<img src="images/stars_' . $rating . '.gif">&nbsp;' . number_format($rating, 1) . ' out of 5';
				?>
			</td>
		</tr>   <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
			  
			  <tr><td class="infoBoxHeading"><?php echo 'Customer Product Reviews';?></td></tr>
			
              <?php

    $reviews_query = tep_db_query($reviews_split->sql_query);
    while ($reviews = tep_db_fetch_array($reviews_query)) {
    	if ($reviews['customers_nickname'] != '') {
			$reviews['customers_name'] = $reviews['customers_nickname'];
		}
	$address = 	(($reviews['entry_city'] != '') ? $reviews['entry_city'] . ', ' : '');
	$country = tep_get_country_name($reviews['entry_country_id']);

      if (isset($reviews['entry_zone_id']) && tep_not_null($reviews['entry_zone_id'])) {
        $state = tep_get_zone_code($reviews['entry_country_id'], $reviews['entry_zone_id'], $reviews['entry_state']);
      }
    $address .= $state; 
	if ($reviews['reviews_title'] == '') {
		$reviews['reviews_title'] = $product_info['products_name'];
	} 
	
?>
              
  <tr>
    <td class="description"><table border="0" width="100%" cellspacing="0" cellpadding="0">
    <tr><td class="main"><i>
	<?php echo sprintf(TEXT_REVIEW_RATING, tep_image(DIR_WS_IMAGES . 'stars_' . $reviews['reviews_rating'] . '.gif', sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating'])), sprintf(TEXT_OF_5_STARS, $reviews['reviews_rating']));?>
	</i></td></tr>
        <tr><td class="main"><b><?php echo $reviews['reviews_title'] . '</b>, ' . tep_date_long($reviews['date_added']);?></td></tr>
       <tr><td class="main"><?php echo sprintf(TEXT_REVIEW_BY, tep_output_string_protected($reviews['customers_name'])) . ' from ' . $address; ?></td></tr> 
     <tr>
        <td valign="top" class="main"><?php echo tep_break_string(tep_output_string_protected($reviews['reviews_text']), 60, '-<br>') . ((strlen($reviews['reviews_text']) >= 100) ? '..' : ''); ?></td>
        </tr>
        <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
            </table></td>
                </tr>              
<?php
    }
?>

<?php
  } else {
?>
              <tr>
                <td><?php new infoBox(array(array('text' => TEXT_NO_REVIEWS))); ?></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }

  if (($reviews_split->number_of_rows > 0) && ((PREV_NEXT_BAR_LOCATION == '2') || (PREV_NEXT_BAR_LOCATION == '3'))) {
?>
              <tr>
                <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                  <tr>
                    <td class="smallText"><?php echo $reviews_split->display_count(TEXT_DISPLAY_NUMBER_OF_REVIEWS); ?></td>
                    <td align="right" class="smallText"><?php echo TEXT_RESULT_PAGE . ' ' . $reviews_split->display_links(MAX_DISPLAY_PAGE_LINKS, tep_get_all_get_params(array('page', 'info'))); ?></td>
                  </tr>
                </table></td>
              </tr>
              <tr>
                <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
              </tr>
<?php
  }
?>
          <?php   /*  <tr>
                <td><table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                  <tr class="infoBoxContents">
                    <td><table border="0" width="100%" cellspacing="0" cellpadding="2">
                      <tr>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                        <td class="main"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, tep_get_all_get_params()) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?></td>
                        <td class="main" align="right"><?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, tep_get_all_get_params()) . '">' . tep_image_button('button_write_review.gif', IMAGE_BUTTON_WRITE_REVIEW) . '</a>'; ?></td>
                        <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                      </tr>
                    </table></td>
                  </tr>
                </table></td>
              </tr> */?>
            </table></td>
           <?php /* <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="right" valign="top"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td align="center" class="smallText">
<?php
  if (tep_not_null($product_info['products_image'])) {
?>
<script language="javascript"><!--
document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\\\')">' . tep_image(DIR_WS_IMAGES . $product_info['products_image'], addslashes($product_info['products_name']), SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
//--></script>
<noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $product_info['products_image']) . '" target="_blank">' . tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'hspace="5" vspace="5"') . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
</noscript>
<?php
  }

  echo '<p><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now') . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a></p>';
?>
                </td>
              </tr>
            </table>
          </td> */?>
        </table></td>
      </tr>
    </table></td>
<!-- body_text_eof //-->
  <?php /*  <td width="<?php echo BOX_WIDTH; ?>" valign="top">
	<table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php //require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table></td>*/?>
  </tr>
  <tr><td align="center"><a href="javascript:window.close()" style="color:#4b773c; text-decoration:underline;">Window Close</a></td>
	</tr></td></tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php //require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
