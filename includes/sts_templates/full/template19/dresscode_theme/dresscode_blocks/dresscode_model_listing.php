<?php
ob_start();
?>

<div class="product">
    <div class="product-image-wrapper">
        <?php echo $product['sticker'] ?>
        <a class="product_img" href="<?php echo $product['name_url'] ?>">
            <?php echo $product['image'] ?>
            <div class="product-image-wrapper-hover"></div>
        </a>
    </div>
    <div class="wrapper-hover">
        <div class="product-price"><?php echo $product['price'] ?></div>
        <div class="product-name">
            <div class="clearfix">
                <a class="icon_cart_title" href="<?php echo $product['name_url'] ?>">
                    <?php echo trimmed_text($product['name'], 40) ?>
                </a>
                <div class="icon_cart_rollover">
                    <a href="<?php echo $product['cart_url'] ?>"></a>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
$col3 ++;
$col2 ++;
if ($col3 == 4 ) {$col3 = 1;}
if ($col2 == 3 ) {$col2 = 1;}

$dresscode_listing_output = ob_get_contents();
ob_end_clean();

?>