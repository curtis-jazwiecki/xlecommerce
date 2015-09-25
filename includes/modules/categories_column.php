<?php
// Get selected template
$category_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'CATEGORY_LISTING_TEMPLATE'");
$category_rows = tep_db_fetch_array($category_query);
$selected_category_template = $category_rows['configuration_value'];

// Get selected template
$row_count='';
if($selected_category_template == 0){
	echo '<table class="subcat catlisting_4" cellpadding="0" cellspacing="" width="100%" align="center"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)){
		$rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / MAX_DISPLAY_CATEGORIES_PER_ROW) . '%';
        $deespest_category  = tep_get_deepest_category($categories['categories_id']);

        $products_in_category = tep_count_products_in_category($deespest_category);
		if (((!tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			if($categories['categories_image'] != ''){
				$display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;
					
					if(USE_FRONTEND_CATEGORIES == 'true'){
						$image_query = tep_db_query("SELECT p.products_image, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . "  order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_image, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					
					if (tep_not_null($image['products_image'])){
						$pos = stripos($image['products_image'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_image'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_image'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_small_image($image['products_image'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					if ($temp[count($temp)-1]==''){
						array_pop($temp);
					}
					$count++;
				}
			}
		}
		
		echo '<td class="cat" width="' . $width . '" valign="top" align="center">';
		echo '<table cellpadding="2" cellspacing="0" width="100%" align="center">';
// START changed from lines above for category name capitilization -cj 09272015
//		echo '<tr><td align="center" height="45" valign="bottom"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ucwords(strtolower($categories['categories_name'])) . '</td></tr>';
		echo '<tr><td align="center" height="45" valign="bottom"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</td></tr>';
// END changed from lines above for category name capitilization -cj 09272015
		echo '<tr><td align="center" valign="bottom">' .  '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" >' .$display_image. '</a></td></tr></table></td>' . "\n";
		if ((($rows / MAX_DISPLAY_CATEGORIES_PER_ROW) == floor($rows / MAX_DISPLAY_CATEGORIES_PER_ROW)) && ($rows != $number_of_categories)) {
			echo '              </tr></table></td></tr><tr><td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td></tr>' . "\n";
			echo '              <tr><td><table class="subcat catlisting_4" cellpadding="0" cellspacing="0" width="100%"><tr>' . "\n";
		}
	}
} elseif($selected_category_template == 1) {
    echo $category_image_query . '<br />' . '<table cellpadding="0" cellspacing="" width="100%" align="center" style="background: #fff; text-align: center;" class="catlisting_3"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
	while ($categories = tep_db_fetch_array($categories_query)){
        $rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / 3) . '%';
        $deespest_category  = tep_get_deepest_category($categories['categories_id']);
		
		$products_in_category = tep_count_products_in_category($deespest_category);
        if (((tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			
			if($categories['categories_image'] != ''){
                $display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;
					
					if(USE_FRONTEND_CATEGORIES == 'true'){
						$image_query = tep_db_query("SELECT p.products_mediumimage, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_mediumimage, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					if (tep_not_null($image['products_mediumimage'])){
						$pos = stripos($image['products_mediumimage'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_mediumimage'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_mediumimage'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_medium_image($image['products_mediumimage'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					if ($temp[count($temp)-1]==''){
						array_pop($temp);
					}
					$count++;
				}
			}
		
		}
		echo '<td width="' . $width . '" valign="top" align="center" class="category_cell">';
		echo '  <table cellpadding="2" cellspacing="0" width="100%" align="left">';
		echo '    <tr height="200px">';
		echo '      <td align="center" valign="bottom">' .  '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" >' . $display_image . '</a></td>';
		echo '    </tr>';
		echo '    <tr>';
		echo '      <td align="center" height="45" valign="bottom"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</td>';
		echo '    </tr>';
		echo '  </table>';
		echo '</td>' . "\n";
        if ((($rows / 3) == floor($rows / 3)) && ($rows != $number_of_categories)){
            echo '      </tr>';
			echo '    </table>';
			echo '  </td>';
			echo '</tr>';
			echo '<tr>';
			echo '  <td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>';
			echo '</tr>' . "\n";
            echo '<tr>';
			echo '  <td>';
			echo '    <table cellpadding="0" cellspacing="0" width="100%" style="background: #fff; text-align: center;" class="catlisting_3">';
			echo '      <tr>' . "\n";
			
		}
		$row_count++;
	}
	if($row_count % 3 == 1){
		echo '<td width="' . $width . '" valign="top" align="center"><table cellpadding="2" cellspacing="0" width="100%" align="left"><tr><td align="center" valign="bottom"></td></tr><tr><td></td></tr></table></td>' . "\n";
		echo '<td width="' . $width . '" valign="top" align="center"><table cellpadding="2" cellspacing="0" width="100%" align="left"><tr><td align="center" valign="bottom"></td></tr><tr><td></td></tr></table></td>' . "\n";
	}
	if($row_count % 3 == 2){
		echo '<td width="' . $width . '" valign="top" align="center"><table cellpadding="2" cellspacing="0" width="100%" align="left"><tr><td align="center" valign="bottom"></td></tr><tr><td></td></tr></table></td>' . "\n";
	}
} elseif($selected_category_template == 2) {
	echo '<table cellpadding="0" cellspacing="" width="100%" align="center" style="background: #fff; text-align: center;"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)){
        $rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / 2) . '%';
        $deespest_category  = tep_get_deepest_category($categories['categories_id']);

        $products_in_category = tep_count_products_in_category($deespest_category);
        if (((!tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			if($categories['categories_image'] != ''){
                $display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;

					if(USE_FRONTEND_CATEGORIES == 'true') {
						$image_query = tep_db_query("SELECT p.products_mediumimage, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . "  order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_mediumimage, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					if (tep_not_null($image['products_mediumimage'])){
						$pos = stripos($image['products_mediumimage'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_mediumimage'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_mediumimage'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_medium_image($image['products_mediumimage'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					if ($temp[count($temp)-1]==''){
						array_pop($temp);
					}
					$count++;
				}

			}
		}

		echo '<td width="' . $width . '" valign="top" align="center">';
		echo '  <table cellpadding="2" cellspacing="0" width="100%" align="left">';
		echo '    <tr>';
		echo '      <td align="center" valign="bottom">' .  '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" >' . $display_image . '</a></td>';
		echo '    </tr>';
		echo '    <tr>';
		echo '      <td align="center" height="45" valign="bottom"><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</td>';
		echo '    </tr>';
		echo '  </table>';
		echo '</td>' . "\n";
        if ((($rows / 2) == floor($rows / 2)) && ($rows != $number_of_categories)){
            echo '      </tr>';
			echo '    </table>';
			echo '  </td>';
			echo '</tr>';
			echo '<tr>';
			echo '  <td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>';
			echo '</tr>' . "\n";
            echo '<tr>';
			echo '  <td>';
			echo '    <table cellpadding="0" cellspacing="0" width="100%" style="background: #fff; text-align: center;">';
			echo '      <tr>' . "\n";
		}
		$row_count++;
	}
	if($row_count % 2 == 1)
		echo '<td width="' . $width . '" valign="top" align="center"><table cellpadding="2" cellspacing="0" width="100%" align="left"><tr><td align="center" valign="bottom"></td></tr><tr><td></td></tr></table></td>' . "\n";
} elseif($selected_category_template == 3) {
	echo '<table cellpadding="0" cellspacing="" width="100%" align="left" style="background: #fff; text-align: left;"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)){
        $rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / 1) . '%';
        $deespest_category  = tep_get_deepest_category($categories['categories_id']);

        $products_in_category = tep_count_products_in_category($deespest_category);
        if (((!tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			if($categories['categories_image'] != ''){
				$display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;
					
					if(USE_FRONTEND_CATEGORIES == 'true'){
						$image_query = tep_db_query("SELECT p.products_image, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . "  order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_image, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					
					if (tep_not_null($image['products_image'])){
						$pos = stripos($image['products_image'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_image'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_image'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_small_image($image['products_image'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					if ($temp[count($temp)-1]==''){
						array_pop($temp);
					}
					$count++;
				}
			}
		}
		echo '<td width="' . $width . '" valign="top" align="left">';
		echo '  <table cellpadding="2" cellspacing="0" width="100%" align="left">';
		echo '    <tr>';
		echo '      <td align="left" valign="bottom">' .  '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" >' . $display_image . '</a> <a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</a></td>';
		echo '    </tr>';
		echo '  </table>';
		echo '</td>' . "\n";
        echo '      </tr>';
        echo '    </table>';
		echo '  </td>';
		echo '</tr>';
		echo '<tr>';
		echo '  <td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>';
		echo '</tr>' . "\n";
		echo '<tr>';
		echo '  <td>';
		echo '    <table cellpadding="0" cellspacing="0" width="100%" style="background: #fff; text-align: left;">';
		echo '      <tr>' . "\n";
	}
} elseif($selected_category_template == 4) {
    echo '<table cellpadding="0" cellspacing="" width="100%" align="left" style="background: #fff; text-align: left;"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)){
        $rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / 1) . '%';
        $deespest_category  = tep_get_deepest_category($categories['categories_id']);

        $products_in_category = tep_count_products_in_category($deespest_category);
        if (((!tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			if($categories['categories_image'] != ''){
				$display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;
					
					if(USE_FRONTEND_CATEGORIES == 'true'){
						$image_query = tep_db_query("SELECT p.products_mediumimage, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_mediumimage, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_mediumimage<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					if (tep_not_null($image['products_mediumimage'])){
						$pos = stripos($image['products_mediumimage'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_mediumimage'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_mediumimage'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_medium_image($image['products_mediumimage'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					if ($temp[count($temp)-1]==''){
						array_pop($temp);
					}
					$count++;
				}
			}
		}

		echo '<td width="' . $width . '" valign="top" align="left">';
		echo '  <table cellpadding="2" cellspacing="0" width="100%" align="left">';
		echo '    <tr>';
		echo '      <td align="left" valign="bottom">' .  '<a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" >' . str_replace("/small/", "/medium/", $display_image) . '</a> <a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</a></td>';
		echo '    </tr>';
		echo '  </table>';
		echo '</td>' . "\n";
        echo '      </tr>';
        echo '    </table>';
		echo '  </td>';
		echo '</tr>';
		echo '<tr>';
		echo '  <td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>';
		echo '</tr>' . "\n";
		echo '<tr>';
		echo '  <td>';
		echo '    <table cellpadding="0" cellspacing="0" width="100%" style="background: #fff; text-align: left;">';
		echo '      <tr>' . "\n";
	}
} elseif($selected_category_template == 5) {
    echo '<table cellpadding="0" cellspacing="" width="100%" align="left" style="background: #fff; text-align: left;"><tr>';
    $number_of_categories = tep_db_num_rows($categories_query);
    $rows = 0;
    while ($categories = tep_db_fetch_array($categories_query)) {
        $rows++;
		$display_image = '';
        $cPath_new = tep_get_path($categories['categories_id']);
        $width = (100 / 1) . '%';
        /*$deespest_category  = tep_get_deepest_category($categories['categories_id']);

        $products_in_category = tep_count_products_in_category($deespest_category);
        if (((!tep_not_null($categories['categories_image'])) || $categories['categories_image'] == '') && $products_in_category > 0){
			if($categories['categories_image'] != ''){
				$display_image = '<img src="images/'.$categories['categories_image'].'" class="subcatimages" border="0" />';
			} else {
				$count = 0;
				$temp = array();
				while (empty($display_image) && $count<=10){
					$image_ok = false;
					
					if(USE_FRONTEND_CATEGORIES == 'true'){
						$image_query = tep_db_query("SELECT p.products_image, p.products_id FROM frontend_products_to_categories fp2c JOIN products p ON p.products_id = fp2c.products_id WHERE fp2c.categories_id = '".(int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . "  order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					} else {
						$image_query = tep_db_query("select p.products_image, p.products_id from products p, products_to_categories p2c where p.products_id = p2c.products_id and p2c.categories_id = '" . (int)$deespest_category . "' and p.products_image<>'' " . (!empty($temp) ? " and p.products_id not in (" . implode(', ', $temp) . ") " : "") . " order by rand() limit 1");
						$image = tep_db_fetch_array($image_query);
					}
					
					if (tep_not_null($image['products_image'])){
						$pos = stripos($image['products_image'], 'http');
						if ($pos!==false && $pos===0){
							if (@file_get_contents($image['products_image'])){
								$image_ok = true;
							}
						} elseif (file_exists(DIR_FS_CATALOG . DIR_WS_IMAGES . $image['products_image'])) {
							$image_ok = true;
						}
						if ($image_ok){
							$display_image = tep_small_image($image['products_image'], $categories['categories_name'], 150, 150, 'class="subcatimages"');
						}
					}
					if (!$image_ok) $temp[] = $image['products_id'];
					$count++;
				}
			}
		}*/
		echo '<td width="' . $width . '" valign="top" align="left">';
		echo '  <table cellpadding="2" cellspacing="0" width="100%" align="left">';
		echo '    <tr>';
		echo '      <td align="left" valign="bottom"><ul style="margin: 0px 0px 0px 20px; padding: 0px;"><li><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath_new) . '" class="subcatheader">' . ($categories['categories_name']) . '</a></li></ul></td>';
		echo '    </tr>';
		echo '  </table>';
		echo '</td>' . "\n";
        echo '      </tr>';
        echo '    </table>';
		echo '  </td>';
		echo '</tr>';
		echo '<tr>';
		echo '  <td>' .tep_draw_separator('pixel_trans.gif', '1', '10') . '</td>';
		echo '</tr>' . "\n";
		echo '<tr>';
		echo '  <td>';
		echo '    <table cellpadding="0" cellspacing="0" width="100%" style="background: #fff; text-align: left;">';
		echo '      <tr>' . "\n";
	}
}
?>
	</tr>
</table>
<?php
function tep_get_deepest_category($parent_id = '0') {
	$categories_query = tep_db_query("select categories_id from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$parent_id . "' order by sort_order");
	if (tep_db_num_rows($categories_query) > 0) {
		while ($categories = tep_db_fetch_array($categories_query)) {
			$categories_query2 = tep_db_query("select count(*) as total from " . TABLE_CATEGORIES . " where parent_id = '" . (int)$categories['categories_id']. "' order by sort_order");
			$categories2 = tep_db_fetch_array($categories_query2);
			if ($categories2['total'] >0) {
				$deepest_category  = tep_get_deepest_category($categories['categories_id']);
				if ($deepest_category >0) break;
			} else {
				$deepest_category = 	$categories['categories_id'];
				break;
			}
		} 
	} else {
		$deepest_category = $parent_id;
	}
    return $deepest_category;
}
?>