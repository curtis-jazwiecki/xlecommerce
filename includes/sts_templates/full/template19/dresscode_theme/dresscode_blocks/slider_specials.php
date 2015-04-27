<?php
$products_specials_query = tep_db_query("select distinct s.products_id, s.specials_new_products_price, pd.products_name, p.products_image, p.products_price, p.products_tax_class_id, p.products_date_added from " . TABLE_SPECIALS . " s, " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where s.products_id = pd.products_id and pd.products_id = p.products_id and pd.language_id = '" . (int)$languages_id . "' and s.status = '1' order by s.specials_date_added desc ");
?>

<?php if (tep_db_num_rows($products_specials_query) < 1) { ?>
<div class="display_none">&nbsp;</div>
<?php } else {?>


<div id="specials_slider">
    <div id="carousel_specials" class="es-carousel-wrapper">
        <div class="es-carousel container">
            <ul>
                <?php
                $k = 0;
                while ($products_specials = tep_db_fetch_array($products_specials_query ) ) {
                    $products_specials['specials_new_products_price'] = tep_get_products_special_price($products_specials['products_id']);
                    $products_specials_price = '<s>' . $currencies->display_price($products_specials['products_price'], tep_get_tax_rate($products_specials['products_tax_class_id'])) . '</s>';
                    $products_specials_price .= '&nbsp;<span class="productSpecialPrice">' . $currencies->display_price($products_specials['specials_new_products_price'], tep_get_tax_rate($products_specials['products_tax_class_id'])) . '</span>';

                    $products_specials_price = '<span class="new_price">'.$products_specials_price.'</span>';

                    $realpath = DIR_FS_CATALOG.'/images/'. $products_specials['products_image'];	;

                    if(file_exists($realpath)) {

                        ?>

                        <li class="four columns product">


                            <div class="product-image-wrapper">

                                <span class="product_sticker sticker_onsale_top_left sticker_onsale_display"></span>


                                <a class="display_block" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_specials["products_id"]) ;?>';">

                                    <?php
                                    /* if product has big img */
                                    $current_product =  $products_specials['products_id'];
                                    $products_new_added_big_img_query = tep_db_query("select distinct pi.image, pi.products_id from " . TABLE_PRODUCTS_IMAGES . " pi where pi.products_id = '$current_product' order by pi.id ASC ");
                                    $products_new_added_big_img = tep_db_fetch_array($products_new_added_big_img_query);

                                    if (tep_not_null($products_new_added_big_img['image'])) {
                                        echo tep_image(DIR_WS_IMAGES . $products_specials['products_image'], $products_specials['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').
                                        '<div class="roll_over_img">'.tep_image(DIR_WS_IMAGES . $products_new_added_big_img['image'], $products_new_added_big_img['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').'</div>';
                                    }
                                    else {
                                        echo tep_image(DIR_WS_IMAGES . $products_specials['products_image'], $products_specials['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"');
                                    }
                                    ?>


                                    <div class="product-image-wrapper-hover"></div>

                                </a>



                            </div>



                            <div class="wrapper-hover">



                                <div class="product-price"><?php echo $products_specials_price; ?></div>



                                <div class="product-name">

                                    <div class="clearfix">

                                        <a class="icon_cart_title" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_specials["products_id"]) ;?>';">

                                            <?php echo et_short_text($products_specials['products_name'] , 40) ?>

                                        </a>

                                        <div class="icon_cart_rollover">

                                            <a onclick="location.href='<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_specials['products_id']) ?>';"></a>

                                        </div>

                                    </div>

                                </div>





                            </div>

                        </li>

                        <?php

                    }

                }

                ?>

            </ul>

        </div>

    </div>

</div>





<?php } ?>







