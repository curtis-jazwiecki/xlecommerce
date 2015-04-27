<style>
    td.main > table + table > tbody > tr > td:first-child,
    #lightbox,body.product_info input[type="image"],
    input[type="image"][src="includes/languages/english/images/buttons/button_in_cart.gif"],
    input[type="image"][src="includes/languages/english/images/buttons/wishlist.gif"]
    {
        display: none;
    }
    select#attribute option , select#attribute {
        color: #000 !important;
    }    
    #cartdiv .skubutton {
        color: #000 !important;
        margin: auto;
    }
</style>             
<!--left_td-->
<td align="center" width="60%" valign="top">
    <table>
        <tr>
            <td>
                <table border="0" cellspacing="0" cellpadding="2" align="center" width="100%">
                    <tr>
                        <td align="center" class="smallText">
                            <span class="productName">DISPLAY_PRODUCT_NAME</span>
                        </td>
                    </tr>
                    <tr>
                        <td align="center" class="smallText">
                            DISPLAY_PRODUCT_IMAGE
                        </td>
                    </tr>
                    
                    <tr>
                        <td align="center" class="main">
                            <div id="cartdiv">

                                <p>DISPLAY_PRODUCT_MANUFACTURER</p>
                                <p>DISPLAY_PRODUCT_MODEL</p>                              

                                <p id="productPrice12">DISPLAY_CHILD_PRODUCT_PRICE</p>

                                <div>

                                    <table>DISPLAY_AVAILABILITY_N_PRICE</table>



                                </div>

                                <div>

                                    <p>DISPLAY_PRODUCT_OPTIONS_TITLE</p>

                                    DISPLAY_PRODUCT_ATTRIBUTE

                                </div>

                                <div>DISPLAY_PRODUCT_QUANTITY</div>

                                <div>DISPLAY_PRODUCT_DISCLAIMER</div>

                                <div>DISPLAY_PRODUCT_ADD_TO_CART <input type="submit" value="add to cart" class="addtocart_btn"></div>

                                <div>DISPLAY_PRODUCT_ADD_TO_WISHLIST<input type="submit" class="skubutton" name="wishlist_x" value="Add to Wishlist"></div>

                                <div id="sharelink">DISPLAY_PRODUCT_SHARE_LINK</div>

                            </div
                        </td>
                    </tr>
                    <tr>
                        <td valign="top">
                            <table border="0" width="100%" cellspacing="1" cellpadding="2" class="ProductInfoBox infoBox" >
                                <tr>
                                    <td valign="top" align="center">
                                        <table border="0"  cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td align="center" class="smallText" colspan="3">
                                                    DISPLAY_PRODUCT_DISCLAIMER
                                                </td>
                                            </tr>
                                            <tr>
                                                <td width="10"></td>
                                                <td class="main" align="center">
                                                    DISPLAY_PRODUCT_ADD_TO_CART
                                                </td>
                                                <td width="10"></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>                    
                </table>
            </td>
        </tr>
        <tr>
            <td valign="top" align="center">
                <table width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td valign="top" align="center">DISPLAY_PRODUCT_SPECIFICATIONS</td>
                    </tr>
                    <tr>
                        <td valign="top" align="center">
                            DISPLAY_PRODUCT_ATTRIBUTES
                        </td>
                    </tr>
                    <tr>
                        <td  width="100%" class="infoBoxHeading" align="center">Product Description</td>
                    </tr>
                    <tr>
                        <td width="100%" class="description" align="center" >
                            DISPLAY_PRODUCT_DESCRIPTION
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center"><strong>Prices for internet orders only. <br>Not all products available in store.</strong></td>
        </tr>
        <tr>
            <td>
                <table width="100%" align="center">
                    <tr>
                        <td class="main" align="center"><b>Overall Customer Rating:</b></td>
                    </tr>
                    <tr>
                        <td class="main" align="center">DISPLAY_PRODUCT_RATINGS</td>
                    </tr>
                    <tr>
                        <td align="center" class="main">DISPLAY_PRODUCT_WRITE_RATINGS</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</td>
<!--left_td_end-->