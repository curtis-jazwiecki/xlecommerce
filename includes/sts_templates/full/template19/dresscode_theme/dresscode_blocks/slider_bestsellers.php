<?php
$products_bestsellers_query = tep_db_query("select distinct pd.products_id, .pd.products_name, pd.products_description, p.products_id, p.products_image, p.products_price, p.products_tax_class_id from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where p.products_ordered > 0 and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_ordered desc, pd.products_name ");
?>



<?php if (tep_db_num_rows($products_bestsellers_query) < 1) { ?>

<div class="display_none">&nbsp;</div>

<?php } else {?>



<div id="bestsellers_slider">
    <div id="carousel_bestsellers" class="es-carousel-wrapper">

        <div class="es-carousel container">

        <ul>

                <?php

            while ($best_viewed = tep_db_fetch_array($products_bestsellers_query)) {
                $best_viewed['specials_new_products_price'] = tep_get_products_special_price($best_viewed['products_id']);

                if (tep_not_null($best_viewed['specials_new_products_price'])) {
                    $best_viewed_price = '<s>' . $currencies->display_price($best_viewed['products_price'], tep_get_tax_rate($best_viewed['products_tax_class_id'])) . '</s> <span>' . $currencies->display_price($best_viewed['specials_new_products_price'], tep_get_tax_rate($best_viewed['products_tax_class_id'])) . '</span>';
                } else {
                    $best_viewed_price = $currencies->display_price($best_viewed['products_price'], tep_get_tax_rate($best_viewed['products_tax_class_id']));
                }
                $best_viewed_price = '<span class="new_price">'.$best_viewed_price.'</span>';

                $realpath = DIR_FS_CATALOG.'/images/'. $best_viewed['products_image'];	;

                    if(file_exists($realpath)) {
                 ?>

                        <li class="four columns product"   >
                            <div class="product-image-wrapper">

                                <span class="product_sticker sticker_onsale_top_left <?php echo (tep_not_null($best_viewed['specials_new_products_price'])) ? 'sticker_onsale_display' : 'sticker_onsale_display_none'; ?>"></span>

                                <a class="display_block" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_viewed["products_id"]) ;?>';">

                                    <?php
                                    /* if product has big img */
                                    $current_product =  $best_viewed['products_id'];
                                    $products_new_added_big_img_query = tep_db_query("select distinct pi.image, pi.products_id from " . TABLE_PRODUCTS_IMAGES . " pi where pi.products_id = '$current_product' order by pi.id ASC ");
                                    $products_new_added_big_img = tep_db_fetch_array($products_new_added_big_img_query);

                                    if (tep_not_null($products_new_added_big_img['image'])) {
                                        echo tep_image(DIR_WS_IMAGES . $best_viewed['products_image'], $best_viewed['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').
                                        '<div class="roll_over_img">'.tep_image(DIR_WS_IMAGES . $products_new_added_big_img['image'], $products_new_added_big_img['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').'</div>';
                                    }
                                    else {
                                        echo tep_image(DIR_WS_IMAGES . $best_viewed['products_image'], $best_viewed['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"');
                                    }
                                    ?>

                                    <div class="product-image-wrapper-hover"></div>

                                </a>



                            </div>



                            <div class="wrapper-hover">



                                <div class="product-price"><?php echo $best_viewed_price; ?></div>



                                <div class="product-name">

                                    <div class="clearfix">

                                        <a class="icon_cart_title" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $best_viewed["products_id"]) ;?>';">

                                            <?php echo et_short_text($best_viewed['products_name'] , 40) ?>

                                        </a>

                                        <div class="icon_cart_rollover">

                                            <a onclick="location.href='<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $best_viewed['products_id']) ?>';"></a>

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







