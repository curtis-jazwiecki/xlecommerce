var jqnnosmart = jQuery.noConflict();
jqnnosmart(document).ready(function(){

//Set default open/close settings
jqnnosmart('.acc_container').hide(); //Hide/close all containers
jqnnosmart('.acc_trigger:first').addClass('active').next().show(); //Add "active" class to first trigger, then show/open the immediate next container

//On Click
jqnnosmart('.acc_trigger').click(function(){
if( jqnnosmart(this).next().is(':hidden') ) { //If immediate next container is closed...
jqnnosmart('.acc_trigger').removeClass('active').next().slideUp(); //Remove all .acc_trigger classes and slide up the immediate next container
jqnnosmart(this).toggleClass('active').next().slideDown(); //Add .acc_trigger class to clicked trigger and slide down the immediate next container
}
return false; //Prevent the browser jump to the link anchor
});

});


//Slideshow

jqnnosmart(window).load(function() {
    jqnnosmart('.flexslider').flexslider();
  });


//Scroll to top Script
var jqnnosmart1 = jQuery.noConflict();
jqnnosmart1(function() {
    jqnnosmart1.fn.scrollToTop = function() {
	jqnnosmart1(this).hide().removeAttr("href");
	if (jqnnosmart1(window).scrollTop() != "0") {
	    jqnnosmart1(this).fadeIn("slow")
	}
	var scrollDiv = jqnnosmart1(this);
	jqnnosmart1(window).scroll(function() {
	    if (jqnnosmart1(window).scrollTop() == "0") {
		jqnnosmart1(scrollDiv).fadeOut("slow")
	    } else {
		jqnnosmart1(scrollDiv).fadeIn("slow")
	    }
	});
	jqnnosmart1(this).click(function() {
	    jqnnosmart1("html, body").animate({
		scrollTop: 0
	    }, "slow")
	})
    }
});

jqnnosmart1(function() {
    jqnnosmart1("#w2b-StoTop").scrollToTop();
});

// Slider 
jqnnosmart1(function() {
	jqnnosmart1('#carousel ul').carouFredSel({
	prev: '#prev',
	next: '#next',
	pagination: "#pager",
	scroll: 1000
});
	
});
