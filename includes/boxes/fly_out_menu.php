<?php
/*
drop_down_menu_column.php

a direct drop down menu, with a second level pop out for categories

INCLUDE this css code into the index.php.html */
?>
<!--
/* NEW MENU */
   ul.topnav { list-style: none; padding: 0 20px; margin: 0; float: left; width: 130px; background: #222; font-size: 1.0em; background: url(images/topnav_bg.gif) repeat-x; }
   ul.topnav li { float: left; margin: 0; padding: 0 15px 0 0; position: relative; /*--Declare X and Y axis base for sub navigation--*/ }
   ul.topnav li a{ padding: 5px 0px 5px 5px; color: #fff; display: block; text-decoration: none; float: left; }
   ul.topnav li a:hover{
       background: url(images/topnav_hover.gif) no-repeat center top;
   }
   ul.topnav li span { /*--Drop down trigger styles--*/
       width: 17px;
       height: 35px;
       float: left;
       background: url(images/subnav_btn.gif) no-repeat center top;
   }
   ul.topnav li span.subhover
    {background-position: center bottombottom; cursor: pointer;} /*--Hover effect for trigger--*/

   ul.topnav li ul.subnav {
     font-size: 12px;
     list-style: none;
     position: absolute; /*--Important - Keeps subnav from affecting main navigation flow--*/
     left: 110px; top: 0px;
     background: #333;
     margin: 0; padding: 0;
     text-align: left;
     display: none;
     float: left;
     width: 200px;
     border: 1px solid #111;
 }

   ul.topnav li ul.subnav2 {
     font-size: 12px;
     list-style: none;
     position: absolute; /*--Important - Keeps subnav from affecting main navigation flow--*/
     left: 110px; top: 0px;
     background: #333;
     margin: 0; padding: 0;
     text-align: left;
     display: none;
     float: left;
     width: 800px;
     border: 1px solid #111;
 }

 ul.topnav li ul.subnav li{  
     margin: 0; padding: 0;  
     border-top: 1px solid #252525; /*--Create bevel effect--*/  
     border-bottom: 1px solid #444; /*--Create bevel effect--*/  
     clear: both;
     width: 200px;  
 }
 
 ul.topnav li ul.subnav2 li.line_break{
     margin: 0; padding: 0;  
     border-top: 1px solid #252525; /*--Create bevel effect--*/  
	 display: block;
     width: 200px;  
 }

 html ul.topnav li ul.subnav li a {  
     float: left;  
     width: 175px;  
     background: #333 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
     padding-left: 20px;  
 }
 html ul.topnav li ul.subnav li a:hover { /*--Hover effect for subnav links--*/  
     background: #222 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
 }
 html ul.topnav li ul.subnav2 li a {  
     float: left;  
     width: 175px;  
     background: #333 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
     padding-left: 20px;  
 }
 html ul.topnav li ul.subnav2 li a:hover { /*--Hover effect for subnav links--*/  
     background: #222 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
 }  
/* END NEW MENU */
-->
<?php
/*
Also Include this javascript in the <head> section

<script type="text/javascript" src="http://code.jquery.com/jquery-latest.js"></script>

<script type="text/javascript">
 $(document).ready(function(){

 $("ul.subnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled - Adds empty span tag after ul.subnav

 $("ul.topnav li span").hover(function() { //When trigger is clicked...

 //Following events are applied to the subnav itself (moving subnav up and down)
 $(this).parent().find("ul.subnav").slideDown('slow').show(); //Drop down the subnav on click

 $(this).parent().hover(function() {
 }, function(){
 $(this).parent().find("ul.subnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
 });
 //Following events are applied to the trigger (Hover events for the trigger)
 }).hover(function() {
 $(this).addClass("subhover"); //On hover over, add class "subhover"
 }, function(){ //On Hover Out
 $(this).removeClass("subhover"); //On hover out, remove class "subhover"
 });
});

 $(document).ready(function(){

 $("ul.subnav2").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled - Adds empty span tag after ul.subnav

 $("ul.topnav li span").hover(function() { //When trigger is clicked...

 //Following events are applied to the subnav itself (moving subnav up and down)
 $(this).parent().find("ul.subnav2").slideDown('slow').show(); //Drop down the subnav on click

 $(this).parent().hover(function() {
 }, function(){
 $(this).parent().find("ul.subnav2").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
 });
 //Following events are applied to the trigger (Hover events for the trigger)
 }).hover(function() {
 $(this).addClass("subhover"); //On hover over, add class "subhover"
 }, function(){ //On Hover Out
 $(this).removeClass("subhover"); //On hover out, remove class "subhover"
 });
});
</script>
*/
?>

 <ul class="topnav">
     <li style="font-weight: bold"><a href="#">Home</a></li>
 </ul>
 <ul class="topnav">
     <li style="font-weight: bold">  
         <a href="#">Categories</a>&nbsp;  
         <ul class="subnav2">
<?php
			$categories_query = tep_db_query("SELECT c.categories_id, cd.categories_id, cd.language_id, cd.categories_name FROM categories c LEFT JOIN categories_description cd ON c.categories_id = cd.categories_id WHERE c.parent_id = 0 AND cd.categories_name != '' ORDER BY cd.categories_name");
			$count=0;
			while ($row = tep_db_fetch_array($categories_query))
			  {
				if(($count % 4) == 0)
					echo "<li class='line_break' style='clear: both;'><a href='".HTTP_SERVER."/".strtolower($row['categories_name'])."-".c."-".$row['categories_id'].".html'>".ucwords(strtolower($row['categories_name']))."</a></li>";
				else
					echo "<li class='line_break'><a href='".HTTP_SERVER."/".strtolower($row['categories_name'])."-".c."-".$row['categories_id'].".html'>".ucwords(strtolower($row['categories_name']))."</a></li>";
				$count++;
			  }
?>
         </ul>
     </li>
 </ul>
 <ul class="topnav">
     <li style="font-weight: bold">
         <a href="#">Information</a>
         <ul class="subnav">
<?php
			$information_query = tep_db_query("SELECT information_id, information_group_id, information_title, information_description, parent_id, sort_order, visible, language_id FROM information WHERE visible = '1' AND information_title != 'TEXT_MAIN' ORDER BY sort_order");

			while ($rows = tep_db_fetch_array($information_query))
			  {
				echo '<li><a href="' . tep_href_link(FILENAME_INFORMATION, 'info_id=' . $rows['information_id']) . '"><b>' . $rows['information_title'] . '</b></a></li>';
			  }
?>
         </ul>
     </li style="font-weight: bold">
 </ul>
 <ul class="topnav">
     <li style="font-weight: bold"><a href="#">About Us</a></li>
 </ul>
 <ul class="topnav">
     <li style="font-weight: bold"><a href='contact_us.php'>Contact Us</a></li>
 </ul>
<?php
//                        for($x=0; $x < sizeof($category_tree_array); $x++)
//                   	  {
//                          echo "<li>".$category_tree_array[$x]."</li>";
//                       }
?>


 </ul>