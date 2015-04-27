<table cellpadding="0" cellspacing="2" width="100%" align="center"><tr>
<?php
    $number_of_categories = tep_db_num_rows($categories_query);
    $num = $number_of_categories/2;
    $num = ceil($num);

    echo '<td valign="top" width="50%">';
    $col = 0;
    $row = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
    	
      $col++;
      $cPath_new = tep_get_path($categories['categories_id']);
      $width = (int)(100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
      echo '              <table cellpadding="0" cellspacing="0" width="100%"><tr><td class="cat_title"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="cat_title">' . ucwords(strtolower($categories['categories_name'])) . '</a> >></td></tr>';
      $sub_cat_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
  echo '<tr><td class="cat_list">'; 
    if(tep_db_num_rows($sub_cat_query) > 0){
      while ($sub_cat = tep_db_fetch_array($sub_cat_query)) {
      		      $cPath_new_1 = "cPath="  . $current_category_id . "_" . $categories['categories_id'] . "_" . $sub_cat['categories_id'];
      echo ' &#149 <a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new_1) . '" class="cat_list">' . ucwords(strtolower($sub_cat['categories_name'])). '</a><br>';     
          }
    } else {
      	$products_in_category = tep_count_products_in_category($categories['categories_id']);
       	 
       	 echo ' &#149 <a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">View all ' . $products_in_category. ' Products.</a><br>';
            }
             echo '</td></tr><tr><td>'.tep_draw_separator('pixel_trans.gif', '1', '10').'</td></tr></table>';
   
      if ($col == $num) {
      	$col = 0;
      	$row++;
        echo '              </td><td valign="top" width="20px">' . tep_draw_separator('pixel_trans.gif', '20', '1') . '</td>';
        echo '              <td valign="top" width="50%">';

      }
    }

?>
</tr></table>