<?php

/* constants  */
define (DC_FOLDER, 'dresscode_theme/');
define (DC_STYLES, DC_FOLDER . 'dresscode_styles/');
define (DC_SCRIPTS, DC_FOLDER . 'dresscode_js/');
define (DC_BLOCKS, DC_FOLDER . 'dresscode_blocks/');
define (DC_IMAGES, DC_FOLDER . 'dresscode_images/');

if ( file_exists(DIR_WS_LANGUAGES . $language . '/dresscode_constants.php') ) {
    require(DIR_WS_LANGUAGES . $language . '/dresscode_constants.php');
}

$dresscode_grids = 3;
/* constants */

    $main_page_url = false;
    if (basename($PHP_SELF) == FILENAME_DEFAULT && !isset($HTTP_GET_VARS['cPath']) && !isset($HTTP_GET_VARS['manufacturers_id'])) {
        $main_page_url = true;
    }
    $product_page_url = false;
    if (basename($PHP_SELF) == FILENAME_PRODUCT_INFO){
        $product_page_url = true;
    }

/* header links */
if (tep_session_is_registered('customer_id')) {
    $login_url = tep_href_link(FILENAME_LOGOFF, '', 'SSL');
    $login_text = MENU_TEXT_LOGOUT;

    $create_account_url = tep_href_link(FILENAME_ACCOUNT, '', 'SSL');
} else {
    $login_url = tep_href_link(FILENAME_LOGIN, '', 'SSL');
    $login_text = MENU_TEXT_LOGIN;

    $create_account_url = tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL');
}



/* functions for texts */


function et_short_text($text, $limit='10') {
	$str = strlen($text) > $limit ? substr(strip_tags($text), 0, $limit) . '&hellip;' : strip_tags($text);
	return $str;
}

function trimmed_text($text, $limit='20') {
	/* mb_internal_encoding("UTF-8"); */
	$str = strlen($text) > $limit ? mb_substr(strip_tags($text), 0, $limit) . '&hellip;' : strip_tags($text);
	return $str;
}

function detectMobileDevice() {
    $mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
    $mobile_agents = array(
        'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
        'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
        'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
        'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
        'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
        'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
        'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
        'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
        'wapr','webc','winw','winw','xda ','xda-');

    if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
        return true;
    }
    else if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
        return true;
    }
    else if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini') > 0) {
        return true;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'macintosh') > 0) {
        return false;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'linux') > 0) {
        return false;
    }
    else if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows') > 0) {
        return false;
    }
    else if (in_array($mobile_ua,$mobile_agents)) {
        return true;
    }
    else {
        return true;
    }
}