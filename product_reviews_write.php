<?php
/*
  $Id: product_reviews_write.php,v 1.55 2003/06/20 14:25:58 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
  require('includes/application_top.php');

  if (!tep_session_is_registered('customer_id')) {
    $navigation->set_snapshot();
    tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
  }

	//Categories Status MOD by tech1@outdoorbusinessnetwork.com
  $product_info_query = tep_db_query("select p.products_id, p.products_model, p.products_image, p.products_price, p.products_tax_class_id, pd.products_name from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd, ".TABLE_PRODUCTS_TO_CATEGORIES." p2c, ".TABLE_CATEGORIES." c where c.categories_status = '1' and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and p.products_status = '1' and p.products_id = pd.products_id and p2c.products_id = p.products_id and c.categories_id = p2c.categories_id and pd.language_id = '" . (int)$languages_id . "'");
	//Categories Status MOD by tech1@outdoorbusinessnetwork.com
  if (!tep_db_num_rows($product_info_query)) {
    tep_redirect(tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('action'))));
  } else {
    $product_info = tep_db_fetch_array($product_info_query);
  }

  $customer_query = tep_db_query("select customers_firstname, customers_lastname, customers_nickname from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
  $customer = tep_db_fetch_array($customer_query);

  if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'process')) {
  	$nickname = tep_db_prepare_input($HTTP_POST_VARS['nickname']);
  	if (empty($nickname)){
		$nickname = tep_db_prepare_input($HTTP_POST_VARS['existing_nickname']);
	}
    $rating = tep_db_prepare_input($HTTP_POST_VARS['rating']);
    $review_title = tep_db_prepare_input($HTTP_POST_VARS['review_title']);
    $review = tep_db_prepare_input($HTTP_POST_VARS['review']);

    $error = false;
    if (strlen(trim($nickname)) < 3) {
      $error = true;

      $messageStack->add('review', 'Nickname either does not exists or less than 4 characters.');
    }
    
    if (strlen(trim($review_title)) ==0) {
      $error = true;

      $messageStack->add('review', 'Review title missing.');
    }
    
    if (strlen($review) < REVIEW_TEXT_MIN_LENGTH) {
      $error = true;

      $messageStack->add('review', JS_REVIEW_TEXT);
    }

    if (($rating < 1) || ($rating > 5)) {
      $error = true;

      $messageStack->add('review', JS_REVIEW_RATING);
    }

    if ($error == false) {
    	tep_db_query("update " . TABLE_CUSTOMERS . " set customers_nickname='" . trim($nickname) . "' where customers_id = '" . (int)$customer_id . "'");
      tep_db_query("insert into " . TABLE_REVIEWS . " (products_id, customers_id, customers_name, reviews_rating, date_added, reviews_title, customers_nickname) values ('" . (int)$HTTP_GET_VARS['products_id'] . "', '" . (int)$customer_id . "', '" . tep_db_input($customer['customers_firstname']) . ' ' . tep_db_input($customer['customers_lastname']) . "', '" . tep_db_input($rating) . "', now(), '" . trim($review_title) . "', '" . trim($nickname) . "')");
      $insert_id = tep_db_insert_id();

      $gettotalrating = tep_db_query("select sum(reviews_rating)/count(*) as rating from " . TABLE_REVIEWS . " where products_id='" . (int)$HTTP_GET_VARS['products_id'] . "'");
      $gettotalrating_info = tep_db_fetch_array($gettotalrating);
      $rating = ceil($gettotalrating_info['rating']);
      tep_db_query("update products set reviews_rating = '".$rating."' where products_id='" . (int)$HTTP_GET_VARS['products_id'] . "'");    
      tep_db_query("insert into " . TABLE_REVIEWS_DESCRIPTION . " (reviews_id, languages_id, reviews_text) values ('" . (int)$insert_id . "', '" . (int)$languages_id . "', '" . tep_db_input($review) . "')");
#### Points/Rewards Module V2.1rc2a BOF ####*/
    if ((USE_POINTS_SYSTEM == 'true') && (tep_not_null(USE_POINTS_FOR_REVIEWS))) {
	    $points_toadd = USE_POINTS_FOR_REVIEWS;
	    $comment = 'TEXT_DEFAULT_REVIEWS';
	    $points_type = 'RV';
	    tep_add_pending_points($customer_id, $product_info['products_id'], $points_toadd, $comment, $points_type);
    }
#### Points/Rewards Module V2.1rc2a EOF ####*/

      tep_redirect(tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('action'))));
    }
  }
  
  // BOF Separate Pricing per Customer
  if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
    $customer_group_id = $_SESSION['sppc_customer_group_id'];
  } else {
    $customer_group_id = '0';
  }

   if ($customer_group_id !='0') {
     $customer_group_price_query = tep_db_query("select customers_group_price from " . TABLE_PRODUCTS_GROUPS . " where products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "' and customers_group_id =  '" . $customer_group_id . "'");
     if ($customer_group_price = tep_db_fetch_array($customer_group_price_query)) {
	    $product_info['products_price'] = $customer_group_price['customers_group_price'];
     }
   }
// EOF Separate Pricing Per Customer


  if ($new_price = tep_get_products_special_price($product_info['products_id'])) {
    $products_price = '<s>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
  } else {
    $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
  }

  if (tep_not_null($product_info['products_model'])) {
    $products_name = $product_info['products_name'] . '<br><span class="smallText">[' . $product_info['products_model'] . ']</span>';
  } else {
    $products_name = $product_info['products_name'];
  }

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_REVIEWS_WRITE);

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params()));
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
?><script type="text/javascript" src="star_rating.js"></script>
<script language="javascript"><!--
function checkForm() {
  var error = 0;
  var error_message = "<?php echo JS_ERROR; ?>";

  var review = document.product_reviews_write.review.value;

  if (review.length < <?php echo REVIEW_TEXT_MIN_LENGTH; ?>) {
    error_message = error_message + "<?php echo JS_REVIEW_TEXT; ?>";
    error = 1;
  }

  if ((document.product_reviews_write.rating[0].checked) || (document.product_reviews_write.rating[1].checked) || (document.product_reviews_write.rating[2].checked) || (document.product_reviews_write.rating[3].checked) || (document.product_reviews_write.rating[4].checked)) {
  } else {
    error_message = error_message + "<?php echo JS_REVIEW_RATING; ?>";
    error = 1;
  }

  if (error == 1) {
    alert(error_message);
    return false;
  } else {
    return true;
  }
}

function popupWindow(url) {
  window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=no,resizable=yes,copyhistory=no,width=100,height=100,screenX=150,screenY=150,top=150,left=150')
}
//--></script>
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="3" cellpadding="3">
  <tr>
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- left_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
<!-- left_navigation_eof //-->
    </table></td>
<!-- body_text //-->
    <td width="100%" valign="top"><?php echo tep_draw_form('product_reviews_write', tep_href_link(FILENAME_PRODUCT_REVIEWS_WRITE, 'action=process&products_id=' . $HTTP_GET_VARS['products_id']), 'post', 'onSubmit="return checkForm();"'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td><table border="0" width="100%" cellspacing="0" cellpadding="0">
          <tr>
            <td class="pageHeading" valign="top"><?php echo $products_name; ?></td>
            <td class="pageHeading" align="right" valign="top"><?php echo $products_price; ?></td>
          </tr>
        </table></td>
      </tr>
<!-- // Points/Rewards Module V2.1rc2a bof //-->
<?php
  if ((USE_POINTS_SYSTEM == 'true') && (tep_not_null(USE_POINTS_FOR_REVIEWS))) {
?>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
      <tr>
        <td class="main"><?php echo sprintf(REVIEW_HELP_LINK, $currencies->format(tep_calc_shopping_pvalue(USE_POINTS_FOR_REVIEWS)), '<a href="' . tep_href_link(FILENAME_MY_POINTS_HELP,'faq_item=13', 'NONSSL') . '" title="' . BOX_INFORMATION_MY_POINTS_HELP . '">' . BOX_INFORMATION_MY_POINTS_HELP . '</a>'); ?></td>
      </tr>
<?php
  }
?>
<!-- // Points/Rewards Module V2.1rc2a eof //-->
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  if ($messageStack->size('review') > 0) {
?>
      <tr>
        <td><?php echo $messageStack->output('review'); ?></td>
      </tr>
      <tr>
        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      </tr>
<?php
  }
?>
      <tr>
        <td><table width="100%" border="0" cellspacing="0" cellpadding="2">
          <tr>
            <td valign="top">
				<table border="0" width="100%" cellspacing="0" cellpadding="2">
              		<tr>
                		<td class="main"><b>Reviewer Nickname</b></td>
                		<td class="main">&nbsp;:&nbsp;</td>
                		<td class="main" width="*">
							<input name="nickname" maxlength="25" <?php echo (!empty($customer['customers_nickname']) ? ' value="' . $customer['customers_nickname'] . '" disabled' : ''); ?> >
							<?php echo (!empty($customer['customers_nickname']) ? '<input type="hidden" name="existing_nickname" value="' . $customer['customers_nickname'] . '">' : ''); ?>							
						</td>
              		</tr>
              		<tr>
                		<td class="main" valign="top"><b><?php echo SUB_TITLE_RATING; ?></b></td>
                		<td class="main" valign="top">&nbsp;:&nbsp;</td>
                		<td class="main"  valign="middle">
							<?php //echo TEXT_BAD . ' ' . tep_draw_radio_field('rating', '1') . ' ' . tep_draw_radio_field('rating', '2') . ' ' . tep_draw_radio_field('rating', '3') . ' ' . tep_draw_radio_field('rating', '4') . ' ' . tep_draw_radio_field('rating', '5') . ' ' . TEXT_GOOD;;?>
<script type="text/javascript">
	loadStars();
</script>
<img src="images/star1.gif" onmouseover="highlight(this.id)" onclick="setStar(this.id)" onmouseout="losehighlight(this.id)" id="1" style="width:30px; height:30px; float:left;" />
<img src="images/star1.gif" onmouseover="highlight(this.id)" onclick="setStar(this.id)" onmouseout="losehighlight(this.id)" id="2" style="width:30px; height:30px; float:left;" />
<img src="images/star1.gif" onmouseover="highlight(this.id)" onclick="setStar(this.id)" onmouseout="losehighlight(this.id)" id="3" style="width:30px; height:30px; float:left;" />
<img src="images/star1.gif" onmouseover="highlight(this.id)" onclick="setStar(this.id)" onmouseout="losehighlight(this.id)" id="4" style="width:30px; height:30px; float:left;" />
<img src="images/star1.gif" onmouseover="highlight(this.id)" onclick="setStar(this.id)" onmouseout="losehighlight(this.id)" id="5" style="width:30px; height:30px; float:left;" />&nbsp;&nbsp;
<div id="vote" style="font-family:tahoma; color:red;display:inline;"></div>
<input type="hidden" name="rating" id="rating">
						</td>
              		</tr>
              		<tr>
                		<td class="main"><b>Review Title</b></td>
                		<td class="main">&nbsp;:&nbsp;</td>
                		<td class="main"><input name="review_title" size="50" maxlength="50"></td>
              		</tr>
              		<tr>
                		<td class="main" valign="top"><b><?php echo SUB_TITLE_REVIEW; ?></b></td>
                		<td class="main" valign="top">&nbsp;:&nbsp;</td>
                		<td class="main"><?php echo tep_draw_textarea_field('review', 'soft', 60, 15); ?></td>
              		</tr>
              		<tr>
                		<td class="main" colspan="3" align="right">
                			<?php echo TEXT_NO_HTML; ?>
						</td>
              		</tr>              		
              		<tr>
                		<td class="main" colspan="3">
                			<div style="display:inline;float:left;">
                				<?php echo '<a href="' . tep_href_link(FILENAME_PRODUCT_REVIEWS, tep_get_all_get_params(array('reviews_id', 'action'))) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
							</div>
                			<div style="display:inline;float:right;">
								<?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?>
							</div>
						</td>
              		</tr>
              	</table>
</td>
            <td width="<?php echo SMALL_IMAGE_WIDTH + 10; ?>" align="right" valign="top"><table border="0" cellspacing="0" cellpadding="2">
              <tr>
                <td align="center" class="smallText">
<?php
  if (tep_not_null($product_info['products_image'])) {
   $feed_status = is_xml_feed_product($product_info['products_id']);
  if ($feed_status) 
   $image = tep_small_image($product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
    else 
   $image = tep_image(DIR_WS_IMAGES . $product_info['products_image'], $product_info['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
?>
<script language="javascript">
document.write('<?php echo '<a href="javascript:popupWindow(\\\'' . tep_href_link(FILENAME_POPUP_IMAGE, 'pID=' . $product_info['products_id']) . '\\\')">' .$image . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>');
</script>
<noscript>
<?php echo '<a href="' . tep_href_link(DIR_WS_IMAGES . $product_info['products_image']) . '" target="_blank">' .$image . '<br>' . TEXT_CLICK_TO_ENLARGE . '</a>'; ?>
</noscript>
<?php
  }

  echo '<p><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now') . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART) . '</a></p>';
  //echo '<p><a href="' . tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now') . '">' . tep_image_button('button_in_cart.gif', IMAGE_BUTTON_IN_CART, 'id="add_to_cart"') . '<input type="button" value="add to cart" class="addtocart_btn"></a></p>';  
?>
                </td>
              </tr>
            </table>
          </td>
        </table></td>
      </tr>
    </table>
	</form></td>
<!-- body_text_eof //-->
    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
<!-- right_navigation //-->
<?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
<!-- right_navigation_eof //-->
    </table>
	</td>
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
