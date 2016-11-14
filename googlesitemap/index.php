<?php

/**

 * Google XML Sitemap Feed Cron Script
 *
 * The Google sitemap service was announced on 2 June 2005 and represents
 * a huge development in terms of crawler technology.  This contribution is
 * designed to create the sitemap XML feed per the specification delineated 
 * by Google.  This cron script will call the code to create the scripts and 
 * eliminate the session auto start issues. 
 * @package Google-XML-Sitemap-Feed

 * @license http://opensource.org/licenses/gpl-license.php GNU Public License

 * @version 1.2

 * @link http://www.oscommerce-freelancers.com/ osCommerce-Freelancers
 * @link http://www.google.com/webmasters/sitemaps/docs/en/about.html About Google Sitemap 
 * @copyright Copyright 2005, Bobby Easland 
 * @author Bobby Easland 
 * @filesource
 */

	chdir('../');

	 
    require_once('includes/configure.php');

	define('DIR_WS_CATALOG', DIR_WS_HTTP_CATALOG);	
	
	require_once(DIR_WS_INCLUDES . 'filenames.php');
	require_once(DIR_WS_INCLUDES . 'database_tables.php');
	require_once(DIR_WS_FUNCTIONS . 'database.php');
	require_once(DIR_WS_FUNCTIONS . 'general.php');


	tep_db_connect() or die('Unable to connect to database server!');

	$configuration_query = tep_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from ' . TABLE_CONFIGURATION);

	while ($configuration = tep_db_fetch_array($configuration_query)) {
		define($configuration['cfgKey'], $configuration['cfgValue']);
	}

        if (USE_FRONTEND_CATEGORIES == 'true') {
    	define('TABLE_CATEGORIES', 'frontend_categories');
        define('TABLE_CATEGORIES_DESCRIPTION', 'frontend_categories_description');
        define('TABLE_PRODUCTS_TO_CATEGORIES', 'frontend_products_to_categories');
      } else {
    	define('TABLE_CATEGORIES', 'categories');
        define('TABLE_CATEGORIES_DESCRIPTION', 'categories_description');
        define('TABLE_PRODUCTS_TO_CATEGORIES', 'products_to_categories');
      }



	//function tep_not_null($value) {
		//if (is_array($value)) {
		//  if (sizeof($value) > 0) {
			//return true;
		//  } else {
			//return false;
		//  }
		//} else {
		//  if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
			//return true;
		//  } else {
			//return false;
		//  }
		//}
	//} # end function

	include_once(DIR_WS_CLASSES . 'language.php');
	$lng = new language();
	$languages_id = $lng->language['id'];

//if ( defined('SEO_URLS') && SEO_URLS == 'true' || defined('SEO_ENABLED') && SEO_ENABLED == 'true' ) {

 if ( (SEO_ENABLED == 'true') ) {

	function tep_session_is_registered( $var ){
		return false;
	}  # end function


	function tep_session_name(){
		return false;
	} # end function
	

	function tep_session_id(){
		return false;

	} # end function

	//function tep_parse_input_field_data($data, $parse) {
		//return strtr(trim($data), $parse);
	//} # end function

//	function tep_output_string($string, $translate = false, $protected = false) {
//		if ($protected == true) {
//		  return htmlspecialchars($string);
	//	} else {
	//	  if ($translate == false) {
			//return tep_parse_input_field_data($string, array('"' => '&quot;'));
		//  } else {
			//return tep_parse_input_field_data($string, $translate);
		//  }
		//}
	//} # end function	

	if ( file_exists(DIR_WS_CLASSES . 'seo.class.php') ){
		require_once(DIR_WS_CLASSES . 'seo.class.php');
		$seo_urls = new SEO_URL($languages_id);
	}	

	require_once(DIR_WS_FUNCTIONS . 'html_output.php');
	if ( file_exists(DIR_WS_CLASSES . 'cache.class.php') ){
		include(DIR_WS_CLASSES . 'cache.class.php');
		$cache = new cache($languages_id);
		if ( file_exists('includes/seo_cache.php') ){
			include('includes/seo_cache.php');
		}
		$cache->get_cache('GLOBAL');
	}
} # end if

define('GOOGLE_SITEMAP_INFO_CHANGE_FREQ','weekly');
define('GOOGLE_SITEMAP_INFO_PRIORITY','0.5');

require_once('googlesitemap/sitemap.class.php');

$google = new GoogleSitemap(DB_SERVER, DB_SERVER_USERNAME, DB_DATABASE, DB_SERVER_PASSWORD);
$submit = true;
echo '<pre>';

if ($google->GenerateProductSitemap()){

	echo 'Generated Google Product Sitemap Successfully' . "\n\n";

} else {

	$submit = false;

	echo 'ERROR: Google Product Sitemap Generation FAILED!' . "\n\n";

}



if ($google->GenerateCategorySitemap()){

	echo 'Generated Google Category Sitemap Successfully' . "\n\n";

} else {

	$submit = false;

	echo 'ERROR: Google Category Sitemap Generation FAILED!' . "\n\n";

}

if ($google->GenerateInfoPagesSitemap()){

	echo 'Generated Google Info Pages Sitemap Successfully' . "\n\n";

} else {

	$submit = false;

	echo 'ERROR: Google Info Pages Sitemap Generation FAILED!' . "\n\n";

}




if ($google->GenerateSitemapIndex()){
	echo 'Generated Google Sitemap Index Successfully' . "\n\n";

} else {

	$submit = false;

	echo 'ERROR: Google Sitemap Index Generation FAILED!' . "\n\n";

}



if ($submit){

	echo 'CONGRATULATIONS! All files generated successfully.' . "\n\n";

	echo 'If you have not already submitted the sitemap index to Google click the link below.' . "\n";

	echo 'Before you do I HIGHLY recommend that you view the XML files to make sure the data is correct.' . "\n\n";

	echo $google->GenerateSubmitURL() . "\n\n";

	echo 'For your convenience here is the CRON command for your site:' . "\n";

	echo 'php ' . dirname($_SERVER['SCRIPT_FILENAME']) . '/index.php' . "\n\n";

	echo 'Here is your sitemap index: ' . $google->base_url . 'sitemap.xml' . "\n";

	echo 'Here is your product sitemap: ' . $google->base_url . 'sitemapproducts.xml' . "\n";

	echo 'Here is your category sitemap: ' . $google->base_url . 'sitemapcategories.xml' . "\n";
	
     echo 'Here is your information sitemap: ' . $google->base_url . 'sitemapinformation.xml' . "\n";

} else {

	print_r($google->debug);

}



echo '</pre>';

?>
