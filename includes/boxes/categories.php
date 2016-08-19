<?php
/*
  $Id: categories.php,v 1.25 2003/07/09 01:13:58 hpdl Exp $

  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/

  function tep_show_category($counter) {
    global $tree, $categories_string, $cPath_array;

    for ($i=0; $i<$tree[$counter]['level']; $i++) {
      $categories_string .= "&nbsp;&nbsp;";
    }

    $current_template_selected = basename(STS_TEMPLATE_DIR);

    if($current_template_selected == 'template21'){
		$categories_string .= '<ul><li class="cat mnu_side_row"><a href="';	
	}else{
		$categories_string .= '<p class="cat"><a href="';
	}

    if ($tree[$counter]['parent'] == 0) {
      $cPath_new = 'cPath=' . $counter;
    } else {
      $cPath_new = 'cPath=' . $tree[$counter]['path'];
    }

    if($current_template_selected == 'template21'){
		$categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="mnu_side_lnk">';
	}else{
		$categories_string .= tep_href_link(FILENAME_DEFAULT, $cPath_new) . '">';
	}

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '<b>';
    }

// display category name
   $categories_string .= $tree[$counter]['name'];

    if (isset($cPath_array) && in_array($counter, $cPath_array)) {
      $categories_string .= '</b>';
    }

    if (tep_has_category_subcategories($counter)) {
      $categories_string .= '';
    }

    if($current_template_selected == 'template21'){
		$categories_string .= '</a></li></ul>';
	}else{
		$categories_string .= '</a></p>';
	}

    //$categories_string .= '<br>';
    $categories_string .= '';

    if ($tree[$counter]['next_id'] != false) {
      tep_show_category($tree[$counter]['next_id']);
    }
  }
?>
<!-- categories //-->
          <tr>
            <td>
<?php
  $info_box_contents = array();
 // $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'categories.jpg', BOX_HEADING_CATEGORIES));

  new infoBoxHeading($info_box_contents, true, false);

  $categories_string = '';
  $tree = array();
//Categories Status MOD BEGIN by FIW
  $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '0' and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name");
//Categories Status MOD END by FIW
  while ($categories = tep_db_fetch_array($categories_query))  {
 //START CJ Un capitalize category names 02252015
   //THIS AREA CHANGES CAPITALIZATION OF CATEGORY NAME IN CATEGORY BOX
   // $tree[$categories['categories_id']] = array('name' => ucwords(strtolower($categories['categories_name'])),
   $tree[$categories['categories_id']] = array('name' => ($categories['categories_name']),
   //END //START CJ Un capitalize category names 02252015
                                                'parent' => $categories['parent_id'],
                                                'level' => 0,
                                                'path' => $categories['categories_id'],
                                                'next_id' => false);

    if (isset($parent_id)) {
      $tree[$parent_id]['next_id'] = $categories['categories_id'];
    }

    $parent_id = $categories['categories_id'];

    if (!isset($first_element)) {
      $first_element = $categories['categories_id'];
    }
  }

/*
  //------------------------
  if (tep_not_null($cPath)) {
    $new_path = '';
    reset($cPath_array);
    while (list($key, $value) = each($cPath_array)) {
      unset($parent_id);
      unset($first_id);
  //Categories Status MOD BEGIN by FIW   
      $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_status = '1' and c.parent_id = '" . (int)$value . "' and c.categories_id = cd.categories_id and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name");
      //Categories Status MOD END by FIW

      if (tep_db_num_rows($categories_query)) {
        $new_path .= $value;
        while ($row = tep_db_fetch_array($categories_query)) {
          $tree[$row['categories_id']] = array('name' => $row['categories_name'],
                                               'parent' => $row['parent_id'],
                                               'level' => $key+1,
                                               'path' => $new_path . '_' . $row['categories_id'],
                                               'next_id' => false);

          if (isset($parent_id)) {
            $tree[$parent_id]['next_id'] = $row['categories_id'];
          }

          $parent_id = $row['categories_id'];

          if (!isset($first_id)) {
            $first_id = $row['categories_id'];
          }

          $last_id = $row['categories_id'];
        }
        $tree[$last_id]['next_id'] = $tree[$value]['next_id'];
        $tree[$value]['next_id'] = $first_id;
        $new_path .= '_';
      } else {
        break;
      }
    }
  }
  */
  tep_show_category($first_element); 

  $info_box_contents = array();
  $info_box_contents[] = array('text' => $categories_string);

  new columnBox($info_box_contents);
?>
            </td>
          </tr>
<!-- categories_eof //-->
