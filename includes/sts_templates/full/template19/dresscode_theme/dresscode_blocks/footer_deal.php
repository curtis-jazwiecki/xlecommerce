<div class="four columns alpha" id="deal">

    <h3><?php echo MENU_TEXT_REVIEWS; ?></h3>

    <div class="product" style="position: relative;">





<?php
    $random_select = "select r.reviews_id, r.reviews_rating, r.reviews_read, p.products_id, p.products_price, p.products_image, pd.products_name from " . TABLE_REVIEWS . " r, " . TABLE_REVIEWS_DESCRIPTION . " rd, " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = r.products_id and r.reviews_id = rd.reviews_id and rd.languages_id = '" . (int)$languages_id . "' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and r.reviews_status = 1";
    if (isset($HTTP_GET_VARS['products_id'])) {
        $random_select .= " and p.products_id = '" . (int)$HTTP_GET_VARS['products_id'] . "'";
    }

    $random_select .= " order by r.reviews_id desc limit " . MAX_RANDOM_SELECT_REVIEWS;
    $random_product = tep_random_select($random_select);


    $reviews_box_contents = '';
      if ($random_product) {


          $rand_review_query = tep_db_query("select substring(reviews_text, 1, 1150) as reviews_text from " . TABLE_REVIEWS_DESCRIPTION . " where reviews_id = '" . (int)$random_product['reviews_id'] . "' and languages_id = '" . (int)$languages_id . "'");
          $rand_review = tep_db_fetch_array($rand_review_query);
          $rand_review_text = tep_break_string(tep_output_string_protected($rand_review['reviews_text']), 150, '-<br />');
          $rand_review_text_short = trimmed_text($rand_review_text, 60);
          $random_product['products_name'] = tep_get_products_name($random_product['products_id']);
          $random_product['specials_new_products_price'] = tep_get_products_special_price($random_product['products_id']);


          if (tep_not_null($random_product['specials_new_products_price'])) {
              $new_price = '<s>' . $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</s>&nbsp;';
              $new_price .= '<span class="productSpecialPrice">' . $currencies->display_price($random_product['specials_new_products_price'], tep_get_tax_rate($random_product['products_tax_class_id'])) . '</span>';
          } else {
              $new_price = $currencies->display_price($random_product['products_price'], tep_get_tax_rate($random_product['products_tax_class_id']));
          }

          $new_price = '<span class="new_price">'.$new_price.'</span>';

          $reviews_query =  tep_db_query("select count(*) as count from " . TABLE_REVIEWS . " where products_id = '" . $random_product['products_id'] . "' and reviews_status = 1");
          $reviews = tep_db_fetch_array($reviews_query);


          $reviews_box_contents .= '


          <div class="product-image-wrapper">

            <a class="img_border"  href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $random_product["products_id"]) . '" title="">
                '. tep_image(DIR_WS_IMAGES . $random_product['products_image'], $random_product['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid"').'
                <div class="product-image-wrapper-hover"></div>
            </a>


            <table style="width: 100%" class="wrapper-hover-hidden">
                <tr>
                    <td>
                        <div class="clearfix product-price">'.$new_price.'</div>
                        <div class="product-name">
                            <div class="clearfix">
                                <a href="'. tep_href_link(FILENAME_PRODUCT_REVIEWS_INFO, 'products_id=' . $random_product['products_id'] . '&reviews_id=' . $random_product['reviews_id']).'" title="">
                                    '.$rand_review_text_short.'
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            </table>

        </div>


	';

      } else {
          $reviews_box_contents .= '<div>'.MODULE_BOXES_REVIEWS_BOX_NO_REVIEWS.'</div>' ;
      }
        echo $reviews_box_contents;
?>


    </div>
</div>