function isTouchDevice() {
    return typeof window.ontouchstart != "undefined" ? true : false
}

function TopSlider(){
    var k1=0.5;
    var k2=0.6;
    var w0=jQuery(window).width();
    if(w0 > 1600) { k2=0.55}
    var w2= w0*k1;
    var w1= w0*k2;
    var w3= (w0-jQuery(".container").width())*0.5;
    var h1=w2/1.9;
    if(h1 < 235) {h1=235;}

    jQuery("#slider_top").css({"height":h1+"px"});
    jQuery("#slider_top").css({"width":w0+"px"});
    jQuery("#carousel1 li").css({"width":w0+"px"});
    jQuery(".overlap_widget_wrapper").css({"width":w0+"px"});

    jQuery("#slider_top a.callbacks_nav.next").css({"right":w3+"px","top":h1*0.5-jQuery("#slider_top a.callbacks_nav.next").height()*0.5+"px"});
    jQuery("#slider_top a.callbacks_nav.prev").css({"left":w3+"px","top":h1*0.5-jQuery("#slider_top a.callbacks_nav.next").height()*0.5+"px"});
    jQuery("#carousel1 .overlap_widget_wrapper .left_image .title").css({"left":w3+"px"});
    jQuery("#carousel1 .overlap_widget_wrapper .right_image .title").css({"right":w3+"px"});
    jQuery(".overlap_widget_wrapper .left_image").css({"width":w1+"px"});

    jQuery(".overlap_widget_wrapper .left_image .placeholder").css({"width":w2+"px"});

    jQuery(".overlap_widget_wrapper .right_image").css({"width":w1+"px"});
    jQuery(".overlap_widget_wrapper .right_image .placeholder").css({"width":w2+"px"});

    jQuery("#back-top").css({"bottom":jQuery("#footer").height()+150+"px"});

    var is_open = false;
    var z_index = 0;

    jQuery(".placeholder").mouseenter(function(){
        is_open = true;
        jQuery(this).parent().css({"zIndex":"999"});
        jQuery(this).stop().animate({
            "width":w1+"px"
        }, 550, 'easeOutQuad');
    });

    jQuery(".placeholder").mouseleave(function(){
        is_open = false;
        z_index++;
        jQuery(this).parent().css({"zIndex":z_index});
        jQuery(this).stop().animate({
            "width":w2+"px"
        }, 550, 'easeOutQuad');
    });
};



function validate() {
    var mail=/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/;
    if(!mail.test(document.index_newsletter.email_address.value)) {
        alert("Please Enter Your  Mail id Properly");
        document.index_newsletter.email_address.focus();
        return false;
    }
}


jQuery(function() {
    jQuery('#et_categ_box_scroll .cat-name').hover(
        function(){ jQuery('.drop-box-subcat',this).show() },
        function(){ jQuery('.drop-box-subcat',this).hide() }
    );
    jQuery('#et_categ_box_scroll .sub-cat-name').hover(
        function(){ jQuery('.drop-box-subsubcat',this).show() },
        function(){ jQuery('.drop-box-subsubcat',this).hide() }
    );
    jQuery('#et_categ_box_scroll .subsub-cat-name').hover(
        function(){ jQuery('.drop-box-3subcat',this).show() },
        function(){ jQuery('.drop-box-3subcat',this).hide() }
    );
    jQuery('#et_categ_box_scroll .3sub-cat-name').hover(
        function(){ jQuery('.drop-box-4subcat',this).show() },
        function(){ jQuery('.drop-box-4subcat',this).hide() }
    );


    var slide = false;
    jQuery('#footer_button').click(function() {
        jQuery('#footer_higher_content').slideToggle(500);
        jQuery('#footer_button').toggleClass('footer_button_up');
    });

    set_reset_tab=function (tab) {
        jQuery('#bestsellers_activate,#specials_activate,#newproducts_activate').removeClass('active_slider');
        tab.addClass('active_slider');
    }

    jQuery('#bestsellers_activate').click(function() {
        jQuery('#bestsellers_slider').show();
        jQuery('#newproducts_slider').hide();
        jQuery('#specials_slider').hide();
        set_reset_tab(jQuery(this));
    });

    jQuery('#specials_activate').click(function(){
        jQuery('#specials_slider').show();
        jQuery('#newproducts_slider').hide();
        jQuery('#bestsellers_slider').hide();
        set_reset_tab(jQuery(this));

    });

    jQuery('#newproducts_activate').click(function(){
        jQuery('#newproducts_slider').show();
        jQuery('#bestsellers_slider').hide();
        jQuery('#specials_slider').hide();
        set_reset_tab(jQuery(this));
    });


    if(!jQuery('#newproducts_slider').length){
        jQuery(".tabs div:eq(0)").remove();
    };

    if(!jQuery('#specials_slider').length){
        jQuery("#specials_activate").parent().remove();
    };

    if(!jQuery('#bestsellers_slider').length){
        jQuery("#bestsellers_activate").parent().remove();
    };
    jQuery(".sixteen.columns.alpha div:first").show();
    jQuery('.tabs > div:first a').addClass('active_slider');


    jQuery('#nav_block_head').click(function() {
        jQuery('.nav_block_dropdown').toggleClass('visible_on');
    });

    jQuery('#menu_block_head').click(function() {
        jQuery('.menu_block_dropdown').toggleClass('visible_on');
    });

    jQuery("#select1").selectbox();
    jQuery("#select2").selectbox();
    jQuery("#select3").selectbox();


    jQuery("#back-top").hide();
    jQuery(window).scroll(function () {
            if (jQuery(this).scrollTop() > 600) {
                jQuery('#back-top').fadeIn();
            } else {
                jQuery('#back-top').fadeOut();
            }
    });
    jQuery('#back-top a').click(function () {
            jQuery('body,html').animate({
                scrollTop: 0
            }, 400);
            return false;
    });
/*
    jQuery('#mycarousel').jcarousel({
        vertical: true,
        scroll: 1,
        auto: 3,
        wrap: 'circular',
        animation: 1000,
        easing: 'linear'
    });
    */

    jQuery("#carousel1").responsiveSlides({
        pager: false,
        nav: true,
        speed: 1000,
        auto: true,
        timeout: 3600,
        namespace: "callbacks"
    });

    jQuery('#carousel').elastislide({
        easing		: 'easeInOutQuad',
        speed		: 1200
    });

    jQuery('#carousel_bestsellers').elastislide({
        easing		: 'easeInOutQuad',
        speed		: 1200
    });



    jQuery('#carousel_specials').elastislide({
        easing		: 'easeInOutQuad',
        speed		: 1200
    });



    jQuery('#carousel_newproducts').elastislide({
        easing		: 'easeInOutQuad',
        speed		: 1200
    });





    jQuery('#slider_top a.callbacks_nav').hover(function() {
            jQuery(this).stop().animate({opacity:1.0},500);
        },
        function() {
            jQuery(this).stop().animate({opacity:0.6},500);
    });



    jQuery(".shopping_cart div.open").live('click',function() {
        jQuery("#shopping_cart_mini").fadeToggle(300, "linear");
    });

    jQuery(".product").hover(function() {
        jQuery(this).find(".roll_over_img").fadeToggle("fast", "linear");
        jQuery(this).find(".product-image-wrapper-hover").fadeToggle("fast", "linear");
    });

    jQuery(".product-list-wrapper").hover(function() {
        jQuery(this).find(".roll_over_img").fadeToggle("fast", "linear");
    });


    jQuery(".product").hover(function() {
        jQuery(this).find(".wrapper-hover").animate({ backgroundColor: "#444444" }, 500);
    },function() {
        jQuery(this).find(".wrapper-hover").animate({ backgroundColor: "#fff" }, 500);
    });

    jQuery(".product").hover(function() {
        jQuery(this).find(".wrapper-hover-hidden ").fadeToggle(500, "linear");
    });

    jQuery(".product").hover(function() {
        jQuery(this).find(".wrapper-hover span").animate({color:"#fff"}, 500);
        jQuery(this).find(".wrapper-hover a").animate({color:"#fff"}, 500);

    },function() {
        jQuery(this).find(".wrapper-hover span").animate({color:"#000"}, 500);
        jQuery(this).find(".wrapper-hover a").animate({color:"#000"}, 500);
    });


    jQuery('#back-top a').hover(function() {
            jQuery(this).stop().animate({opacity:1.0},500);
        },
        function() {
            jQuery(this).stop().animate({opacity:0.4},500);
    });

    jQuery(".fancybox").fancybox();
    jQuery(".various").fancybox({
        maxWidth	: 800,
        maxHeight	: 600,
        fitToView	: false,
        width		: '70%',
        height		: '70%',
        autoSize	: false,
        closeClick	: false,
        openEffect	: 'none',
        closeEffect	: 'none'
    });

    /* parallax bg */

    var windowWidth = window.innerWidth || document.documentElement.clientWidth;
    if ($(".parallax").length > 0 && !isTouchDevice() ) {
        $(".parallax").parallax({
            speed: 0,
            axis: "y"
        });
    }



    /* brands slider */
    var brandsCarousel = jQuery(".brands .brands-carousel ul");
    var brandsCarouselMax = 7;

    brandsCarousel.carouFredSel({
        responsive: true,
        width: '100%',
        scroll: 1,
        prev: '#brands-carousel-prev',
        next: '#brands-carousel-next',
        items: {
            width: 140,
            height: '30%',	//	optionally resize item-height
            visible: {
                min: 1,
                max: brandsCarouselMax
            }
        }
    });

    var blogCarousel = jQuery(".from-blog .carousel ul");
    var blogCarouselMax = 2;

    blogCarousel.carouFredSel({
        responsive: true,
        width: '100%',
        scroll: 1,
        prev: '#blog-carousel-prev',
        next: '#blog-carousel-next',
        items: {
            width: 400,
            visible: {
                min: 1,
                max: blogCarouselMax
            }
        }
    });



});

jQuery(document).ready(TopSlider);
jQuery(window).resize(TopSlider);
//jQuery.noConflict();