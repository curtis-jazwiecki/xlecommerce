<?php

$x=1;

$counter= 1;

$counter2= 1;

$ban = array();

$banners_demo = tep_db_query("select * from " . TABLE_BANNERS . " where banners_group = 'index' and template='" . (int)$template_id . "'");

if (tep_db_num_rows($banners_demo) == 0 ) {

    $default_banner1 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('1', 'products_new.php', 'slider_img1.jpg', 'index', now(), 1)");
    $default_banner2 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('2', 'products_new.php', 'slider_img2.jpg', 'index', now(), 1)");
    $default_banner3 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('3', 'products_new.php', 'slider_img3.jpg', 'index', now(), 1)");

    $default_banner4 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('4', 'products_new.php', 'slider_img4.jpg', 'index', now(), 1)");
    $default_banner5 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('5', 'products_new.php', 'slider_img5.jpg', 'index', now(), 1)");
    $default_banner6 = tep_db_query("insert into " . TABLE_BANNERS . " (banners_title,  banners_url, banners_image, banners_group, date_added, status) values ('6', 'products_new.php', 'slider_img6.jpg', 'index', now(), 1)");

}

$banners_demo_query = tep_db_query("select * from " . TABLE_BANNERS . " where banners_group = 'index' and template='19' order by banners_title");

if (tep_db_num_rows($banners_demo_query)) {

    while ($banners1 = tep_db_fetch_array($banners_demo_query)) {
        $ban[$counter] = array();
        $ban[$counter]['banners_id'] = $banners1['banners_id'];
        $ban[$counter]['banners_title'] = $banners1['banners_title'];
        $ban[$counter]['banners_url'] = $banners1['banners_url'];
        $ban[$counter]['banners_image'] = $banners1['banners_image'];
        $counter++;
    }
}


$ban_numb = tep_db_num_rows($banners_demo_query);


?>



<div id="slider_top">
    <ul class="rslides" id="carousel1">
    <?php
        $i = 1;
        while ($i < $ban_numb) {
    ?>
            <li>
                <div class="overlap_widget_wrapper">
                <?php
                    for ($j = $i; $j < $i + 2; $j++) {
                ?>
                        <div class="<?php echo (($j%2 == 0) ? 'right' : 'left'); ?>_image">
                            <div class="placeholder">
                                <?php if (tep_banner_exists('static', $ban[$j]['banners_id'])){ echo str_replace('_blank', '_self', tep_display_banner('static', $ban[$j]['banners_id'])); } ?>
                            </div>
                        </div>
                        <div class="clear"></div>
                <?php
                    }
                    $i+=2;
                ?>
                </div>
            </li>
    <?php
        }
    ?>

    </ul>
</div>



