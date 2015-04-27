

<?php
$products_new_added_query = tep_db_query("select distinct pd.products_id, pd.products_name, pd.products_description, p.products_date_added, p.products_image, p.products_price, p.products_tax_class_id from " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_PRODUCTS . " p where p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' order by p.products_date_added DESC, pd.products_name limit 10");
?>

<?php if (tep_db_num_rows($products_new_added_query) < 1) {?>
    <div class="display_none">&nbsp;</div>
<?php } else {?>
	
	<div id="newproducts_slider">
        <div id="carousel_newproducts" class="es-carousel-wrapper">
        <div class="es-carousel container">
            <ul>
		  		<?php
		 		$k = 0;
				while ($products_new_added = tep_db_fetch_array($products_new_added_query) ) {

					$products_new_added['specials_new_products_price'] = tep_get_products_special_price($products_new_added['products_id']);

					if (tep_not_null($products_new_added['specials_new_products_price'])) {
                        $products_new_added_price = '<s>' . $currencies->display_price($products_new_added['products_price'], tep_get_tax_rate($products_new_added['products_tax_class_id'])) . '</s> <span>' . $currencies->display_price($products_new_added['specials_new_products_price'], tep_get_tax_rate($products_new_added['products_tax_class_id'])) . '</span>';
					} else {
                        $products_new_added_price = $currencies->display_price($products_new_added['products_price'], tep_get_tax_rate($products_new_added['products_tax_class_id']));
					}

                    $products_new_added_price = '<span class="new_price">'.$products_new_added_price.'</span>';


					$realpath = DIR_FS_CATALOG.'/images/'. $products_new_added['products_image'];

                    if(file_exists($realpath)) {
					?>
                    <li class="four columns product">




                        <div class="product-image-wrapper">

                            <span class="product_sticker sticker_onsale_top_left <?php echo (tep_not_null($products_new_added['specials_new_products_price']))? 'sticker_onsale_display' : 'sticker_onsale_display_none'; ?>"></span>

                            <a class="product_img" href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_added["products_id"]) ;?>" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_added["products_id"]) ;?>';">
                                <?php
                                    /* if product has big img */
                                $current_product =  $products_new_added['products_id'];
                                $products_new_added_big_img_query = tep_db_query("select distinct pi.image, pi.products_id from " . TABLE_PRODUCTS_IMAGES . " pi where pi.products_id = '$current_product' order by pi.id ASC ");
                                $products_new_added_big_img = tep_db_fetch_array($products_new_added_big_img_query);

                                if (tep_not_null($products_new_added_big_img['image'])) {
                                    echo tep_image(DIR_WS_IMAGES . $products_new_added['products_image'], $products_new_added['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').
                                    '<div class="roll_over_img">'.tep_image(DIR_WS_IMAGES . $products_new_added_big_img['image'], $products_new_added_big_img['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"').'</div>';
                                }
                                else {
                                    echo tep_image(DIR_WS_IMAGES . $products_new_added['products_image'], $products_new_added['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT, 'class="scale-with-grid-resize"');
                                }
                                ?>


                                <div class="product-image-wrapper-hover"></div>
                            </a>
                        </div>
                        <div class="wrapper-hover">
                            <div class="product-price"><?php echo $products_new_added_price; ?></div>
                            <div class="product-name">
                                <div class="clearfix">
                                    <a class="icon_cart_title" href="<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_added["products_id"]) ;?>" onclick="location.href='<?php echo tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_new_added["products_id"]) ;?>';">
                                        <?php echo et_short_text( $products_new_added['products_name'] , 40) ?>
                                    </a>
                                    <div class="icon_cart_rollover">
                                        <a onclick="location.href='<?php echo tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action')) . 'action=buy_now&products_id=' . $products_new_added['products_id']) ?>';"></a>
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





