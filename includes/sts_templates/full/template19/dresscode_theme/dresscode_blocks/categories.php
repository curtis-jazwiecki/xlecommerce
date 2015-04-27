<div class="et_categ_box">
    <div id="et_categ_box_scroll">

<?php
$categ_query = tep_db_query("select distinct c.categories_id, c.parent_id, cd.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c, ".TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and c.parent_id = '0' and cd.language_id='" . (int)$languages_id ."' order by sort_order, cd.categories_name ");
    $categories_menu_output .= '<div class="img_link_wrapper"><a class="image-link" href="'. tep_href_link(FILENAME_DEFAULT).'"></a></div>';
			while ($cat = tep_db_fetch_array($categ_query)) {
				 $cPath_new = 'cPath=' .$cat['categories_id'];

	/* parent category output */

                /* begin demand when category name all languages */
                if ($cat['categories_name'] !== "") {
                    $categories_menu_output .= '<div class="cat-name">
				 <a class="main_category" href="'.tep_href_link(FILENAME_DEFAULT, $cPath_new).'">'.$cat['categories_name'].'</a>';
				 $current_cat_id = $cat['categories_id'];

	/* sub category output */

				 $categories_query_sub = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$current_cat_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");

				if (tep_db_num_rows($categories_query_sub) > 0) {

                    $categories_menu_output .= '<div class="drop-box-subcat">';
					while ($sub_cat = tep_db_fetch_array($categories_query_sub)) {

                        /* begin demand when subcategory name all languages */
                        if ($sub_cat['categories_name'] !== "") {
                            $categories_menu_output .= '<div class="sub-cat-name">
						<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$cat['categories_id'].'_'.$sub_cat['categories_id']).'">'.$sub_cat['categories_name'].'</a>';
						/* sub sub cat */

						$last_subcat_id = $sub_cat['categories_id'];
						$subcategories_query_sub = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$last_subcat_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
						if (tep_db_num_rows($subcategories_query_sub) > 0) {
                            $categories_menu_output .= '<div class="drop-box-subsubcat responsive_position">';
							while ($sub_subcat = tep_db_fetch_array($subcategories_query_sub)) {

                                /* begin demand when sub sub category name all languages */
                                if ($sub_subcat['categories_name'] !== "") {
                                    $categories_menu_output .= '<div class="subsub-cat-name">
                                <a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$sub_subcat['categories_id']).'">'.$sub_subcat['categories_name'].'</a>';

								/* 3 sub cat */ 
								$last_subsubcat_id = $sub_subcat['categories_id'];
								$subcategories_query_3sub = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$last_subsubcat_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");

								/* display block */

								if (tep_db_num_rows($subcategories_query_3sub) > 0) {
                                    $categories_menu_output .= '<div class="drop-box-3subcat responsive_position">';
									while ($sub_3subcat = tep_db_fetch_array($subcategories_query_3sub)) {
                                        $categories_menu_output .= '<div class="3sub-cat-name">
										<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$cat['categories_id'].'_'.$sub_cat['categories_id']).'_'.$sub_subcat['categories_id'].'_'.$sub_3subcat['categories_id'].'">'.$sub_3subcat['categories_name'].'</a>';

										/* 4 sub cat */
										$last_3cat_id = $sub_3subcat['categories_id'];
										$subcategories_query_4sub = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.parent_id = '" . (int)$last_3cat_id . "' and c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' order by sort_order, cd.categories_name");
										/* display block */

										if (tep_db_num_rows($subcategories_query_4sub) > 0) {
                                            $categories_menu_output .= '<div class="drop-box-4subcat">';
											while ($sub_4subcat = tep_db_fetch_array($subcategories_query_4sub)) {
                                                $categories_menu_output .= '<a href="'.tep_href_link(FILENAME_DEFAULT, 'cPath='.$cat['categories_id'].'_'.$sub_cat['categories_id']).'_'.$sub_subcat['categories_id'].'_'.$sub_3subcat['categories_id'].'_'.$sub_4subcat['categories_id'].'">'.$sub_4subcat['categories_name'].'</a>';
											}
                                            $categories_menu_output .= '</div>';
										}
                                        $categories_menu_output .= '</div>';

										/* 4 sub cat */
									}
                                    $categories_menu_output .= '</div>';
								}

								/* display block */

								/* 3 sub cat end  */
                                    $categories_menu_output .= '</div>';
                            }
                            /* end demand when sub sub category name all languages */
							}
                            $categories_menu_output .= '</div>';
						}
                            $categories_menu_output .= '</div>';
						/* sub sub cat */
                        }
                        /* end demand when subcategory name all languages*/
					}
                    $categories_menu_output .= '</div>';
                }
				}
                /* end demand when category name all languages */

                $categories_menu_output .= '</div>';
			}



            echo $categories_menu_output;

?>
        <div class="cat-name blog-link"><a class="main_category" href="<?php echo BLOG_MENU_LINK; ?>"><?php echo BLOG_MENU_ITEM; ?></a></div>

</div>
    </div>