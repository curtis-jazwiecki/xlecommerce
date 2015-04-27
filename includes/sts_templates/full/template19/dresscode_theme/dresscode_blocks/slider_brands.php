<?php
    $manufacturers_query = tep_db_query("SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, count(p.products_id) as 'total_product' FROM manufacturers m, products p WHERE m.manufacturers_id = p.manufacturers_id and m.manufacturers_status = '1' and`products_status` = '1' group by p.manufacturers_id order by manufacturers_image desc, total_product desc limit 20");
    if (tep_db_num_rows($manufacturers_query) > 0) :
?>
    <div class="container">
            <div class="sixteen columns">
                <div class="infoBoxHeading infoBoxHeading-slider-brands"><?php echo BOX_HEADING_BRAND; ?></div>
                <div class="brands carouFredSel">
                    <div class="carouFredSel-controls">
                        <div class="carouFredSel-buttons">
                            <a id="brands-carousel-prev" class="prev"></a>
                            <a id="brands-carousel-next" class="next"></a>
                        </div>
                    </div>
                    <div class="brands-carousel">
                        <ul class="slides">
                            <?php while ($manufacturers = tep_db_fetch_array($manufacturers_query)) : ?>
                                <li>
                                    <a href="<?php echo tep_href_link(FILENAME_DEFAULT, 'manufacturers_id=' . $manufacturers['manufacturers_id']); ?>">
                                        <?php
                                            if(!empty($manufacturers['manufacturers_image'])) {
                                                echo tep_image(DIR_WS_IMAGES . $manufacturers['manufacturers_image'], $manufacturers['manufacturers_name'], '"', '', '');
                                             } else {
                                                echo $manufacturers['manufacturers_name'];
                                            }
                                        ?>
                                    </a>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    </div>
                </div>
            </div>
    </div>
<?php endif; ?>