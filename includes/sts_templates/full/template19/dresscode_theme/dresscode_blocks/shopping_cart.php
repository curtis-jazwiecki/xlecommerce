<div class="shopping_cart_b">
    <div class="cart_wrapper">
        <div class="clearfix">
            <a class="cart_link" href="<?php echo tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL'); ?>"><?php echo SHOPPING_CART; ?>:</a>
            <div class="open">
                 <span><?php echo $cart->count_contents(); ?><?php echo SOME_ITEMS_IN_CART; ?> - </span>
                 <span class="price"><?php echo $currencies->format($cart->show_total()); ?></span>
            </div>
        </div>
    </div>
</div>

<div id="shopping_cart_mini">

    <div class="inner-wrapper">

     <?php
    if ($cart->count_contents() > 0) {
        $cart_contents_string = '<table class="table_style">';
        $products = $cart->get_products();
        for ($i=0, $n=sizeof($products); $i<$n; $i++) {
            $cart_contents_string .= '<tr class="item"><td class="t_right v_align_top">';
            if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
                $cart_contents_string .= '<span class="newItemInCart">';
            }
            $cart_contents_string .= $products[$i]['quantity'] . '&nbsp;x&nbsp;';
            if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
                $cart_contents_string .= '</span>';
            }
            $cart_contents_string .= '</td><td class="v_align_top"><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products[$i]['id']) . '">';
            if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
                $cart_contents_string .= '<span class="newItemInCart">';
            }
            $cart_contents_string .= $products[$i]['name'];
            if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
                $cart_contents_string .= '</span>';
            }
            $cart_contents_string .= '</a></td></tr>';
            if ((tep_session_is_registered('new_products_id_in_cart')) && ($new_products_id_in_cart == $products[$i]['id'])) {
                tep_session_unregister('new_products_id_in_cart');
            }
        }
        $cart_contents_string .=
            '<tr>
                <td colspan="2" class="t_right td_style">
                    <div class="total">' .TEXT_CART_SUBTOTAL. '&nbsp;<span class="price">'.$currencies->format($cart->show_total()) . '</span></div>
                </td>
            </tr>
        </table>';

        $cart_contents_string .=
        '<div class="wrapper">
            <a class="button" title="View Cart" href="'.tep_href_link(FILENAME_SHOPPING_CART, '', 'SSL').'">'.VIEW_CART.'</a>
            <a class="button" title="Checkout" href="'.tep_href_link(FILENAME_CHECKOUT_SHIPPING, '', 'SSL').'">'.MENU_TEXT_CHECKOUT.'</a>
        </div>';

    } else {
        $cart_contents_string .= ' <p class="empty">'.ET_HEADER_NOW_IN_CART.'</p>';
    }
    echo $cart_contents_string;
    ?>


    </div>
</div>





















