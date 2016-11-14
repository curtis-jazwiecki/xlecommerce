<?php
/*
$Id: sts_user_code.php,v 4.1 2005/02/05 05:57:21 rigadin Exp $
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
  Based on: Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com
STS v4.1 by Rigadin (rigadin@osc-help.net)
*/
/* The following code is a sample of how to add new boxes easily.
  Use as many blocks as you need and just change the block names.
   $sts->start_capture();
   require(DIR_WS_BOXES . 'new_thing_box.php');
   $sts->stop_capture('newthingbox', 'box');  // 'box' makes the system remove some html code before and after the box. Otherwise big mess!
 Note: If $sts->stop_capture('newthingbox', 'box') is followed by $sts->start_capture, you can replace both by $sts->restart_capture('newthingbox', 'box')
 Another way to declare STS variables is to enter them directly into the STS array:
   $sts->template['MyText']='Hello World';
*/
$sts->template['urlspecials'] = tep_href_link(FILENAME_SPECIALS, '', 'SSL');
$sts->template['urlcontactus'] = tep_href_link(FILENAME_CONTACT_US, '', 'SSL');
$sts->template['urlreviews'] = tep_href_link(FILENAME_REVIEWS, '', 'SSL');
$sts->template['basehref'] = (($request_type == 'SSL') ? HTTP_SERVER : HTTPS_SERVER) . (($request_type == 'SSL') ? DIR_WS_HTTPS_CATALOG : DIR_WS_HTTP_CATALOG);
$write_cache = false;
$temp_file_name = 'catmenu-' . $language . '.cache';
if (USE_CACHE=='true'){
    $data = '';
    if (!read_cache($data, $temp_file_name, PURGE_CACHE_DAYS_LIMIT)){
        $write_cache = true;
    } else {
        $sts->template['catmenu'] = $data;
    }
}
if($write_cache){
    $sts->start_capture();
    echo "\n<!-- Start Category Menu -->\n";
    echo tep_draw_form('goto', FILENAME_DEFAULT, 'get', '');
    echo tep_draw_pull_down_menu('cPath', tep_get_category_tree(), $current_category_id, 'onChange="this.form.submit();"');
    echo "</form>\n";
    echo "<!-- End Category Menu -->\n";
    $sts->stop_capture('catmenu');
    if (USE_CACHE=='true'){
        write_cache($sts->template['catmenu'], $temp_file_name);
    }
}
$sts->start_capture();
include(DIR_WS_INCLUDES . 'boxes/featured.php');
$sts->stop_capture('featuredbox');
$sts->start_capture();
include(DIR_WS_MODULES . FILENAME_FEATURED);
$sts->stop_capture('featuredproducts');
$sts->start_capture();
include(DIR_WS_MODULES . 'featuredproduct1.php');
$sts->stop_capture('featuredgroup1');
$sts->start_capture();
include(DIR_WS_MODULES . 'featuredproduct2.php');
$sts->stop_capture('featuredgroup2');
$sts->start_capture();
include(DIR_WS_MODULES . 'featuredproduct3.php');
$sts->stop_capture('featuredgroup3');
  $sts->start_capture();
  include(DIR_WS_MODULES . 'featured_mobile.php');
  $sts->stop_capture('featuredmobileproducts');
$sts->start_capture();
if ($request_type == 'SSL') {
?>	
<script src="https://ssl.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
	_uacct="<?php echo GOOGLE_ANALYTICS_ACCOUNT; ?>";
	urchinTracker();
</script>
<?php
} else {
?>
<script src="http://www.google-analytics.com/urchin.js" type="text/javascript"></script>
<script type="text/javascript">
	_uacct="<?php echo GOOGLE_ANALYTICS_ACCOUNT; ?>";
	urchinTracker();
</script>
<?php
}
$sts->stop_capture('google_analytics');	
if ($PHP_SELF=='/' . FILENAME_ADVANCED_SEARCH){
	$sts->template["searchstringprefix"] = '';
	$sts->template["searchstring"] = '';
} else{
	$sts->template["searchstringprefix"] = '<span class="style3" style="padding-bottom:3px; ">Search:</span>';
	$sts->template["searchstring"] = tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get'). tep_draw_input_field('keywords', '', 'size="10" maxlength="30" style="width: ' . (BOX_WIDTH-30) . 'px"') . '&nbsp;' . tep_hide_session_id() . tep_image_submit('button_quick_find.gif', BOX_HEADING_SEARCH) . '</form>';
        $sts->template['startsearchform'] = tep_draw_form('quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get');
        $sts->template['endsearchform'] = '</form>';
}
function tep_get_category_tree($parent_id = '0', $spacing = '', $exclude = '', $category_tree_array = '', $include_itself = false) {
	global $languages_id;
	if (!is_array($category_tree_array)) $category_tree_array = array();
	if ( (sizeof($category_tree_array) < 1) && ($exclude != '0') ) $category_tree_array[] = array('id' => '0', 'text' => "Catalog");
	if ($include_itself) {
		$category_query = tep_db_query("select cd.categories_name from " . TABLE_CATEGORIES_DESCRIPTION . " cd where cd.language_id = '" . (int)$languages_id . "' and cd.categories_id = '" . (int)$parent_id . "'");
		$category = tep_db_fetch_array($category_query);
		$category_tree_array[] = array('id' => $parent_id, 'text' => $category['categories_name']);
	}
	$categories_query = tep_db_query("select c.categories_id, cd.categories_name, c.parent_id from " . TABLE_CATEGORIES . " c, " . TABLE_CATEGORIES_DESCRIPTION . " cd where c.categories_id = cd.categories_id and cd.language_id = '" . (int)$languages_id . "' and c.parent_id = '" . (int)$parent_id . "' order by c.sort_order, cd.categories_name");
	while ($categories = tep_db_fetch_array($categories_query)) {
		if ($exclude != $categories['categories_id']) $category_tree_array[] = array('id' => $categories['categories_id'], 'text' => $spacing . $categories['categories_name']);
		$category_tree_array = tep_get_category_tree($categories['categories_id'], $spacing . '&nbsp;&nbsp;&nbsp;', $exclude, $category_tree_array);
	}
	return $category_tree_array;
	
	
	}
        
$current_template = basename(STS_TEMPLATE_DIR);
if ( file_exists( DIR_FS_CATALOG . DIR_WS_MODULES . 'sts_inc/sts_' . $current_template . '.php' ) ){
	include_once(DIR_FS_CATALOG . DIR_WS_MODULES . 'sts_inc/sts_' . $current_template . '.php');
}
$categories_html = '';
$left_column_display = '';
$content_width_css_val = '';
$customer_reviews = '';
$categories_listing_footer_01 = '';
$categories_listing_footer_02 = '';
$login_form = '';
$create_account = '';
$newsletter_signup = '';
$startnewslettersignupform = '';
$endnewslettersignupform = '';
$pwa_link = '';
$pg_head_create_acc = '';
$form_head_create_acc = '';
$btn_text_create_account = '';
$customer_reviews_html = '';
$template18_stylesheet1 = '';
$categories_list_chunk1 = '';
$categories_list_chunk2 = '';
$categories_list_chunk3 = '';
$customer_orders_count = '0';
$customer_wishlist_items_count = '0';
$cart_items_count = (!empty($cart) && is_object($cart) && get_class($cart)=='shoppingCart') ? $cart->count_contents() : '0';
$cart_total_amount = $cart->show_total();
$sts->template['script'] = strtolower( basename( $PHP_SELF, '.php' ) );
if ($sts->template['script']=='create_account'){
    if (isset($_GET['guest']) && $_GET['guest']=='guest'){
        $sts->template['script'] .= ' guest';
    }
}
if (!empty($current_template)){
    $template_id = str_ireplace('template', '', $current_template);
    if (!empty($template_id) && is_numeric($template_id)){
        $query = tep_db_query("select distinct banners_group, template_variable from banners where template='" . (int)$template_id . "'");
        if (tep_db_num_rows($query)){
            while($group = tep_db_fetch_array($query)){
                $variable = substr($group['template_variable'], 1);
                
                /*if ( strtolower( $group['banners_group'] ) != 'slider' ){ // 'slider' banner group intentionally kept out of cache
                    $write_cache = false;
                    $data = '';
                    if (USE_CACHE=='true'){
                        $temp_file_name = 't' . $template_id . '_' . $variable . '-' . $language . '.cache';
                        if (!read_cache($data, $temp_file_name, PURGE_CACHE_DAYS_LIMIT)){
                            $write_cache = true;
                        } else {
                            $sts->template[$variable] = $data;
                        }
                    }
                    if ($write_cache){*/
                        switch ( strtolower( $group['banners_group'] ) ){
                            case 'slider':
                                $sts->template[$variable] = generateSlider($template_id, $group['banners_group']);
                                break;
                            default:
                                $sts->template[$variable] = tep_display_banner('dynamic', $group['banners_group']);
                        }
                        /*if (USE_CACHE=='true'){
                            write_cache($sts->template[$variable], $temp_file_name);
                        }
                    }
                } else {
                    $sts->template[$variable] = generateSlider($template_id, $group['banners_group']);
                /*}*/
            }
        }
		
		function tep_information_show($information_group_id) {
									
					global $languages_id;
					$child_information = array();
					$information_tree = array();
					$informationString = array();
					$parent_child_selected = '';
					
				
					$information_query = tep_db_query("SELECT information_id, information_title, parent_id FROM " . TABLE_INFORMATION . " WHERE visible='1' and is_hidden='0' and information_title<>'' and language_id='" . (int)$languages_id ."' and information_group_id = '" . (int)$information_group_id . "' ORDER BY sort_order");
					
					while($information = tep_db_fetch_array($information_query)) {
						$information_tree[$information['information_id']] = array(
							'info_title' 	=> $information['information_title'],
							'parent_id' 	=> $information['parent_id'],
							'info_next_id' 	=> 0
						);
						if ($information_tree[$information['information_id']]['parent_id'] != '0') {
							$child_information[] = array (
								'parent_info_id' => $information['parent_id'],
								'child_info_id'  => $information['information_id']
							);
						}
					}
					$count_child = count($child_information);
				
					// Test if a child has been requested and set $parent_child_selected
					for ( $i = 0; $i < ($count_child); $i++ ) {
						if ((isset($_GET['info_id'])) && ($child_information[$i]['child_info_id'] == $_GET['info_id'])) {
							$parent_child_selected = $child_information[$i]['parent_info_id'];
						}
					}
				
					// Run through the $information_tree to find all pages
					while ( $element = each ( $information_tree ) )  {
						if (!isset($information_tree[$element['key']]['parent_id']) || ($information_tree[$element['key']]['parent_id'] == 0)) {
				
							//Set the main title to bold if it was selected or one of its children were selected
							if (((isset($_GET['info_id'])) && ($_GET['info_id'] == $element['key'])) || ($parent_child_selected == $element['key'])) {
								$informationString[$element['key']] = array(
									"link"	=>	tep_href_link(FILENAME_INFORMATION, 'info_id=' . $element['key']),
									"title"	=>	$information_tree[$element['key']]['info_title']
								);
								
								
							} else {
								
								$informationString[$element['key']] = array(
									"link"	=>	tep_href_link(FILENAME_INFORMATION, 'info_id=' . $element['key']),
									"title"	=>	$information_tree[$element['key']]['info_title']
								);
							}
				
							
							//Show children if they exist
							if (((isset($_GET['info_id'])) && ($_GET['info_id'] == $element['key'])) || ($parent_child_selected == $element['key'])) {
								for ( $i = 0; $i < ($count_child); $i++ ) {
									
									if ($child_information[$i]['parent_info_id'] == $element['key'])
				
									//Show a child as bold if it was selected
									if ((isset($_GET['info_id'])) && ($_GET['info_id'] == $child_information[$i]['child_info_id'])) {
										$informationString[$element['key']]['child'] = array(
											"link"	=>	tep_href_link(FILENAME_INFORMATION, 'info_id=' . $child_information[$i]['child_info_id']),
											"title"	=>	$information_tree[$child_information[$i]['child_info_id']]['info_title']
										);
										
									} else {
										
										$informationString[$element['key']]['child'] = array(
											"link"	=>	tep_href_link(FILENAME_INFORMATION, 'info_id=' . $child_information[$i]['child_info_id']),
											"title"	=>	$information_tree[$child_information[$i]['child_info_id']]['info_title'] 
										);
										
									}
								}
							}
						}
					}
					
					return $informationString;
					
				}
				
				
        $script = strtolower( basename( $PHP_SELF ) );
        switch($template_id){
            case '1':
             $show_search_filter = show_search_filter($script);
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
                } 
                $categories_html = getCategoriesHtml();
				break;
			case '2':
			case '13':
				$show_search_filter = show_search_filter($script);
                if ( ($script != 'shop.php' && $script != 'advanced_search_result.php') || (!$show_search_filter)){
				    $sts->template['search_filterbox'] = '';
					$sts->template['categorybox'] = '';
				} 
                $categories_html = '<ul class="nav">' . getCategoriesHtmlTpl18() . '</ul>';
				$str_search_javascript = '';
				
				if(basename($_SERVER['PHP_SELF']) == 'advanced_search.php'){
				
					$str_search_javascript = "function popupWindow(url) {window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')}";
				
				}
               
			    $template2_stylesheet1 = 
					'<script type="text/javascript"> 
					'.$str_search_javascript.'
					function removeWishlistProducts(products_id){
						jQuery.ajax({
							url: "compare.php", 
							method: "post", 
							data: "mode=removewishlist&products_id="+products_id, 
							success: function(response){
								if(response != ""){
									alert("Success: Product removed from compare list!");
									location.reload();
								}
							}
						});
					} </script>'. "\n" .
                    '<script type="text/javascript" src="star_rating.js"></script>'. "\n" .'
					<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj_tabcontent.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-tab.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap-responsive.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-general.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-mobile.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-ie.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-layout.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/flexslider.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.css" />' . "\n" . 
                    '<link href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/jquery.cookiebar.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-cyan.css" />' . "\n" . 
                    (((basename($PHP_SELF) == FILENAME_DEFAULT && $cPath == '') && !isset($_GET['manufacturers_id'])) 
                    ? 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/homepage.css" />' . "\n" 
                    : 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/nohomepage.css" />' . "\n" 
                    ) .
                    '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/bootstrap.min.js"></script>' . "\n" . 
                    
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.flexslider.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.cookiebar.js"></script>' . "\n" . 
                    '<script type="text/plain" class="cc-onconsent-inline-advertising" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/colorbox/colorbox.css" />' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/photoset-grid/jquery.photoset-grid.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/colorbox/jquery.colorbox-min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.carouFredSel-6.0.4-packed.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/osmart.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/tabcontent.js"></script>' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Dosis:200,400,700" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n";
                if (tep_session_is_registered('customer_id')) {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . ' id="tdb2">Log Off</a>';
                } else {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . ' id="tdb2">Log In</a>';
                }
                break;
            
            case '12':
                
                if ($script!='index.php' && $script!='advanced_search_result.php' && $script!='shop.php'){
                    $left_column_display = 'none';
                    $content_width_css_val = '100%';
                } else {
                    $content_width_css_val = 'auto';
                }
                
                $show_search_filter = show_search_filter($script);
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
                } else {
                    $sts->template['categorybox'] = '';
                }
                
                $categories_html = '';
                $temp_file_name = 't12_top_navigation_category_dropdown-' . $language . '.cache';
                if ( USE_CACHE=='true' ){
                    if (!read_cache($categories_html, $temp_file_name)){
                        $categories_html = getCategoriesHtml();
                        write_cache($categories_html, $temp_file_name);
                    }
                } else {
                    $categories_html = getCategoriesHtml();
                }
                
                $categories_listing_footer_01 = $categories_listing_footer_02 = '';
                $temp_file_name1 = 't12_categories_listing_footer_01-' . $language . '.cache';
                $temp_file_name2 = 't12_categories_listing_footer_02-' . $language . '.cache';
                if (USE_CACHE=='true'){
                    if( !read_cache($categories_listing_footer_01, $temp_file_name1) || !read_cache($categories_listing_footer_02, $temp_file_name2) ){
                        $splited_categories_list = getSplitedCategoriesList();
                        $categories_listing_footer_01 = implode('', $splited_categories_list[0]);
                        $categories_listing_footer_02 = implode('', $splited_categories_list[1]);
                        write_cache($categories_listing_footer_01, $temp_file_name1);
                        write_cache($categories_listing_footer_02, $temp_file_name2);
                    }
                } else {
                    $splited_categories_list = getSplitedCategoriesList();
                    $categories_listing_footer_01 = implode('', $splited_categories_list[0]);
                    $categories_listing_footer_02 = implode('', $splited_categories_list[1]);
                }
                
                
                if ($script=='index.php'){
                    $reviews = getCustomerReviews(TEMPLAT12_REVIEWS_MAX_COUNT);
                    $customer_reviews_html = getReviewsHtml($template_id, $reviews);
                }
                
                $sts->template["searchstring"] = '<form name="quick_find" action="' . tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false) . '" method="get"><input type="text" name="keywords" maxlength="30" class="search" /><input type="submit" value="" class="searc_sub" />'.tep_draw_hidden_field("search_in_description",1).'</form>';
                
                $login_form = tep_draw_form('login', tep_href_link(FILENAME_LOGIN, 'action=process', 'SSL')) . 
                                '<div class="divpadd">' . 
                                    '<p>' . ENTRY_EMAIL_ADDRESS . '</p>' . 
                                    '<p><input name="email_address"></p>' . 
                                    '<p>' . ENTRY_PASSWORD . '</p>' . 
                                    '<p><input type="password" name="password"></p>' . 
                                    '<p><input type="submit" value="LOGIN" class="buttonbg">' . 
                                    '<a href="' . tep_href_link(FILENAME_PASSWORD_FORGOTTEN, '', 'SSL') . '">' . TEXT_PASSWORD_FORGOTTEN . '</a></p>' . 
                                '</div>' . 
                            '</form>';
                
                if (defined('PURCHASE_WITHOUT_ACCOUNT') && ($cart->count_contents() > 0) && (PURCHASE_WITHOUT_ACCOUNT == 'ja' || PURCHASE_WITHOUT_ACCOUNT == 'yes')) {
                    $pwa_link = TEXT_GUEST_INTRODUCTION . '<br><a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, 'guest=guest', 'SSL') . '"><input type="button" value="CHECKOUT" class="buttonbg"></a><br><br>or<br>';
                } else {
                    $pwa_link = '';
                }
                $create_account = '<a href="' . tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL') . '"><input type="button" value="CREATE AN ACCOUNT" class="buttonbg"></a>';
                if (isset($_GET['guest']) && $_GET['guest']=='guest'){
                    $btn_text_create_account = 'CONTINUE';
                    $pg_head_create_acc = '';
                    $form_head_create_acc = 'YOUR PERSONAL DETAILS';
                } else {
                    $btn_text_create_account = 'REGISTER NOW';
                    $pg_head_create_acc = 'Register for an account';
                    $form_head_create_acc = 'CREATE YOUR ACCOUNT';
                }
                
                $newsletter_signup = '<form method="post" action="?action=newsletter_signup">
                                        <input type="text" class="footer_textbox" placeholder="First Name" name="newsletter_name">
                                        <input type="text" class="footer_textbox" placeholder="Email Address" name="newsletter_email">
                                        <input type="submit" class="footer_sub" value="Sign Up!">
</form>';
                if (tep_session_is_registered('customer_id')) {
                        $sts->template['myaccountlogoff'] = $sts->template['myaccount'] . '<span class="skuborder"></span>' . $sts->template['logoff'];
                } else {
                        $sts->template['myaccountlogoff'] = $sts->template['myaccount'];
                }
                
                break;
			case '21':
			case '9':
            	$show_search_filter = show_search_filter($script);
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
					$sts->template['searchmobile_filterbox']='';
                } else {
                    $sts->template['categorybox'] = '';
                }
                $categories_html = '<ul class="nav">' . getCategoriesHtmlTpl21() . '</ul>';
                
				$str_search_javascript = '';
				if(basename($_SERVER['PHP_SELF']) == 'advanced_search.php'){
					$str_search_javascript = "function popupWindow(url) {
window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')}";
				}
			    $template18_stylesheet1 = 
					'<script type="text/javascript"> 
					'.$str_search_javascript.'
					function removeWishlistProducts(products_id){
						jQuery.ajax({
							url: "compare.php", 
							method: "post", 
							data: "mode=removewishlist&products_id="+products_id, 
							success: function(response){
								if(response != ""){
									alert("Success: Product removed from compare list!");
									location.reload();
								}
							}
						});
					} </script>'. "\n" .
                    '<script type="text/javascript" src="star_rating.js"></script>'. "\n" .'
					<link  href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj_tabcontent.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/template.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-tab.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap-responsive.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-general.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-mobile.css" />' . "\n" .  '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-ie.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-layout.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/flexslider.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.css" />' . "\n" . '<link href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/jquery.cookiebar.css" />' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-cyan.css" />' . "\n" . (((basename($PHP_SELF) == FILENAME_DEFAULT && $cPath == '') && !isset($_GET['manufacturers_id'])) ? '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/homepage.css" />' . "\n" : '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/nohomepage.css" />' . "\n" ) . '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/bootstrap.min.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.flexslider.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.cookiebar.js"></script>' . "\n" . '<script type="text/plain" class="cc-onconsent-inline-advertising" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n" . '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/colorbox/colorbox.css" />' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/photoset-grid/jquery.photoset-grid.min.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/colorbox/jquery.colorbox-min.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.carouFredSel-6.0.4-packed.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/osmart.js"></script>' . "\n" . '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/tabcontent.js"></script>' . "\n" . '<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />' . "\n" . '<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css" />' . "\n" . '<link href="https://fonts.googleapis.com/css?family=Dosis:200,400,700" rel="stylesheet" type="text/css">' . "\n" . '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n";
                if (tep_session_is_registered('customer_id')) {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . ' id="tdb2" class="mnu_side_lnk">Log Off</a>';
                } else {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . ' id="tdb2" class="mnu_side_lnk">Log In</a>';
                }
				
				// added on 08-07-2016 to get all information pages #start
				$information_pages_array = tep_information_show(1);
				$information_pages_html = '';
				if(count($information_pages_array) > 0){
					
					foreach($information_pages_array as $information_id => $information){
						
						$information_pages_html .= '<li><a href="'.$information['link'].'">'.$information['title'].'</a>';
						
						
						if(count($information['child']) > 0){
							
							$information_pages_html .= '<ul>';
							
							foreach($information['child'] as $child_information_id => $child_information){
							
								$information_pages_html .= '<li><a href="'.$child_information['link'].'">'.$child_information['title'].'</a><li>';
							
							}
							
							$information_pages_html .= '</ul>';
							
							
							
						}
						
						$information_pages_html .= '</li>';
					
					}
					
					
					
				}
				
				
				
				
				$sts->template['information_pages'] = $information_pages_html;
				
				
				
				
				// added on 08-07-2016  #ends 
				
				
				
                break;
            case '18':
			case '4':
			case '7':
			case '11':
			case '12':
			case '14':
			
             $show_search_filter = show_search_filter($script);
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
                } else {
                    $sts->template['categorybox'] = '';
                }
                $categories_html = '<ul class="nav">' . getCategoriesHtmlTpl18() . '</ul>';
                //$sts->template['stylesheet'] = '<link rel="stylesheet" type="text/css" href="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_INCLUDES . 'sts_templates/'.$check['configuration_value'].'/stylesheet.css" />' . "\n";
$str_search_javascript = '';
if(basename($_SERVER['PHP_SELF']) == 'advanced_search.php'){
	$str_search_javascript = "function popupWindow(url) {
 window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}";
}
               
			   
			    $template18_stylesheet1 = 
					'<script type="text/javascript"> 
					'.$str_search_javascript.'
					function removeWishlistProducts(products_id){
						jQuery.ajax({
							url: "compare.php", 
							method: "post", 
							data: "mode=removewishlist&products_id="+products_id, 
							success: function(response){
								if(response != ""){
									alert("Success: Product removed from compare list!");
									location.reload();
								}
							}
						});
					} </script>'. "\n" .
                    '<script type="text/javascript" src="star_rating.js"></script>'. "\n" .'
					<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj_tabcontent.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-tab.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap-responsive.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-general.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-mobile.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-ie.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-layout.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/flexslider.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.css" />' . "\n" . 
                    '<link href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/jquery.cookiebar.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-cyan.css" />' . "\n" . 
                    (((basename($PHP_SELF) == FILENAME_DEFAULT && $cPath == '') && !isset($_GET['manufacturers_id'])) 
                    ? 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/homepage.css" />' . "\n" 
                    : 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/nohomepage.css" />' . "\n" 
                    ) .
                    '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/bootstrap.min.js"></script>' . "\n" . 
                    
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.flexslider.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.cookiebar.js"></script>' . "\n" . 
                    '<script type="text/plain" class="cc-onconsent-inline-advertising" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/colorbox/colorbox.css" />' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/photoset-grid/jquery.photoset-grid.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/colorbox/jquery.colorbox-min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.carouFredSel-6.0.4-packed.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/osmart.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/tabcontent.js"></script>' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Dosis:200,400,700" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n";
                if (tep_session_is_registered('customer_id')) {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . ' id="tdb2">Log Off</a>';
                } else {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . ' id="tdb2">Log In</a>';
                }
                break;
				case '3':
             $show_search_filter = show_search_filter($script);
				if ($script=='contact_us.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='information.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='specials.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='wishlist.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='checkout_shipping.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='account.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='ship_estimator.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='account_history.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_edit.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_newsletters.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_notifications.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_password.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='checkout_success.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='logoff.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='checkout_success.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='wishlist_help.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='address_book_process.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
                } else {
                    $sts->template['categorybox'] = '';
                }
                $categories_html = '<ul class="nav">' . getCategoriesHtmlTpl18() . '</ul>';
                //$sts->template['stylesheet'] = '<link rel="stylesheet" type="text/css" href="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_INCLUDES . 'sts_templates/'.$check['configuration_value'].'/stylesheet.css" />' . "\n";
$str_search_javascript = '';
if(basename($_SERVER['PHP_SELF']) == 'advanced_search.php'){
	$str_search_javascript = "function popupWindow(url) {
 window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}";
}
               
			   
			    $template18_stylesheet1 = 
					'<script type="text/javascript"> 
					'.$str_search_javascript.'
					function removeWishlistProducts(products_id){
						jQuery.ajax({
							url: "compare.php", 
							method: "post", 
							data: "mode=removewishlist&products_id="+products_id, 
							success: function(response){
								if(response != ""){
									alert("Success: Product removed from compare list!");
									location.reload();
								}
							}
						});
					} </script>'. "\n" .
                    '<script type="text/javascript" src="star_rating.js"></script>'. "\n" .'
					<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj_tabcontent.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-tab.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap-responsive.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-general.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-mobile.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-ie.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-layout.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/flexslider.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.css" />' . "\n" . 
                    '<link href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/jquery.cookiebar.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-cyan.css" />' . "\n" . 
                    (((basename($PHP_SELF) == FILENAME_DEFAULT && $cPath == '') && !isset($_GET['manufacturers_id'])) 
                    ? 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/homepage.css" />' . "\n" 
                    : 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/nohomepage.css" />' . "\n" 
                    ) .
                    '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/bootstrap.min.js"></script>' . "\n" . 
                    
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.flexslider.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.cookiebar.js"></script>' . "\n" . 
                    '<script type="text/plain" class="cc-onconsent-inline-advertising" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/colorbox/colorbox.css" />' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/photoset-grid/jquery.photoset-grid.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/colorbox/jquery.colorbox-min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.carouFredSel-6.0.4-packed.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/osmart.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/tabcontent.js"></script>' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Dosis:200,400,700" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n";
                if (tep_session_is_registered('customer_id')) {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . ' id="tdb2">Log Off</a>';
                } else {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . ' id="tdb2">Log In</a>';
                }
				
				
				// added on 29-09-2016 #start
				$header_currencies_array = getCurrencies('ChooseCurrencyBox');
				
				$header_currency_string = $header_currencies_array['form'].'<input type="hidden" name="currency" value="" id="set_hidden_currency">';
				
				foreach($header_currencies_array['currency'] as $header_currencies){
				
					$header_currency_string .= '<div class=""><a onClick="setCurrency(this);" href="javascript:void(0);" id="'.$header_currencies['id'].'"><span class="Text">'.$header_currencies['text'].'</span></a></div>';	
					
				}
				
				$header_currency_string .= $header_currencies_array['hidden'].'</form>';
				
				
				$header_currency_string .= '<script type="text/javascript">
											function setCurrency(ele){
												jQuery("#set_hidden_currency").val(jQuery(ele).attr("id"));
												document.forms["currencies"].submit();
											}
											</script>';
				
				$sts->template['urlcurrencies'] = $header_currency_string;
				$sts->template['default_selected_currency'] = $currency;
				
				$information_pages_array = tep_information_show(1);
				$information_pages_html = '';
				if(count($information_pages_array) > 0){
					
					foreach($information_pages_array as $information_id => $information){
						
						$information_pages_html .= '<li><a href="'.$information['link'].'">'.$information['title'].'</a>';
						
						
						if(count($information['child']) > 0){
							
							$information_pages_html .= '<ul>';
							
							foreach($information['child'] as $child_information_id => $child_information){
							
								$information_pages_html .= '<li><a href="'.$child_information['link'].'">'.$child_information['title'].'</a><li>';
							
							}
							
							$information_pages_html .= '</ul>';
							
							
							
						}
						
						$information_pages_html .= '</li>';
					
					}
					
					
					
				}
				
				
				
				
				$sts->template['information_pages'] = $information_pages_html;
				// added on 29-09-2016 #ends
				//added on 17-10-2016
				case '17':
             $show_search_filter = show_search_filter($script);
				if ($script=='contact_us.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='information.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='specials.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='wishlist.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='checkout_shipping.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='account.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='ship_estimator.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='account_history.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_edit.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_newsletters.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_notifications.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='account_password.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='checkout_success.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='logoff.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='checkout_success.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				if ($script=='wishlist_help.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
				if ($script=='address_book_process.php'){
					$sts->template['free_shipping_banner'] = '';
					
				}
				
                if ($script=='index.php' || !$show_search_filter){
                    $sts->template['search_filterbox'] = '';
                } else {
                    $sts->template['categorybox'] = '';
                }
                $categories_html = '<ul class="nav">' . getCategoriesHtmlTpl18() . '</ul>';
                //$sts->template['stylesheet'] = '<link rel="stylesheet" type="text/css" href="' . HTTP_SERVER . DIR_WS_HTTP_CATALOG . DIR_WS_INCLUDES . 'sts_templates/'.$check['configuration_value'].'/stylesheet.css" />' . "\n";
$str_search_javascript = '';
if(basename($_SERVER['PHP_SELF']) == 'advanced_search.php'){
	$str_search_javascript = "function popupWindow(url) {
 window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
}";
}
               
			   
			    $template18_stylesheet1 = 
					'<script type="text/javascript"> 
					'.$str_search_javascript.'
					function removeWishlistProducts(products_id){
						jQuery.ajax({
							url: "compare.php", 
							method: "post", 
							data: "mode=removewishlist&products_id="+products_id, 
							success: function(response){
								if(response != ""){
									alert("Success: Product removed from compare list!");
									location.reload();
								}
							}
						});
					} </script>'. "\n" .
                    '<script type="text/javascript" src="star_rating.js"></script>'. "\n" .'
					<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/jquery/ui/redmond/jquery-ui-1.10.4.min.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj_tabcontent.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/template.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-tab.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/bootstrap-responsive.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-general.css" />' . "\n" .
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-mobile.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-ie.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-layout.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/flexslider.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.css" />' . "\n" . 
                    '<link href="' . STS_TEMPLATE_DIR . 'ext/css/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/jquery.cookiebar.css" />' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/mj-cyan.css" />' . "\n" . 
                    (((basename($PHP_SELF) == FILENAME_DEFAULT && $cPath == '') && !isset($_GET['manufacturers_id'])) 
                    ? 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/homepage.css" />' . "\n" 
                    : 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/css/nohomepage.css" />' . "\n" 
                    ) .
                    '<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/bootstrap.min.js"></script>' . "\n" . 
                    
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.flexslider.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.cookiebar.js"></script>' . "\n" . 
                    '<script type="text/plain" class="cc-onconsent-inline-advertising" src="https://pagead2.googlesyndication.com/pagead/show_ads.js"></script>' . "\n" . 
                    '<link rel="stylesheet" type="text/css" href="' . STS_TEMPLATE_DIR . 'ext/colorbox/colorbox.css" />' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/photoset-grid/jquery.photoset-grid.min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/colorbox/jquery.colorbox-min.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/jquery.carouFredSel-6.0.4-packed.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/osmart.js"></script>' . "\n" . 
                    '<script type="text/javascript" src="' . STS_TEMPLATE_DIR . 'ext/jquery/tabcontent.js"></script>' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Oswald" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=PT+Sans" rel="stylesheet" type="text/css" />' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Dosis:200,400,700" rel="stylesheet" type="text/css">' . "\n" . 
                    '<link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700,900,100italic,300italic,400italic,700italic,900italic" rel="stylesheet" type="text/css">' . "\n";
                if (tep_session_is_registered('customer_id')) {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGOFF, '', 'SSL') . ' id="tdb2">Log Off</a>';
                } else {
                    $sts->template['loginofflogo'] = '<a href=' . tep_href_link(FILENAME_LOGIN, '', 'SSL') . ' id="tdb2">Log In</a>';
                }
				
				
				// added on 29-09-2016 #start
				$header_currencies_array = getCurrencies('ChooseCurrencyBox');
				
				$header_currency_string = $header_currencies_array['form'].'<input type="hidden" name="currency" value="" id="set_hidden_currency">';
				
				foreach($header_currencies_array['currency'] as $header_currencies){
				
					$header_currency_string .= '<div class=""><a onClick="setCurrency(this);" href="javascript:void(0);" id="'.$header_currencies['id'].'"><span class="Text">'.$header_currencies['text'].'</span></a></div>';	
					
				}
				
				$header_currency_string .= $header_currencies_array['hidden'].'</form>';
				
				
				$header_currency_string .= '<script type="text/javascript">
											function setCurrency(ele){
												jQuery("#set_hidden_currency").val(jQuery(ele).attr("id"));
												document.forms["currencies"].submit();
											}
											</script>';
				
				$sts->template['urlcurrencies'] = $header_currency_string;
				$sts->template['default_selected_currency'] = $currency;
				
				$information_pages_array = tep_information_show(1);
				$information_pages_html = '';
				if(count($information_pages_array) > 0){
					
					foreach($information_pages_array as $information_id => $information){
						
						$information_pages_html .= '<li><a href="'.$information['link'].'">'.$information['title'].'</a>';
						
						
						if(count($information['child']) > 0){
							
							$information_pages_html .= '<ul>';
							
							foreach($information['child'] as $child_information_id => $child_information){
							
								$information_pages_html .= '<li><a href="'.$child_information['link'].'">'.$child_information['title'].'</a><li>';
							
							}
							
							$information_pages_html .= '</ul>';
							
							
							
						}
						
						$information_pages_html .= '</li>';
					
					}
					
					
					
				}
				
				
				
				
				$sts->template['information_pages'] = $information_pages_html;
				// added on 17-10-2016 #ends
                break;
            case '19':
                //BOF: header categories html
                $categories_html = '<div class="drop-box-subcat" style="display: none;">'.getCategoriesHtmlTpl19().'</div>';
                $write_cache = false;
                $sts->template['categories_html'] = '';
                $temp_file_name = 't19-catmenuhead.cache';
                /*if (USE_CACHE=='true'){
                    $data = '';
                    if (!read_cache($data, $temp_file_name, PURGE_CACHE_DAYS_LIMIT)){
                        $write_cache = true;
                    } else {
                        //echo 'hiii';
                        //exit();
                        $sts->template['categories_html'] = $data;
                    }
                }
                if($write_cache || USE_CACHE=='false'){
                     //echo 'hiii1';
                        //exit();
                    $sts->start_capture();
                    include(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/' . $current_template . '/dresscode_theme/dresscode_blocks/categories.php');
                    $sts->stop_capture('categories_html');
                    if (USE_CACHE=='true'){
                         //echo 'hiii2';
                        //exit();
                        write_cache($sts->template['categories_html'], $temp_file_name);
                    }
                }*/
                //EOF: header categories html
                //BOF: breadcrumbs
                    $sts->start_capture();
                    include(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/' . $current_template . '/dresscode_theme/dresscode_blocks/bread_crumbs.php');
                    $sts->stop_capture('breadcrumbs');
                //EOF: breadcrumbs
                //BOF: top_slider
                    $sts->start_capture();
                    include(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/' . $current_template . '/dresscode_theme/dresscode_blocks/top_slider.php');
                    $sts->stop_capture('top_slider');
                //EOF: top_slider
                    $reviews = getCustomerReviews(1);
                    $customer_reviews_html = getReviewsHtml($template_id, $reviews, true);
                    $startnewslettersignupform = '<form method="post" action="?action=newsletter_signup">';
                    $endnewslettersignupform = '</form>';
                break;
			case '20':
                //BOF: navigation categories html
                /*$write_cache = false;
                $temp_file_name = 't20-catmenuhead.cache';
                if (USE_CACHE=='true'){
                    $data = '';
                    if (!read_cache($data, $temp_file_name, PURGE_CACHE_DAYS_LIMIT)){
                        $write_cache = true;
                    } else {
                        $sts->template['categories_html'] = $data;
                    }
                }
                if($write_cache || USE_CACHE=='false'){
                    $sts->start_capture();
                    $data = getNavigationCategoriesListTpl20();
					echo $data;
                    $sts->stop_capture('categories_html');
                    if (USE_CACHE=='true'){
                        write_cache($sts->template['categories_html'], $temp_file_name);
                    }
                }*/
                $temp = getNavigationCategoriesListTpl20();
                $categories_list_chunk1 = implode(' ', $temp[0]);
                $categories_list_chunk2 = implode(' ', $temp[1]);
                $categories_list_chunk3 = implode(' ', $temp[2]);
                //EOF: navigation categories html
                break;
        //}
        }
    }
}
// set TEXT_MAIN #start
$text_main_page_query = tep_db_fetch_array(tep_db_query("select information_description from ".TABLE_INFORMATION." where information_group_id = '2'"));
$sts->template['TEXT_MAIN'] = $text_main_page_query['information_description'];
// set TEXT_MAIN #ends
$sts->template['login_url'] = tep_href_link(FILENAME_LOGIN, '', 'SSL');
$sts->template['wishlist_url'] = tep_href_link(FILENAME_WISHLIST, '', 'SSL');
$sts->template['ordershistory_url'] = tep_href_link(FILENAME_ACCOUNT_HISTORY, '', 'SSL');
$sts->template['language_name'] = $lng->language['name'];
$sts->template['currency'] = $currency;
$sts->template['left_column_display'] = $left_column_display;
if (empty($sts->template['categories_html'])) $sts->template['categories_html'] = $categories_html;
$sts->template['content_width_css_val'] = $content_width_css_val;
$sts->template['customer_reviews_html'] = $customer_reviews_html;
$sts->template['categories_listing_footer_01'] = $categories_listing_footer_01;
$sts->template['categories_listing_footer_02'] = $categories_listing_footer_02;
$sts->template['login_form'] = $login_form;
$sts->template['pwa_link'] = $pwa_link;
$sts->template['btn_text_create_account'] = $btn_text_create_account;
$sts->template['pg_head_create_acc'] = $pg_head_create_acc;
$sts->template['form_head_create_acc'] = $form_head_create_acc;
$sts->template['create_account'] = $create_account;
$sts->template['newsletter_signup'] = $newsletter_signup;
$sts->template['startnewslettersignupform'] = $startnewslettersignupform;
$sts->template['endnewslettersignupform'] = $endnewslettersignupform;
$sts->template['cart_items_count'] = $cart_items_count;
$sts->template['cart_total_amount'] = $cart_total_amount;
$sts->template['template18_stylesheet1'] = $template18_stylesheet1;
$sts->template['template2_stylesheet1'] = $template2_stylesheet1;
$sts->template['copyright_year'] = date('Y', time());
$sts->template['categories_list_chunk1'] = $categories_list_chunk1;
$sts->template['categories_list_chunk2'] = $categories_list_chunk2;
$sts->template['categories_list_chunk3'] = $categories_list_chunk3;
$sts->template['customer_orders_count'] = $customer_orders_count;
$sts->template['customer_wishlist_items_count'] = $customer_wishlist_items_count;
function get_block_content($template_dir_name, $block){
    $content = '';
	$path = DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/' . $template_dir_name . '/blocks/' . $block . '.php.html';
	
	if ( file_exists( $path ) ){
		return file_get_contents($path);
	}
    return $content;
}
function generateSlider($template_id, $banner_group_name){
    $html = '';
    $query = tep_db_query("select banners_image, banners_url from banners where template='" . (int)$template_id . "' and banners_group='" . tep_db_prepare_input($banner_group_name) . "' and status='1'");
    if (tep_db_num_rows($query)){  
        $html = '<script src="includes/jquery.cycle2.min.js"></script>';
		$html .= '<div class="cycle-slideshow" data-cycle-fx=scrollHorz data-cycle-timeout=5000 data-cycle-slides="> a">';
        
        if($template_id ==21){
		$html .= '<span class="cycle-prev"><img src="/images/controls_prev.png"></span><span class="cycle-next"><img src="/images/controls_next.png"></span>';
	  }
        while ($image = tep_db_fetch_array($query)){
			$url = trim($image['banners_url']);
            if(!empty($url)){
                if(stripos($url, 'http')=== false){
                    $url = tep_href_link($url, '', 'NOSSL', false);
                }
            } else {
                $url = '/';
            }
            $html .= '<a href="' . $url . '"><img src="' . DIR_WS_IMAGES . $image['banners_image'] . '" /></a>';
        }
        $html .= '</div>';
    }
    return $html;
}
function getCategoriesHtml($parent_id='0'){
    $response = '<ul style="z-index:999;">';
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        
        //$cPath = tep_get_path($category['categories_id']);
        $cPath = $category['categories_id'];
        //$response .= '<li><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
        $response .= '<li><a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
        //$response .= getCategoriesHtml($category['categories_id']);
        $response .= '</li>';
     }
    $response .= '</ul>';
     return $response;
}
function getCategoriesHtmlTpl18($parent_id='0', $level = '0'){
    $response = '';
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        $cPath = $category['categories_id'];
        if ($parent_id=='0'){
            $response .= '<li class="selected_0">';
            $response .= '<a class="current_parent" href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
            $response .= '<ul class="nav-child unstyled">';
        } else {
            $response .= '<li class="selected_' . $level . '">';
            $response .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
            $response .= '</li>';
        }
        if ($level<1){
            $response .= getCategoriesHtmlTpl18($category['categories_id'], $level + 1);
        }
        if ($parent_id=='0'){
            $response .= '</ul>';
            $response .= '</li>';
        }
     }
     return $response;
}
function getCategoriesHtmlTpl19($parent_id='0', $level = '0'){
    $response = '';
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        $cPath = $category['categories_id'];
        if ($parent_id=='0'){
            $response .= '<div class="sub-cat-name" >';
            $response .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
   $new_sql = tep_db_query("select count(*) from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$category['categories_id'] . "' and c.categories_status='1' having count(*)>0  order by cd.categories_name");
		$count = tep_db_num_rows($new_sql);			
			if($count) {
            	$response .= '<div class="drop-box-subsubcat responsive_position" style="display: none;">';
			}
           // $response .= '<div class="drop-box-subsubcat responsive_position" style="display: none;">';
        } else {
            $response .= '<div class="subsub-cat-name">';
            $response .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
            $response .= '</div>';
        }
        if ($level<1){
            //$response .= getCategoriesHtmlTpl19($category['categories_id'], $level + 1);
            if($count) {
            $response .= getCategoriesHtmlTpl19($category['categories_id'], $level + 1);
			}
        }
        if ($parent_id=='0'){
            if($count) {
            $response .= '</div>';
            }
            $response .= '</div>';
        }
     }
     return $response;
}
function getCategoriesHtmlTpl21($parent_id='0', $level = '0'){
    $response = '';
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        $cPath = $category['categories_id'];
        if ($parent_id=='0'){
            $response .= '<li class="selected_0">';
            $response .= '<a class="current_parent" href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
            $response .= '<ul class="nav-child unstyled">';
        } else {
            $response .= '<li class="selected_' . $level . '">';
            $response .= '<a href="' . tep_href_link(FILENAME_DEFAULT, 'cPath=' . $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
            $response .= '</li>';
        }
        if ($level<1){
            $response .= getCategoriesHtmlTpl18($category['categories_id'], $level + 1);
        }
        if ($parent_id=='0'){
            $response .= '</ul>';
            $response .= '</li>';
        }
     }
     return $response;
}
function getCustomerReviews($max_count = '10'){
    $response = array();
    $reviews_query = tep_db_query("select r.reviews_id as id, r.products_id as product_id, r.customers_id, r.customers_nickname as reviewer, r.reviews_rating as rating, r.date_added as posted_on, r.reviews_title, r.customers_nickname, rd.reviews_text as content, p.products_image as image, p.products_mediumimage as mediumimage, pd.products_name as product_name from reviews r inner join reviews_description rd on (r.reviews_id=rd.reviews_id and rd.languages_id='1') inner join products p on r.products_id=p.products_id inner join products_description pd on (p.products_id=pd.products_id and pd.language_id='1') order by date_added desc limit 0, " . $max_count);
    while($review = tep_db_fetch_array($reviews_query)){
        $review['image'] = (tep_not_null($review['mediumimage']) ? $review['mediumimage'] : $review['image']);
        $image = '';
        if (tep_not_null($review['image'])) {
            $feed_status = is_xml_feed_product($review['product_id']);
            if ($feed_status) {
                $image = tep_small_image($review['image'], $review['product_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
            } else  {
                $image = tep_image(DIR_WS_IMAGES . $review['image'], $review['name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT,'class="subcatimages"');
					}
				}
        $response[$review['id']] = array(
            'product_name' => $review['product_name'], 
            'image' => $image, 
            'posted_on' => $review['posted_on'], 
            'reviewer' => $review['reviewer'], 
            'rating' => $review['rating'], 
            'content' => $review['content'], 
            'product_link' => tep_href_link('product_info.php', 'products_id=' . $review['product_id'], 'NONSSL', false), 
        );
    }
    return $response;
}
function getReviewsHtml($template_id, $reviews, $show_empty_struct = false){
    $html = '';
    //if ($template_id=='12'){
        $template = DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/full/template' . $template_id . '/blocks/customer_reviews.php.html';
        if ( file_exists( $template ) ){
            $template_struct = file_get_contents($template);
                $search = array(
                    '$review_image', 
                    '$review_product_name', 
                    '$review_posted_on', 
                    '$reviewer', 
                    '$review_rating', 
                    '$review_text', 
                    '$product_link', 
                );
            foreach($reviews as $review_id => $review){
                $replacements = array(
                    $review['image'], 
                    $review['product_name'], 
                    $review['posted_on'], 
                    $review['reviewer'], 
                    $review['rating'], 
                    $review['content'], 
                    $review['product_link'], 
                );
                $html .= str_ireplace($search, $replacements, $template_struct);
            }
        }
    //}
        if ($show_empty_struct && empty($html)){
            $replacements = array(
                '', 
                '', 
                '', 
                '', 
                '', 
                '',  
                '', 
            );
            $html .= str_ireplace($search, $replacements, $template_struct);
    }
    return $html;
}
function getSplitedCategoriesList(){
    $info = array();
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        $cPath = tep_get_path($category['categories_id']);
        //$response .= '<li><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath) . '">' . $category['categories_name'] . '</a>' . "\n";
		$info[] = '<li><a href="' . tep_href_link(FILENAME_DEFAULT, $cPath) . '">' . $category['categories_name'] . '</a></li>' . "\n";
        //$response .= '</li>';
     }
	//return $response;
	$parts = array(
		array_slice($info, 0, floor(count($info)/2)), 
		array_slice($info, floor(count($info)/2)), 
	);
	return $parts;
}
function getNavigationCategoriesListTpl20(){
    $info = array();
    $sql = tep_db_query("select c.categories_id, cd.categories_name from " . TABLE_CATEGORIES . " c inner join " . TABLE_CATEGORIES_DESCRIPTION . " cd on (c.categories_id=cd.categories_id and cd.language_id='1') where c.parent_id='" . (int)$parent_id . "' and c.categories_status='1' order by cd.categories_name");
     while($category = tep_db_fetch_array($sql)){
        $cPath = tep_get_path($category['categories_id']);
		$info[] = '<li><a class="color_dark tr_delay_hover" href="' . tep_href_link(FILENAME_DEFAULT, $cPath) . '">' . $category['categories_name'] . '</a></li>' . "\n";
     }
	$count = ceil(count($info)/3);
	$parts = array_chunk($info, $count);
	return $parts;
}
function show_search_filter(&$script){
    if ($script=='shop.php'){
        if (isset($_GET['cPath'])){
            $category = $_GET['cPath'];
            if (strrpos($category, '_')!==false){
                $category = substr($category, strrpos($category, '_')+1 );
            }
            $sql = tep_db_query("select count(*) as count from " . TABLE_CATEGORIES . " where parent_id='" . (int)$category . "'");
            $info = tep_db_fetch_array($sql);
            if ($info['count']>0){
                return false;
            } else {
                return true;
            }
            
        } elseif (isset($_GET['manufacturers_id'])) {
            return true;
        }
        return false;
    }
 if ($script == 'advanced_search_result.php') {
    return true;
    
   }
    return false;
}
if (!isset($sts->template['message'])){
    $sts->template['message'] = '';
}
$template_link = '';
if (checkmobile2()){
    if (!isset($_SESSION['switch_template_flag']) || $_SESSION['switch_template_flag']==false){
        if (!isset($_SESSION['switch_template_flag'])) $_SESSION['switch_template_flag']=false;
        $template_link = '<a href="' . tep_href_link('index.php', 'switch_flag=activate') . '">Switch to Main Site</a>';
    } else {
        $template_link = '<a href="' . tep_href_link('index.php', 'switch_flag=deactivate') . '">Switch to Mobile Site</a>';
    }
}
//$sts->template['switch_template_link'] = 'Switch to Main Site';
$sts->template['switch_template_link'] = $template_link;
$sts->template['store_logo'] = 'images/store_logo.png';
?>