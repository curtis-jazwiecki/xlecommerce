<script>    
    $(document).ready(function() {        
        $('td.childButton').each(function() {            
            $(this).children().eq(1).replaceWith('<input type="submit" value="add to cart" class="addtocart_btn">');        
        });    
    });
</script>
<div id="productinfo">    
    <div id="leftpart">        
        <h1>DISPLAY_PRODUCT_NAME</h1>        
        <div id="proimg">            
            <div id="mainImg">                
                DISPLAY_PRODUCT_IMAGE              
            </div>            
            <div id="moreImg">                
                DISPLAY_PRODUCT_EXTRA_IMAGE            
            </div>        
        </div>           
        <div id="pro_info">            
            <p class="itembg">Product Description</p>            
            <div class="prodetail">DISPLAY_PRODUCT_DESCRIPTION</div>        
        </div>        
        <div id="pro_spec">            
            <p class="itembg">Product Specifications</p>            
            <div class="prodetail">DISPLAY_PRODUCT_SPECIFICATIONS</div>        
        </div>        
        <div id="pro_review">            
            <p class="itembg">Product Reviews</p>            
            <div class="prodetail">DISPLAY_PRODUCT_RATINGS | DISPLAY_PRODUCT_WRITE_RATINGS</div>            
            <div class="prodetail">DISPLAY_PRODUCT_REVIEWS</div>        
        </div>        
        <div id="pro_package">            
            <p class="itembg">Product Package</p>            
            <div class="prodetail">DISPLAY_PACKAGE</div>        
        </div>    </div>    <div id="rightpart">        
            <div id="cartdiv">            
                <p>DISPLAY_PRODUCT_MANUFACTURER</p>            
                <p id="productItem12">DISPLAY_PRODUCT_MODEL</p>            
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
            </div>
            <div>DISPLAY_PRODUCT_RELATED_ITEMS</div>    
        </div>            
</div>  