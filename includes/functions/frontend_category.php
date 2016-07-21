<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
function remove_frontend_category($category_id) {
	$category_image_query = tep_db_query("select categories_image from frontend_categories where categories_id = '" . (int)$category_id . "'");
	$category_image = tep_db_fetch_array($category_image_query);
	$duplicate_image_query = tep_db_query("select count(*) as total from frontend_categories where categories_image = '" . tep_db_input($category_image['categories_image']) . "'");
	$duplicate_image = tep_db_fetch_array($duplicate_image_query);
	if ($duplicate_image['total'] < 2) {
		if (file_exists(DIR_FS_CATALOG_IMAGES . $category_image['categories_image'])) {
			@unlink(DIR_FS_CATALOG_IMAGES . $category_image['categories_image']);
		}
	}
    tep_db_query("delete from frontend_categories where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from frontend_categories_description where categories_id = '" . (int)$category_id . "'");
    tep_db_query("delete from frontend_products_to_categories where categories_id = '" . (int)$category_id . "'");
}

function get_frontend_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
	global $languages_id;
    if (!is_array($category_tree_array)) $category_tree_array = array();
    if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => TEXT_TOP);
    if ($include_itself) {
		$category_query = tep_db_query("select cd.categories_name from frontend_categories_description cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
		$category = tep_db_fetch_array($category_query);
		$category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
    }
    $categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from frontend_categories c, frontend_categories_description cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
    while ($categories = tep_db_fetch_array($categories_query)) {
      if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
      $category_tree_array = get_frontend_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
    }
    return $category_tree_array;
}

function childs_in_frontend_category_count($categories_id) {
    $categories_count = 0;
    $categories_query = tep_db_query("select categories_id from frontend_categories where parent_id = '" . (int)$categories_id . "'");
    while ($categories = tep_db_fetch_array($categories_query)) {
      $categories_count++;
      $categories_count += childs_in_frontend_category_count($categories['categories_id']);
    }
    return $categories_count;
}

function products_in_frontend_category_count($categories_id, $include_deactivated = false) {
    $products_count = 0;
    if ($include_deactivated) {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, frontend_products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$categories_id . "'");
    } else {
      $products_query = tep_db_query("select count(*) as total from " . TABLE_PRODUCTS . " p, frontend_products_to_categories p2c where p.products_id = p2c.products_id and p.products_status = '1' and p2c.categories_id = '" . (int)$categories_id . "'");
    }
    $products = tep_db_fetch_array($products_query);
    $products_count += $products['total'];
    $childs_query = tep_db_query("select categories_id from frontend_categories where parent_id = '" . (int)$categories_id . "'");
    if (tep_db_num_rows($childs_query)) {
      while ($childs = tep_db_fetch_array($childs_query)) {
        $products_count += products_in_frontend_category_count($childs['categories_id'], $include_deactivated);
      }
    }

    return $products_count;
}

function get_frontend_category_name($category_id, $language_id) {
    $category_query = tep_db_query("select categories_name from frontend_categories_description where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);
    return $category['categories_name'];
}

function get_frontend_category_desc($category_id, $language_id) {
    $category_query = tep_db_query("select categories_htc_description from frontend_categories_description where categories_id = '" . (int)$category_id . "' and language_id = '" . (int)$language_id . "'");
    $category = tep_db_fetch_array($category_query);
    return $category['categories_htc_description'];
}

function get_frontend_path($current_category_id = '') {
    global $cPath_array;

    if ($current_category_id == '') {
      $cPath_new = implode('_', $cPath_array);
    } else {
      if (sizeof($cPath_array) == 0) {
        $cPath_new = $current_category_id;
      } else {
        $cPath_new = '';
        $last_category_query = tep_db_query("select parent_id from frontend_categories where categories_id = '" . (int)$cPath_array[(sizeof($cPath_array)-1)] . "'");
        $last_category = tep_db_fetch_array($last_category_query);

        $current_category_query = tep_db_query("select parent_id from frontend_categories where categories_id = '" . (int)$current_category_id . "'");
        $current_category = tep_db_fetch_array($current_category_query);

        if ($last_category['parent_id'] == $current_category['parent_id']) {
          for ($i = 0, $n = sizeof($cPath_array) - 1; $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        } else {
          for ($i = 0, $n = sizeof($cPath_array); $i < $n; $i++) {
            $cPath_new .= '_' . $cPath_array[$i];
          }
        }

        $cPath_new .= '_' . $current_category_id;

        if (substr($cPath_new, 0, 1) == '_') {
          $cPath_new = substr($cPath_new, 1);
        }
      }
    }

    return 'cPath=' . $cPath_new;
}

function generate_frontend_category_path($id, $from = 'category', $categories_array = '', $index = 0) {
    global $languages_id;

    if (!is_array($categories_array)) $categories_array = array();

    if ($from == 'product') {
      $categories_query = tep_db_query("select categories_id from frontend_products_to_categories where products_id = '" . (int)$id . "'");
      while ($categories = tep_db_fetch_array($categories_query)) {
        if ($categories['categories_id'] == '0') {
          $categories_array[$index][] = array('id' => '0', 'text' => TEXT_TOP);
        } else {
          $category_query = tep_db_query("select cd.categories_name, c.parent_id from frontend_categories c, frontend_categories_description cd where c.categories_id = '" . (int)$categories['categories_id'] . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
          $category = tep_db_fetch_array($category_query);
          $categories_array[$index][] = array('id' => $categories['categories_id'], 'text' => $category['categories_name']);
          if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = generate_frontend_category_path($category['parent_id'], 'category', $categories_array, $index);
          $categories_array[$index] = array_reverse($categories_array[$index]);
        }
        $index++;
      }
    } elseif ($from == 'category') {
      $category_query = tep_db_query("select cd.categories_name, c.parent_id from frontend_categories c, frontend_categories_description cd where c.categories_id = '" . (int)$id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "'");
      $category = tep_db_fetch_array($category_query);
      $categories_array[$index][] = array('id' => $id, 'text' => $category['categories_name']);
      if ( (tep_not_null($category['parent_id'])) && ($category['parent_id'] != '0') ) $categories_array = generate_frontend_category_path($category['parent_id'], 'category', $categories_array, $index);
    }

    return $categories_array;
}

function get_cpath_by_products_id($product_id){
	$resp = '';
	$sql = tep_db_query("select categories_id from products_to_categories where products_id='" . $product_id . "'");
	if (tep_db_num_rows($sql)){
		$sql_info = tep_db_fetch_array($sql);
		return tep_get_path($sql_info['categories_id']); 
	} else {
		return tep_get_path(); 
	}
}

function get_output_generated_category_path($id, $from = 'category') {
    $calculated_category_path_string = '';
    $calculated_category_path = generate_frontend_category_path($id, $from);
    for ($i=0, $n=sizeof($calculated_category_path); $i<$n; $i++) {
      for ($j=0, $k=sizeof($calculated_category_path[$i]); $j<$k; $j++) {
        $calculated_category_path_string .= $calculated_category_path[$i][$j]['text'] . '&nbsp;&gt;&nbsp;';
      }
      $calculated_category_path_string = substr($calculated_category_path_string, 0, -16) . '<br>';
    }
    $calculated_category_path_string = substr($calculated_category_path_string, 0, -4);

    if (strlen($calculated_category_path_string) < 1) $calculated_category_path_string = TEXT_TOP;

    return $calculated_category_path_string;
}

function set_frontend_category_status($categories_id, $status) {
    $categories = get_frontend_category_tree($categories_id, '', '0', '', true);
	for ($i=0, $n=sizeof($categories); $i<$n; $i++) {
		$update_status = tep_db_query("update frontend_categories set categories_status = '".(int)$status."' where categories_id = '".(int)$categories[$i]['id']."' ");
	}
  	if ($status == '1') {
      return tep_db_query("update frontend_categories set categories_status = '1', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
    } elseif ($status == '0') {
      return tep_db_query("update frontend_categories set categories_status = '0', last_modified = now() where categories_id = '" . (int)$categories_id . "'");
    } else {
      return -1;
    }
} 

function set_frontend_category_by_category_id($base_category_id, $frontend_category_id){
	$sql = tep_db_query("select a.products_id from products_to_categories a where a.categories_id='" . $base_category_id . "' and not exists(select b.categories_id from frontend_products_to_categories b where b.products_id=a.products_id)");
	while ($sql_info = tep_db_fetch_array($sql)){
		tep_db_query("insert into frontend_products_to_categories (products_id, categories_id) values ('" . $sql_info['products_id'] . "', '" . $frontend_category_id . "')");
	}
	
	$sql = tep_db_query("select categories_id from categories where parent_id='" . $base_category_id . "'");
	while ($sql_info = tep_db_fetch_array($sql)){
		set_frontend_category_by_category_id($sql_info['categories_id'], $frontend_category_id);
	}
}

function set_frontend_category_by_product_id($product_id, $frontend_category_id){
	tep_db_query("delete from frontend_products_to_categories where products_id='" . $product_id . "'");
	tep_db_query("insert into frontend_products_to_categories (products_id, categories_id) values ('" . $product_id . "', '" . $frontend_category_id . "')");
}

function unset_frontend_category_by_product_id($product_id){
	tep_db_query("delete from frontend_products_to_categories where products_id='" . $product_id . "'");
}

function get_frontend_category_name_by_product_id($product_id){
	$resp = '';
	$sql = tep_db_query("select a.categories_name from frontend_categories_description a inner join frontend_categories b on a.categories_id=b.categories_id inner join frontend_products_to_categories c on a.categories_id=c.categories_id where a.language_id='1' and c.products_id='" . $product_id . "'");
	if (tep_db_num_rows($sql)){
		$sql_info = tep_db_fetch_array($sql);
		$resp = $sql_info['categories_name'];
	}
	return $resp;
}

?>