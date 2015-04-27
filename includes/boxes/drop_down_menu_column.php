<?php
/*
drop_down_menu_column.php

a direct drop down menu, with a second level pop out for categories

INCLUDE this css code into the index.php.html */
?>
<!--
/* NEW MENU */
   ul.topnav { list-style: none; padding: 0 20px; margin: 0; float: left; width: 860px; background: #222; font-size: 1.0em; background: url(images/topnav_bg.gif) repeat-x; }
   ul.topnav li { float: left; margin: 0; padding: 0 15px 0 0; position: relative; /*--Declare X and Y axis base for sub navigation--*/ }  
   ul.topnav li a{ padding: 10px 5px 0px 5px; color: #fff; display: block; text-decoration: none; float: left; }  
   ul.topnav li a:hover{ background: url(images/topnav_hover.gif) no-repeat center top; }  
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
     left: 0px; top: 35px;
     background: #333;
     margin: 0; padding: 0;
     text-align: left;
     display: none;
     float: left;
     width: 300px;
     border: 1px solid #111;
 }
 ul.topnav li ul.subnav li{  
     margin: 0; padding: 0;  
     border-top: 1px solid #252525; /*--Create bevel effect--*/  
     border-bottom: 1px solid #444; /*--Create bevel effect--*/  
     height: 35px;
     clear: both;
     width: 300px;  
 }
   ul.topnav li ul.subnav2 {
     font-size: 12px;
     list-style: none;
     position: absolute; /*--Important - Keeps subnav from affecting main navigation flow--*/
     left: 0px; top: 35px;
     background: #333;
     margin: 0; padding: 0;
     text-align: left;
     display: none;
     float: left;
     width: 300px;
     border: 1px solid #111;
 }
 ul.topnav li ul.subnav2 li{  
     margin: 0; padding: 0;  
     border-top: 1px solid #252525; /*--Create bevel effect--*/  
     border-bottom: 1px solid #444; /*--Create bevel effect--*/  
     height: 35px;
     clear: both;
     width: 275px;  
 }
 html ul.topnav li ul.subnav2 li a {  
     float: left;  
     width: 275px;  
     height: 25px;
     background: #333 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
     padding-left: 20px;  
 }
 html ul.topnav li ul.subnav li a {  
     float: left;  
     width: 250px;  
     height: 25px;
     background: #333 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
     padding-left: 20px;  
 }
 html ul.topnav li ul.subnav li a:hover { /*--Hover effect for subnav links--*/  
     background: #222 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
 }
 html ul.topnav li ul.subnav li ul.subsubnav li a:hover { /*--Hover effect for subnav links--*/  
     background: #222 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
 }

 ul.topnav li ul.subnav li ul.subsubnav{
     font-size: 12px;
     list-style: none;
     position: absolute; /*--Important - Keeps subnav from affecting main navigation flow--*/
     left: 300px; top: 0px;
     background: #333;
     margin: 0; padding: 0;
     text-align: left;
     display: none;
     float: left;
     width: 300px;
     border: 1px solid #111;
 }
 ul.topnav li ul.subnav li ul.subsubnav li{
     margin: 0; padding: 0;
     border-top: 1px solid #252525; /*--Create bevel effect--*/
     border-bottom: 1px solid #444; /*--Create bevel effect--*/
     height: 35px;
     clear: both;
     width: 300px;
 }
 html ul.topnav li ul.subnav li ul.subsubnav li a {  
     float: left;  
     width: 275px;  
     background: #333 url(images/dropdown_linkbg.gif) no-repeat 10px center;  
     padding-left: 20px;  
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

 $(document).ready(function(){

 $("ul.subsubnav").parent().append("<span></span>"); //Only shows drop down trigger when js is enabled - Adds empty span tag after ul.subnav

 $("ul.topnav li ul.subnav li span").hover(function() { //When trigger is clicked...

 //Following events are applied to the subnav itself (moving subnav up and down)
 $(this).parent().find("ul.subsubnav").slideDown('slow').show(); //Drop down the subnav on click

 $(this).parent().hover(function() {
 }, function(){
 $(this).parent().find("ul.subsubnav").slideUp('slow'); //When the mouse hovers out of the subnav, move it back up
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
     <li style="font-weight: bold">  
         <a href="#">Categories</a>  
         <ul class="subnav">
<?php
//Custom Code
			$category_arr_cat_id = array();
			$category_arr_count = array();
			$category_arr_language_id = array();
			$category_arr_categories_name = array();
			$category_arr_parent_id = array();
			$category_arr_cat_id_2 = array();
			$category_arr_count_2 = array();
			$category_arr_language_id_2 = array();
			$category_arr_categories_name_2 = array();
			$category_arr_parent_id_2 = array();
			$arr_count=0;
			$arr_count2=0;
			$categories_query = tep_db_query("SELECT c.categories_id, cd.categories_id, cd.language_id, cd.categories_name, c.parent_id, c.categories_status FROM categories c LEFT JOIN categories_description cd ON c.categories_id = cd.categories_id WHERE c.parent_id = 0 AND c.categories_status = 1 AND cd.categories_name != '' ORDER BY cd.categories_name");
			$categories_query2 = tep_db_query("SELECT c.categories_id, cd.categories_id, cd.language_id, cd.categories_name, c.parent_id, c.categories_status FROM categories c LEFT JOIN categories_description cd ON c.categories_id = cd.categories_id WHERE c.parent_id > 0 AND c.categories_status = 1 AND cd.categories_name != '' ORDER BY c.categories_id");
;
			while($rows = tep_db_fetch_array($categories_query))
			  {
				$category_arr_cat_id[$arr_count] = $rows['categories_id'];
				$category_arr_count[$arr_count] = $arr_count;
				$category_arr_language_id[$arr_count] = $rows['language_id'];
				$category_arr_categories_name[$arr_count] = $rows['categories_name'];
				$category_arr_parent_id[$arr_count] = $rows['parent_id'];
				$arr_count++;
			  }

			while($rowz = tep_db_fetch_array($categories_query2))
			  {
				$category_arr_cat_id_2[$arr_count2] = $rowz['categories_id'];
				$category_arr_count_2[$arr_count2] = $arr_count;
				$category_arr_language_id_2[$arr_count2] = $rowz['language_id'];
				$category_arr_categories_name_2[$arr_count2] = $rowz['categories_name'];
				$category_arr_parent_id_2[$arr_count2] = $rowz['parent_id'];
				$arr_count2++;
			  }

			for($x=0;$x < $arr_count; $x++)
			  {
                echo "<li>";
				echo "<a href='".HTTP_SERVER."/".strtolower($category_arr_categories_name[$x])."-".c."-".$category_arr_cat_id[$x].".html'>".ucwords(strtolower($category_arr_categories_name[$x]))."</a>";

				for($y=0;$y < $arr_count2; $y++)
				  {
					if($category_arr_cat_id[$x] == $category_arr_parent_id_2[$y])
					  {
						echo "<ul class='subsubnav'>";
						for($z=0;$z < $arr_count2; $z++)
						  {
							if($category_arr_cat_id[$x] == $category_arr_parent_id_2[$z])
							  {
								echo "<li><a href='".HTTP_SERVER."/".strtolower($category_arr_categories_name_2[$z])."-".c."-".$category_arr_cat_id_2[$z].".html'>".ucwords(strtolower($category_arr_categories_name_2[$z]))."</a></li>";
							  }
						  }
						echo "</ul>";
					  }
				  }

				echo "</li>";
			  }

?>
         </ul>
     </li>
     <li style="font-weight: bold">
         <a href="#">Information</a>
         <ul class="subnav2">
<?php
			$information_query = tep_db_query("SELECT information_id, information_group_id, information_title, information_description, parent_id, sort_order, visible, language_id FROM information WHERE visible = '1' AND information_title != 'TEXT_MAIN' ORDER BY sort_order");

			while ($rows = tep_db_fetch_array($information_query))
			  {
				echo '<li><a href="' . tep_href_link(FILENAME_INFORMATION, 'info_id=' . $rows['information_id']) . '"><b>' . $rows['information_title'] . '</b></a></li>';
			  }

?>
         </ul>
     </li style="font-weight: bold">
     <li style="font-weight: bold"><a href="#">About Us</a></li>
    <li style="font-weight: bold"><a href='contact_us.php'>Contact Us</a></li>
 </ul>