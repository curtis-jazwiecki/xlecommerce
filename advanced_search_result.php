<?php
/*
$Id: advanced_search_result.php,v 1.72 2003/06/23 06:50:11 project3000 Exp $
osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com
Copyright (c) 2003 osCommerce
Released under the GNU General Public License
*/

require ('includes/application_top.php');
require (DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADVANCED_SEARCH);

if ($_POST){
    if (isset($_POST['p_'])) $_SESSION['filter_p'] = $_POST['p_'];
    if (isset($_POST['m_'])) $_SESSION['filter_m'] = $_POST['m_'];
    $_SESSION['filter_s'] = isset($_POST['s_']) ? $_POST['s_'] : '';
}

$keywords = $_GET['keywords'];
if (!empty($keywords)){
    $_SESSION['filter_p'] = $_SESSION['filter_m'] = $_SESSION['filter_s'] = $_SESSION['filter_c'] = '';
}


if (!empty($_GET['pfrom']) && !empty($_GET['pto'])){
	$pfrom = (int)$_GET['pfrom'];
	$pto = (int)$_GET['pto'];
} elseif (!empty($_SESSION['filter_p'])){
	$temp = explode('|', $_SESSION['filter_p']);
	$pfrom = (int)$temp[0];
	$pto = (int)$temp[1];
} else {
	$pfrom = null;
	$pto = null;
}


if (isset($keywords) && $keywords != ''){
    $pw_keywords = explode(' ', stripslashes(strtolower($keywords)));
    $pw_replacement_words = $pw_keywords;
    $pw_boldwords = $pw_keywords;
    $sql_words = tep_db_query("SELECT * FROM searchword_swap");
    $pw_replacement = array();
    $pw_link_text = array();
    while ($sql_words_result = tep_db_fetch_array($sql_words)){
        if (stripslashes(strtolower($keywords)) == stripslashes(strtolower($sql_words_result['sws_word']))){
            $pw_replacement[] = stripslashes($sql_words_result['sws_replacement']);
            $pw_link_text[] = '<b><i>' . stripslashes($sql_words_result['sws_replacement']) . '</i></b>';
            $pw_mispell = 1;
        }

        if (!$pw_replacement){
            for ($i = 0; $i < sizeof($pw_keywords); $i++){
                if ($pw_keywords[$i] == stripslashes(strtolower($sql_words_result['sws_word']))){
                    $pw_replacement[] = stripslashes($sql_words_result['sws_replacement']);
                    $pw_link_text[] = '<b><i>' . stripslashes($sql_words_result['sws_replacement']) . '</i></b>';
                    $pw_mispell = 1;
                }
            }
        }
    }

    if ($pw_replacement){
        $pw_string = '<br><span class="main"><font color="red">' . TEXT_REPLACEMENT_SUGGESTION . '</font>';
    }

    for($i=0; $i<sizeof($pw_replacement); $i++){
        $pw_string .= '<span><a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'keywords=' . urlencode($pw_replacement[$i]) . '&search_in_description=1') . '">' . $pw_link_text[$i] . '</a></span>&nbsp;&nbsp;';
    }

    $pw_string .= '<br><br>';
}

$error = false;

if ((isset($keywords) && empty($keywords)) && (isset($HTTP_GET_VARS['dfrom']) && (empty($HTTP_GET_VARS['dfrom']) || ($HTTP_GET_VARS['dfrom'] == DOB_FORMAT_STRING))) && (isset($HTTP_GET_VARS['dto']) && (empty($HTTP_GET_VARS['dto']) || ($HTTP_GET_VARS['dto'] == DOB_FORMAT_STRING))) && (isset($pfrom) && !is_numeric($pfrom)) && (isset($pto) && ! is_numeric($pto))) {
    $error = true;
    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
} else {
    $dfrom = '';
    $dto = '';
    if (isset($HTTP_GET_VARS['dfrom'])){
        $dfrom = (($HTTP_GET_VARS['dfrom'] == DOB_FORMAT_STRING) ? '' : $HTTP_GET_VARS['dfrom']);
    }

    if (isset($HTTP_GET_VARS['dto'])){
        $dto = (($HTTP_GET_VARS['dto'] == DOB_FORMAT_STRING) ? '' : $HTTP_GET_VARS['dto']);
    }

    $date_check_error = false;

    if (tep_not_null($dfrom)){
        if (!tep_checkdate($dfrom, DOB_FORMAT_STRING, $dfrom_array)){
            $error = true;
            $date_check_error = true;
            $messageStack->add_session('search', ERROR_INVALID_FROM_DATE);
        }
    }

    if (tep_not_null($dto)){
        if (!tep_checkdate($dto, DOB_FORMAT_STRING, $dto_array)){
            $error = true;
            $date_check_error = true;
            $messageStack->add_session('search', ERROR_INVALID_TO_DATE);
        }
    }

    if (($date_check_error == false) && tep_not_null($dfrom) && tep_not_null($dto)){
        if (mktime(0, 0, 0, $dfrom_array[1], $dfrom_array[2], $dfrom_array[0]) > mktime(0, 0, 0, $dto_array[1], $dto_array[2], $dto_array[0])){
            $error = true;
            $messageStack->add_session('search', ERROR_TO_DATE_LESS_THAN_FROM_DATE);
        }
    }

    $price_check_error = false;

    if (tep_not_null($pfrom)){
        if (!settype($pfrom, 'double')){
            $error = true;
            $price_check_error = true;
            $messageStack->add_session('search', ERROR_PRICE_FROM_MUST_BE_NUM);
        }
    }

    if (tep_not_null($pto)){
        if (!settype($pto, 'double')){
            $error = true;
            $price_check_error = true;
            $messageStack->add_session('search', ERROR_PRICE_TO_MUST_BE_NUM);
        }
    }

    if (($price_check_error == false) && is_float($pfrom) && is_float($pto)){
        if ($pfrom >= $pto){
            $error = true;
            $messageStack->add_session('search', ERROR_PRICE_TO_LESS_THAN_PRICE_FROM);
        }
    }

    if (tep_not_null($keywords)){
        if (!tep_parse_search_string($keywords, $search_keywords)){
            $error = true;
            $messageStack->add_session('search', ERROR_INVALID_KEYWORDS);
        }
    }
}

if (empty($dfrom) && empty($dto) && empty($pfrom) && empty($pto) && empty($keywords) && empty($_SESSION['filter_p']) && empty($_SESSION['filter_m']) && empty($_SESSION['filter_s']) && empty($_SESSION['filter_k']) && empty($_SESSION['filter_c']) && empty($HTTP_GET_VARS['manufacturers_id'])){
    $error = true;
    $messageStack->add_session('search', ERROR_AT_LEAST_ONE_INPUT);
}

if ($error == true){
    tep_redirect(tep_href_link(FILENAME_ADVANCED_SEARCH, tep_get_all_get_params(), 'NONSSL', true, false));
}

// Search enhancement mod start
$search_enhancements_keywords = $keywords;
$search_enhancements_keywords = strip_tags($search_enhancements_keywords);
$search_enhancements_keywords = addslashes($search_enhancements_keywords);

if ($search_enhancements_keywords != $last_search_insert){
    tep_db_query("insert into search_queries (search_text)  values ('" . $search_enhancements_keywords . "')");
    tep_session_register('last_search_insert');
    $last_search_insert = $search_enhancements_keywords;
}
// Search enhancement mod end

$breadcrumb->add(NAVBAR_TITLE_1, tep_href_link(FILENAME_ADVANCED_SEARCH));
$breadcrumb->add(NAVBAR_TITLE_2, tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT,

tep_get_all_get_params(), 'NONSSL', true, false));
?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
    <html <?php echo HTML_PARAMS; ?>>
    <head>
<?php

// BOF: Header Tag Controller v2.6.0
if (file_exists(DIR_WS_INCLUDES . 'header_tags.php')) {
    require (DIR_WS_INCLUDES . 'header_tags.php');
} else {
?> 
  <title><?php echo TITLE; ?></title>
<?php
}
// EOF: Header Tag Controller v2.6.0
?>
        <style type="text/css">
        $stylesheet
        </style>
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
    <!-- header //-->
    <?php require (DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
        <table border="0" width="100%" cellspacing="3" cellpadding="3">
            <tr>
                <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                    <!-- left_navigation //-->
                    <?php require (DIR_WS_INCLUDES . 'column_left.php'); ?>
                    <!-- left_navigation_eof //-->
                    </table>
                </td>
                <!-- body_text //-->
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td class="pageHeading"><?php echo HEADING_TITLE_2; ?></td>
                                        <td class="pageHeading" align="right"><?php //echo tep_image(DIR_WS_IMAGES . '', HEADING_TITLE_2, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
                                    </tr>
                                    <tr>
                                        <td class="main">
                                            <p>
                                            <?php
                                            if (isset($HTTP_GET_VARS['plural']) && ($HTTP_GET_VARS['plural'] == '1')) {
                                                echo TEXT_REPLACEMENT_SEARCH_RESULTS . ' <b><i>' . stripslashes($keywords) . 's</i></b>';
                                            } else {
                                                echo TEXT_REPLACEMENT_SEARCH_RESULTS . ' <b><i>' . stripslashes($keywords) . '</i></b>';
                                            }
                                            ?>
                                            </p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                        </tr>
                        <tr>
                            <td>
                            <?php
                            // create column list
                            $define_list = array(
                                'PRODUCT_LIST_MODEL' => PRODUCT_LIST_MODEL,
                                'PRODUCT_LIST_NAME' => PRODUCT_LIST_NAME,
                                'PRODUCT_LIST_MANUFACTURER' => PRODUCT_LIST_MANUFACTURER,
                                'PRODUCT_LIST_PRICE' => PRODUCT_LIST_PRICE,
                                'PRODUCT_LIST_QUANTITY' => PRODUCT_LIST_QUANTITY,
                                'PRODUCT_LIST_WEIGHT' => PRODUCT_LIST_WEIGHT,
                                'PRODUCT_LIST_IMAGE' => PRODUCT_LIST_IMAGE,
                                'PRODUCT_LIST_BUY_NOW' => PRODUCT_LIST_BUY_NOW
                            );

                            asort($define_list);
                            $column_list = array();
                            reset($define_list);

                            while (list($key, $value) = each($define_list)){
                                if ($value > 0) $column_list[] = $key;
                            }

                            // BOF Separate Pricing Per Customer
                            if (isset($_SESSION['sppc_customer_group_id']) && $_SESSION['sppc_customer_group_id'] != '0') {
                                $customer_group_id = $_SESSION['sppc_customer_group_id'];
                            } else {
                                $customer_group_id = '0';
                            }
                            // EOF Separate Pricing Per Customer

                            $select_column_list = '';
                            $select_column_list = "p.sold_in_bundle_only, ";

                            for ($i = 0, $n = sizeof($column_list); $i < $n; $i++) {
                                switch ($column_list[$i]) {
                                    case 'PRODUCT_LIST_MODEL':
                                        $select_column_list .= 'p.products_model, ';
                                        break;
                                    case 'PRODUCT_LIST_MANUFACTURER':
                                        $select_column_list .= 'm.manufacturers_name, ';
                                        break;
                                    case 'PRODUCT_LIST_QUANTITY':
                                        $select_column_list .= 'p.products_quantity, ';
                                        break;
                                    case 'PRODUCT_LIST_IMAGE':
                                        $select_column_list .= 'p.products_image, p.products_mediumimage, ';
                                        break;
                                    case 'PRODUCT_LIST_WEIGHT':
                                        $select_column_list .= 'p.products_weight, ';
                                        break;
                                }
                            }

                            // BOF Separate Pricing Per Customer
                            $status_tmp_product_prices_table = false;
                            $status_need_to_get_prices = false;

                            // find out if sorting by price has been requested
                            if ((isset($HTTP_GET_VARS['sort'])) && (preg_match('/[1-8][ad]/', $HTTP_GET_VARS['sort'])) && (substr($HTTP_GET_VARS['sort'], 0, 1) <= sizeof($column_list))) {
                                $_sort_col = substr($HTTP_GET_VARS['sort'], 0, 1);
                                if ($column_list[$_sort_col - 1] == 'PRODUCT_LIST_PRICE') {
                                    $status_need_to_get_prices = true;
                            }
                            }

                            if ((tep_not_null($pfrom) || tep_not_null($pto) || $status_need_to_get_prices == true) && $customer_group_id != '0') {
                                $product_prices_table = TABLE_PRODUCTS_GROUP_PRICES . $customer_group_id;
                                // the table with product prices for a particular customer group is re-built only a number of times per hour
                                // (setting in /includes/database_tables.php called MAXIMUM_DELAY_UPDATE_PG_PRICES_TABLE, in minutes)
                                // to trigger the update the next function is called (new function that should have been
                                // added to includes/functions/database.php)
                                tep_db_check_age_products_group_prices_cg_table($customer_group_id);
                                $status_tmp_product_prices_table = true;
                            } elseif ((tep_not_null($pfrom) || tep_not_null($pto) || $status_need_to_get_prices == true) && $customer_group_id == '0') {
                                // to be able to sort on retail prices we *need* to get the special prices instead of leaving them
                                // NULL and do product_listing the job of getting the special price
                                // first make sure that table exists and needs no updating
                                tep_db_check_age_specials_retail_table();
                                $status_tmp_special_prices_table = true;
                            }

                            if ($status_tmp_product_prices_table == true) {
                                //$select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, pd.products_specifications, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price ";
                                $select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price ";
                            } elseif ($status_tmp_special_prices_table == true){
                                //$select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, pd.products_specifications, if(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, if(s.status, s.specials_new_products_price, p.products_price) as final_price ";
                                $select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, if(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, if(s.status, s.specials_new_products_price, p.products_price) as final_price ";
                            } else{
                                //$select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, pd.products_specifications, NULL as specials_new_products_price, NULL as final_price ";
                                $select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, p.parent_products_model, NULL as specials_new_products_price, NULL as final_price ";
                            }
                            // next line original select query
                            // $select_str = "select distinct " . $select_column_list . " m.manufacturers_id, p.products_id, pd.products_name, p.products_price, p.products_tax_class_id, IF(s.status, s.specials_new_products_price, NULL) as specials_new_products_price, IF(s.status, s.specials_new_products_price, p.products_price) as final_price ";

                            if ((DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null ($pto))) {
                                $select_str .= ", SUM(tr.tax_rate) as tax_rate ";
                            }

                            if ($status_tmp_product_prices_table == true) {
                                $from_str = "from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id) left join " . $product_prices_table . " as tmp_pp using(products_id)";
                            } elseif ($status_tmp_special_prices_table == true) {
                                $from_str = "from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id) left join " . TABLE_SPECIALS_RETAIL_PRICES . " s on p.products_id = s.products_id ";
                            } else {
                                $from_str = "from " . TABLE_PRODUCTS . " p left join " . TABLE_MANUFACTURERS . " m using(manufacturers_id) ";
                            }
                            // EOF Separate Pricing Per Customer

                            $from_str .= " left join " . TABLE_SPECIALS . " s1 on p.products_id=s1.products_id ";

                            if ((DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null ($pto))) {
                                if (!tep_session_is_registered('customer_country_id')) {
                                    $customer_country_id = STORE_COUNTRY;
                                    $customer_zone_id = STORE_ZONE;
                                }

                                $from_str .= " left join " . TABLE_TAX_RATES . " tr on p.products_tax_class_id = tr.tax_class_id left join " . TABLE_ZONES_TO_GEO_ZONES . " gz on tr.tax_zone_id = gz.geo_zone_id and (gz.zone_country_id is null or gz.zone_country_id = '0' or gz.zone_country_id = '" . (int) $customer_country_id . "') and (gz.zone_id is null or gz.zone_id = '0' or gz.zone_id = '" . (int)$customer_zone_id . "')";
                            }

                            $from_str .= ", " . TABLE_PRODUCTS_DESCRIPTION . " pd, " . TABLE_CATEGORIES . " c, " . TABLE_PRODUCTS_TO_CATEGORIES . " p2c";

                            if (!empty($_SESSION['filter_s'])){
                                //$from_str .= " , products_attributes pa ";
                            }


                            $attributes_query_raw = "select distinct products_options_values_id from products_options_values where";
                            $options_str = '';

                            $where_str = " where c.categories_status = '1' and p.products_status = '1' and p.products_id = pd.products_id and pd.language_id = '" . (int)$languages_id . "' and p.products_id = p2c.products_id and p2c.categories_id = c.categories_id ";

                            if (isset($HTTP_GET_VARS['attributes_name']) && isset($HTTP_GET_VARS['attributes_value'])) {
                                $products_specifications_array = explode(" ", $HTTP_GET_VARS['attributes_value']);
                                $products_specifications_array = explode(" ", $HTTP_GET_VARS['attributes_name']);
                                for ($a = 0; $a < sizeof($products_specifications_array); $a++) {
                                    $where_str = $where_str . " AND (pd.products_specifications LIKE '%" . mysql_real_escape_string($HTTP_GET_VARS['attributes_value']) . "%' AND pd.products_specifications LIKE '%" . mysql_real_escape_string($HTTP_GET_VARS['attributes_name']) . "%')";
                                }
                            }

                            if (isset($HTTP_GET_VARS['categories_id']) && tep_not_null($HTTP_GET_VARS['categories_id'])) {
                                if (isset($HTTP_GET_VARS['inc_subcat']) && ($HTTP_GET_VARS['inc_subcat'] == '1')) {
                                    $subcategories_array = array();
                                    tep_get_subcategories($subcategories_array, $HTTP_GET_VARS['categories_id']);
                                    $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and (p2c.categories_id = '" . (int) $HTTP_GET_VARS['categories_id'] . "'";

                                    for ($i = 0, $n = sizeof($subcategories_array); $i < $n; $i++) {
                                        $where_str .= " or p2c.categories_id = '" . (int)$subcategories_array[$i] . "'";
                                    }
                                    $where_str .= ")";
                                } else {
                                    $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . (int) $languages_id . "' and p2c.categories_id = '" . (int)$HTTP_GET_VARS['categories_id'] . "'";
                                }

                            } elseif (isset($_SESSION['filter_c']) && !empty($_SESSION['filter_c'])){
	                            if (strrpos($_SESSION['filter_c'], '_')!==false){
                                    $temp = substr($_SESSION['filter_c'], strrpos($_SESSION['filter_c'], '_')+1 );
                                } else {
                                    $temp = $_SESSION['filter_c'];
                                }

                                $where_str .= " and p2c.products_id = p.products_id and p2c.products_id = pd.products_id and pd.language_id = '" . (int) $languages_id . "' and p2c.categories_id = '" . (int)$temp . "'";
                            }

                            if (isset($HTTP_GET_VARS['manufacturers_id']) && tep_not_null($HTTP_GET_VARS['manufacturers_id'])) {
                                $where_str .= " and m.manufacturers_id = '" . (int)$HTTP_GET_VARS['manufacturers_id'] . "'";
                            } elseif (isset($_POST['m_']) && !empty($_POST['m_'])){
                                $where_str .= " and m.manufacturers_id in (" . implode(',', $_POST['m_']) . ") ";
                            }

                            if (($_GET['keywords'] != "" && $_GET['keywords'] != null)) {
                                if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
                                    $where_str .= " and ((";
                                    for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i++) {
                                        switch ($search_keywords[$i]) {
                                            case '(':
                                            case ')':
                                            case 'and':
                                            case 'or':
                                                $where_str .= " " . $search_keywords[$i] . " ";
                                                break;
                                            default:
                                                $keyword = tep_db_prepare_input($search_keywords[$i]);
                                                //$where_str .= "(pd.products_name like '%" . tep_db_input($keyword) . "%' or p.products_model like '%" . tep_db_input($keyword) . "%' or m.manufacturers_name like '%" . tep_db_input($keyword) . "%' OR pd.products_specifications LIKE '%" . tep_db_input($keyword) . "%' or pd.products_description like '%" . tep_db_input($keyword) . "%'";
                                                $where_str .= "(pd.products_name like '%" . tep_db_input($keyword) . "%' or p.products_model like '%" . tep_db_input($keyword) . "%' or m.manufacturers_name like '%" . tep_db_input($keyword) . "%'";
                                                $where_str .= ')';
                                                break;
                                        }
                                    }
                                }
                                $where_str .= " )";
                                if ($options_str != '') {
                                    $where_str .= " or (pa.options_values_id in (" . $options_str . ") and p.products_id = pa.products_id)";

                                }
                                $where_str .= " )";
                            }

                            if (tep_not_null($dfrom)) {
                                $where_str .= " and p.products_date_added >= '" . tep_date_raw($dfrom) . "'";
                            }

                            if (tep_not_null($dto)) {
                                $where_str .= " and p.products_date_added <= '" . tep_date_raw($dto) . "'";
                            }

                            if (tep_not_null($pfrom)) {
                                if ($currencies->is_set($currency)) {
                                    $rate = $currencies->get_value($currency);
                                    $pfrom = $pfrom / $rate;
                                }
                            }

                            if (tep_not_null($pto)) {
                                if (isset($rate)) {
                                    $pto = $pto / $rate;
                                }
                            }

                            // BOF Separate Pricing Per Customer

                            if ($status_tmp_product_prices_table == true) {
                                if (DISPLAY_PRICE_WITH_TAX == 'true') {
                                    if ($pfrom > 0)
                                        $where_str .= " and (IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= " . (double)$pfrom . ")";

                                    if ($pto > 0)
                                        $where_str .= " and (IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= " . (double)$pto . ")";
                                } else {
                                    if ($pfrom > 0)
                                        $where_str .= " and (IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) >= " . (double)$pfrom . ")";
                                    if ($pto > 0)
                                        $where_str .= " and (IF(tmp_pp.status, tmp_pp.specials_new_products_price, tmp_pp.products_price) <= " . (double)$pto . ")";

                                }

                            } else {
                            // $status_tmp_product_prices_table is not true: uses p.products_price instead of cg_products_price
                            // because in the where clause for the case $status_tmp_special_prices is true, the table
                            // specials_retail_prices is abbreviated with "s" also we can use the same code for "true" and for "false"
                                if (DISPLAY_PRICE_WITH_TAX == 'true') {
                                    if ($pfrom > 0)
                                        $where_str .= " and (IF(s.status AND s.customers_group_id = '" . $customer_group_id . "', s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) >= " . (double)$pfrom . ")";
                                    if ($pto > 0)
                                        $where_str .= " and (IF(s.status AND s.customers_group_id = '" . $customer_group_id . "', s.specials_new_products_price, p.products_price) * if(gz.geo_zone_id is null, 1, 1 + (tr.tax_rate / 100) ) <= " . (double)$pto . ")";
                                } else {
                                    if ($pfrom > 0)
                                        $where_str .= " and (IF(s.status AND s.customers_group_id = '" . $customer_group_id . "', s.specials_new_products_price, p.products_price) >= " . (double)$pfrom . ")";
                                    if ($pto > 0)
                                        $where_str .= " and (IF(s.status AND s.customers_group_id = '" . $customer_group_id . "', s.specials_new_products_price, p.products_price) <= " . (double)$pto . ")";
                                }
                            }
                            // EOF Separate Pricing Per Customer

                            $where_str .= (STOCK_HIDE_OUT_OF_STOCK_PRODUCTS == 'true' ? " and p.products_quantity>='" . (int)STOCK_MINIMUM_VALUE . "' " : '');
                            $where_str .= " and p.is_store_item='0' ";
                            $where_str .= " and (m.manufacturers_status is null or m.manufacturers_status='1') ";

                            if ((DISPLAY_PRICE_WITH_TAX == 'true') && (tep_not_null($pfrom) || tep_not_null($pto))){
                                $where_str .= " group by p.products_id, tr.tax_priority";
                            }

                            if ((!isset($HTTP_GET_VARS['sort'])) || (!preg_match('/[1-8][ad]/', $HTTP_GET_VARS['sort'])) || (substr($HTTP_GET_VARS['sort'], 0, 1) > sizeof($column_list))) {
                                for ($i = 0, $n = sizeof($column_list); $i < $n; $i++) {
                                    if ($column_list[$i] == 'PRODUCT_LIST_NAME') {
                                        $HTTP_GET_VARS['sort'] = $i + 1 . 'a';
                                        $order_str = ' order by pd.products_name';
                                        break;
                                    }
                                }
                            } else {
                                $sort_col = substr($HTTP_GET_VARS['sort'], 0, 1);
                                $sort_order = substr($HTTP_GET_VARS['sort'], 1);
                                $order_str = ' order by ';
                                switch ($column_list[$sort_col - 1]){
                                    case 'PRODUCT_LIST_MODEL':
                                        $order_str .= "p.products_model " . ($sort_order == 'd' ? "desc" : "") . ", pd.products_name";
                                        break;
                                    case 'PRODUCT_LIST_NAME':
                                        $order_str .= "pd.products_name " . ($sort_order == 'd' ? "desc" : "");
                                        break;
                                    case 'PRODUCT_LIST_MANUFACTURER':
                                        $order_str .= "m.manufacturers_name " . ($sort_order == 'd' ? "desc" : "") . ", pd.products_name";
                                        break;
                                    case 'PRODUCT_LIST_QUANTITY':
                                        $order_str .= "p.products_quantity " . ($sort_order == 'd' ? "desc" : "") . ", pd.products_name";
                                        break;
                                    case 'PRODUCT_LIST_IMAGE':
                                        $order_str .= "pd.products_name";
                                        break;
                                    case 'PRODUCT_LIST_WEIGHT':
                                        $order_str .= "p.products_weight " . ($sort_order == 'd' ? "desc" : "") . ", pd.products_name";
                                        break;
                                    case 'PRODUCT_LIST_PRICE':
                                        $order_str .= "final_price " . ($sort_order == 'd' ? "desc" : "") . ", pd.products_name";
                                        break;
                                }
                            }

                            $options = array();

                            if (!empty($_SESSION['filter_s'])){
                                $option_value_pair = '';
                                foreach($_SESSION['filter_s'] as $val){
                                    $temp = explode('|', $val);
                                    $option_value_pair .= " ( pa.options_id='" . (int)$temp[0] . "' and pa.options_values_id='" . (int)$temp[1] . "' ) or ";
                                }

                                if (!empty($option_value_pair)){
                                    if (strrpos($_SESSION['filter_c'], '_')!==false){
                                        $cur_cat_id = substr($_SESSION['filter_c'], strrpos($_SESSION['filter_c'], '_')+1 );
                                    } else {
                                        $cur_cat_id = $_SESSION['filter_c'];
                                    }
                                    $filter_query = tep_db_query("select distinct if( isnull(p.parent_products_model) or p.parent_products_model='', p.products_id, (select p2.products_id from products p2 where p2.products_model=p.parent_products_model) ) as id from products p inner join products_to_categories p2c on p.products_id=p2c.products_id inner join products_attributes pa on p.products_id=pa.products_id where p2c.categories_id='" . (int)$cur_cat_id . "' and p.products_status='1' and (" . substr($option_value_pair, 0, -4) . ")");

                                    $ids = array();
                                    if (tep_db_num_rows($filter_query)){
                                        while($entry = tep_db_fetch_array($filter_query)){
                                            $ids[] = $entry['id'];
                                        }
                                    }
                                    $option_value_pair = " and p.products_id=pa.products_id and (" . substr($option_value_pair, 0, -4) . ") ";
                                }
                                //$where_str .= $option_value_pair;
                            } else {
                                $where_str .= " and p.parent_products_model is null ";
                            }
                            /*
                            $items_filter_query = tep_db_query("select distinct if( isnull(p.parent_products_model) or p.parent_products_model='', p.products_id, (select p2.products_id from products p2 where p2.products_model=p.parent_products_model) ) as id " . $from_str . $where_str);
                            $ids = array();
                            if (tep_db_num_rows($items_filter_query)){
                                while($entry = tep_db_fetch_array($items_filter_query)){
                                    $ids[] = $entry['id'];
                                }
                            }*/

                            $listing_sql = $select_str . $from_str . $where_str . (!empty($ids) ? " and p.products_id in (" . implode(',', $ids) . ") " : "") .  $order_str;
                            //$listing_sql = $select_str . $from_str . $where_str . $order_str;

                           // echo $listing_sql;
                            require (DIR_WS_MODULES . FILENAME_PRODUCT_LISTING);
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                        <td class="main">
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading" width="50%" align="left">
                                    <?php
                                    echo '<a href="' . tep_href_link(FILENAME_ADVANCED_SEARCH, tep_get_all_get_params(array('sort', 'page')), 'NONSSL', true, false) . '">' . tep_image_button('button_back.gif', IMAGE_BUTTON_BACK) . '</a>'; ?>
                                    </td>
                                    <td class="pageHeading" width="50%" align="right"></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
            <!-- body_text_eof //-->
            <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                <!-- right_navigation //-->
                <?php require (DIR_WS_INCLUDES . 'column_right.php'); ?>
                <!-- right_navigation_eof //-->
                </table>
            </td>
        </tr>
    </table>
    <!-- body_eof //-->
    <!-- footer //-->
    <?php require (DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <br>
</body>
</html>
<?php require (DIR_WS_INCLUDES . 'application_bottom.php'); ?>