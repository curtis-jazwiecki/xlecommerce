<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Slide Show</title>
<script type="text/javascript" src="slide_show/coin-slider.js"></script>
<script type="text/javascript" src="slide_show/coin-slider.min.js"></script>
<link rel="stylesheet" href="slide_show/coin-slider-styles.css" type="text/css" />

<script type="text/javascript">
$(document).ready(function() {
   $('#s3slider').s3Slider({
      timeOut: 4000
   });
}); 
</script>
</head>
<body>

<div id="s3slider">
   <ul id="s3sliderContent">
      <li class="s3sliderImage">
          <img src="http://67.227.176.37/images/front_page/shop_box.jpg" />
          <span>Shop Online!</span>
      </li>
      <li class="s3sliderImage">
          <img src="http://67.227.176.37/images/front_page/specials_box.jpg" />
          <span>Check out our Specials</span>
      </li>
      <li class="s3sliderImage">
          <img src="http://67.227.176.37/images/front_page/contact_box.jpg" />
          <span>Contact Us Today</span>
      </li>
      <div class="clear s3sliderImage"></div>
   </ul>
</div> 

</body>
</html>