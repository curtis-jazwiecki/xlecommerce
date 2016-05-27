<?php

/*

  $Id: create_account.php,v 1.65 2003/06/09 23:03:54 hpdl Exp $



  osCommerce, Open Source E-Commerce Solutions

  http://www.oscommerce.com



  Copyright (c) 2003 osCommerce



  Released under the GNU General Public License

*/



  require('includes/application_top.php');

// PWA BOF

  if (isset($HTTP_GET_VARS['guest']) && $cart->count_contents() < 1) tep_redirect(tep_href_link(FILENAME_SHOPPING_CART));

// PWA EOF

// needs to be included earlier to set the success message in the messageStack

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CREATE_ACCOUNT);



  $process = false;

  if (isset($HTTP_POST_VARS['action']) && ($HTTP_POST_VARS['action'] == 'process')) {

    $process = true;



    if (ACCOUNT_GENDER == 'true') {

      if (isset($HTTP_POST_VARS['gender'])) {

        $gender = tep_db_prepare_input($HTTP_POST_VARS['gender']);

      } else {

        $gender = false;

      }

    }

    $firstname = tep_db_prepare_input($HTTP_POST_VARS['firstname']);

    $lastname = tep_db_prepare_input($HTTP_POST_VARS['lastname']);

    if (ACCOUNT_DOB == 'true') $dob = tep_db_prepare_input($HTTP_POST_VARS['dob']);

    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email_address']);

   // if (ACCOUNT_COMPANY == 'true') $company = tep_db_prepare_input($HTTP_POST_VARS['company']);

    // BOF Separate Pricing Per Customer, added: field for tax id number

    if (ACCOUNT_COMPANY == 'true') {

    $company = tep_db_prepare_input($_POST['company']);

    $company_tax_id = tep_db_prepare_input($_POST['company_tax_id']);

    }

// EOF Separate Pricing Per Customer, added: field for tax id number



    $street_address = tep_db_prepare_input($HTTP_POST_VARS['street_address']);

    if (ACCOUNT_SUBURB == 'true') $suburb = tep_db_prepare_input($HTTP_POST_VARS['suburb']);

    $postcode = tep_db_prepare_input($HTTP_POST_VARS['postcode']);

    $city = tep_db_prepare_input($HTTP_POST_VARS['city']);

    if (ACCOUNT_STATE == 'true') {

      $state = tep_db_prepare_input($HTTP_POST_VARS['state']);

      if (isset($HTTP_POST_VARS['zone_id'])) {

        $zone_id = tep_db_prepare_input($HTTP_POST_VARS['zone_id']);

      } else {

        $zone_id = false;

      }

    }

    $country = tep_db_prepare_input($HTTP_POST_VARS['country']);

    $telephone = tep_db_prepare_input($HTTP_POST_VARS['telephone']);

    $fax = tep_db_prepare_input($HTTP_POST_VARS['fax']);

    if (isset($HTTP_POST_VARS['newsletter'])) {

      $newsletter = tep_db_prepare_input($HTTP_POST_VARS['newsletter']);

    } else {

      $newsletter = false;

    }

    $password = tep_db_prepare_input($HTTP_POST_VARS['password']);

    $confirmation = tep_db_prepare_input($HTTP_POST_VARS['confirmation']);



    $error = false;



    if (ACCOUNT_GENDER == 'true') {

      if ( ($gender != 'm') && ($gender != 'f') ) {

        $error = true;



        $messageStack->add('create_account', ENTRY_GENDER_ERROR);

      }

    }



    if (strlen($firstname) < ENTRY_FIRST_NAME_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_FIRST_NAME_ERROR);

    }



    if (strlen($lastname) < ENTRY_LAST_NAME_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_LAST_NAME_ERROR);

    }



    if (ACCOUNT_DOB == 'true') {

      if (checkdate(substr(tep_date_raw($dob), 4, 2), substr(tep_date_raw($dob), 6, 2), substr(tep_date_raw($dob), 0, 4)) == false) {

        $error = true;



        $messageStack->add('create_account', ENTRY_DATE_OF_BIRTH_ERROR);

      }

    }



    if (strlen($email_address) < ENTRY_EMAIL_ADDRESS_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR);

    } elseif (tep_validate_email($email_address) == false) {

      $error = true;



      $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);

    } else {

	/*

      $check_email_query = tep_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where

	  customers_email_address = '" . tep_db_input($email_address) . "'");

	  */

      // PWA BOF 2b

      $check_email_query = tep_db_query("select count(*) as total from " . TABLE_CUSTOMERS . " where customers_email_address = '" . tep_db_input($email_address) . "' and guest_account != '1'");

      // PWA EOF 2b

      $check_email = tep_db_fetch_array($check_email_query);

      if ($check_email['total'] > 0) {

        $error = true;



        $messageStack->add('create_account', ENTRY_EMAIL_ADDRESS_ERROR_EXISTS);

      }

    }



    if (strlen($street_address) < ENTRY_STREET_ADDRESS_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_STREET_ADDRESS_ERROR);

    }



    if (strlen($postcode) < ENTRY_POSTCODE_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_POST_CODE_ERROR);

    }



    if (strlen($city) < ENTRY_CITY_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_CITY_ERROR);

    }



    if (is_numeric($country) == false) {

      $error = true;



      $messageStack->add('create_account', ENTRY_COUNTRY_ERROR);

    }



     $zone_id = 0;

    if (ACCOUNT_STATE == 'true') {

      $check_query = tep_db_query("select count(*) as total from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "'");

      $check = tep_db_fetch_array($check_query);

      $entry_state_has_zones = ($check['total'] > 0);

      if ($entry_state_has_zones == true) {

        $zone_query = tep_db_query("select distinct zone_id, zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' and (zone_name like '" . tep_db_input($state) . "%' or zone_code like '%" . tep_db_input($state) . "%')");

        if (tep_db_num_rows($zone_query) == 1) {

          $zone = tep_db_fetch_array($zone_query);

          $zone_id = $zone['zone_id'];
           $state=$zone['zone_name'];

        } else {

          $error = true;



          $messageStack->add('create_account', ENTRY_STATE_ERROR_SELECT);

        }

      } else {

        if (strlen($state) < ENTRY_STATE_MIN_LENGTH) {

          $error = true;



          $messageStack->add('create_account', ENTRY_STATE_ERROR);

        }

      }

    }



    if (strlen($telephone) < ENTRY_TELEPHONE_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_TELEPHONE_NUMBER_ERROR);

    }



/*

    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {

      $error = true;



      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);

    } elseif ($password != $confirmation) {

      $error = true;



      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);

    }

	



}

    if ($error == false) {

	*/

// PWA BOF

    if (!isset($HTTP_GET_VARS['guest']) && !isset($HTTP_POST_VARS['guest'])) {

// PWA EOF



	    if (strlen($password) < ENTRY_PASSWORD_MIN_LENGTH) {

	      $error = true;



	      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR);

	    } elseif ($password != $confirmation) {

	      $error = true;



	      $messageStack->add('create_account', ENTRY_PASSWORD_ERROR_NOT_MATCHING);

	    }

// PWA BOF

} 

// PWA EOF

    if ($error == false) {

		// PWA BOF 2b

		if (!isset($HTTP_GET_VARS['guest']) && !isset($HTTP_POST_VARS['guest']))

		{

			$dbPass = tep_encrypt_password($password);

			$guestaccount = '0';

		}else{

			$dbPass = 'null';

			$guestaccount = '1';

		}

		// PWA EOF 2b



      $sql_data_array = array('customers_firstname' => $firstname,

                              'customers_lastname' => $lastname,

                              'customers_email_address' => $email_address,

                              'customers_telephone' => $telephone,

                              'customers_fax' => $fax,

                              'customers_newsletter' => $newsletter,

							  /*

                              'customers_password' => tep_encrypt_password($password),

							  */

                              // PWA BOF 2b

                              'customers_password' => $dbPass,

                              'guest_account' => $guestaccount, 

                              // PWA EOF 2b

                              'customers_ip_address' => tep_get_ip_address());



      if (ACCOUNT_GENDER == 'true') $sql_data_array['customers_gender'] = $gender;

      if (ACCOUNT_DOB == 'true') $sql_data_array['customers_dob'] = tep_date_raw($dob);

      // BOF Separate Pricing Per Customer

   // if you would like to have an alert in the admin section when either a company name has been entered in

   // the appropriate field or a tax id number, or both then uncomment the next line and comment the default

   // setting: only alert when a tax_id number has been given

   //    if ( (ACCOUNT_COMPANY == 'true' && tep_not_null($company) ) || (ACCOUNT_COMPANY == 'true' && tep_not_null($company_tax_id) ) ) {

	  if ( ACCOUNT_COMPANY == 'true' && tep_not_null($company_tax_id)  ) {

      $sql_data_array['customers_group_ra'] = '1';

// entry_company_tax_id moved from table address_book to table customers in version 4.2.0

      $sql_data_array['entry_company_tax_id'] = $company_tax_id; 

    }

// EOF Separate Pricing Per Customer





      tep_db_perform(TABLE_CUSTOMERS, $sql_data_array);



      $customer_id = tep_db_insert_id();



      $sql_data_array = array('customers_id' => $customer_id,

                              'entry_firstname' => $firstname,

                              'entry_lastname' => $lastname,

                              'entry_street_address' => $street_address,

                              'entry_postcode' => $postcode,

                              'entry_city' => $city,

                              'entry_country_id' => $country);



      if (ACCOUNT_GENDER == 'true') $sql_data_array['entry_gender'] = $gender;

      if (ACCOUNT_COMPANY == 'true') $sql_data_array['entry_company'] = $company;

      if (ACCOUNT_SUBURB == 'true') $sql_data_array['entry_suburb'] = $suburb;

      if (ACCOUNT_STATE == 'true') {

        if ($zone_id > 0) {

          $sql_data_array['entry_zone_id'] = $zone_id;

          $sql_data_array['entry_state'] = '';

        } else {

          $sql_data_array['entry_zone_id'] = '0';

          $sql_data_array['entry_state'] = $state;

        }

      }

// PWA BOF

     if (isset($HTTP_GET_VARS['guest']) or isset($HTTP_POST_VARS['guest']))

       tep_session_register('customer_is_guest');

// PWA EOF

      tep_db_perform(TABLE_ADDRESS_BOOK, $sql_data_array);



      $address_id = tep_db_insert_id();



      tep_db_query("update " . TABLE_CUSTOMERS . " set customers_default_address_id = '" . (int)$address_id . "' where customers_id = '" . (int)$customer_id . "'");



      tep_db_query("insert into " . TABLE_CUSTOMERS_INFO . " (customers_info_id, customers_info_number_of_logons, customers_info_date_account_created,ip_address) values ('" . (int)$customer_id . "', '0', now(),'".tep_get_ip_address()."')");



      if (SESSION_RECREATE == 'True') {

        tep_session_recreate();

      }

      

      // BOF Separate Pricing Per Customer

// register SPPC session variables for the new customer

// if there is code above that puts new customers directly into another customer group (default is retail)

// then the below code need not be changed, it uses the newly inserted customer group

      $check_customer_group_info = tep_db_query("select c.customers_group_id, cg.customers_group_show_tax, cg.customers_group_tax_exempt, cg.group_specific_taxes_exempt from " . TABLE_CUSTOMERS . " c left join " . TABLE_CUSTOMERS_GROUPS . " cg using(customers_group_id) where c.customers_id = '" . $customer_id . "'");

      $customer_group_info = tep_db_fetch_array($check_customer_group_info);

      $sppc_customer_group_id = $customer_group_info['customers_group_id'];

      $sppc_customer_group_show_tax = (int)$customer_group_info['customers_group_show_tax'];

      $sppc_customer_group_tax_exempt = (int)$customer_group_info['customers_group_tax_exempt'];

      $sppc_customer_specific_taxes_exempt = '';

      if (tep_not_null($customer_group_info['group_specific_taxes_exempt'])) {

        $sppc_customer_specific_taxes_exempt = $customer_group_info['group_specific_taxes_exempt'];

      }

// EOF Separate Pricing Per Customer





      $customer_first_name = $firstname;

      $customer_default_address_id = $address_id;

      $customer_country_id = $country;

      $customer_zone_id = $zone_id;

      tep_session_register('customer_id');

      tep_session_register('customer_first_name');

      tep_session_register('customer_default_address_id');

      tep_session_register('customer_country_id');

      tep_session_register('customer_zone_id');

// PWA BOF

      if (isset($HTTP_GET_VARS['guest']) or isset($HTTP_POST_VARS['guest'])) tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING));

// PWA EOF

      

      // BOF Separate Pricing Per Customer

      tep_session_register('sppc_customer_group_id');

      tep_session_register('sppc_customer_group_show_tax');

      tep_session_register('sppc_customer_group_tax_exempt');

      tep_session_register('sppc_customer_specific_taxes_exempt');

// EOF Separate Pricing Per Customer





// Ingo PWA

      if (isset($HTTP_GET_VARS['guest'])) tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING));

// restore cart contents

      $cart->restore_contents();



// build the message content

      $name = $firstname . ' ' . $lastname;



      if (ACCOUNT_GENDER == 'true') {

         if ($gender == 'm') {

           $email_text = sprintf(EMAIL_GREET_MR, $lastname);

         } else {

           $email_text = sprintf(EMAIL_GREET_MS, $lastname);

         }

      } else {

        $email_text = sprintf(EMAIL_GREET_NONE, $firstname);

      }



      $email_text .= EMAIL_WELCOME . EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;



// ###### Added CCGV Contribution #########

  if (NEW_SIGNUP_GIFT_VOUCHER_AMOUNT > 0) {

    $coupon_code = create_coupon_code();

    $insert_query = tep_db_query("insert into " . TABLE_COUPONS . " (coupon_code, coupon_type, coupon_amount, date_created) values ('" . $coupon_code . "', 'G', '" . NEW_SIGNUP_GIFT_VOUCHER_AMOUNT . "', now())");

    $insert_id = tep_db_insert_id($insert_query);

    $insert_query = tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $insert_id ."', '0', 'Admin', '" . $email_address . "', now() )");



    $email_text .= sprintf(EMAIL_GV_INCENTIVE_HEADER, $currencies->format(NEW_SIGNUP_GIFT_VOUCHER_AMOUNT)) . "\n\n" .

                   sprintf(EMAIL_GV_REDEEM, $coupon_code) . "\n\n" .

                   EMAIL_GV_LINK . tep_href_link(FILENAME_GV_REDEEM, 'gv_no=' . $coupon_code,'NONSSL', false) .

                   "\n\n";

  }

  if (NEW_SIGNUP_DISCOUNT_COUPON != '') {

		$coupon_code = NEW_SIGNUP_DISCOUNT_COUPON;

    $coupon_query = tep_db_query("select * from " . TABLE_COUPONS . " where coupon_code = '" . $coupon_code . "'");

    $coupon = tep_db_fetch_array($coupon_query);

		$coupon_id = $coupon['coupon_id'];		

    $coupon_desc_query = tep_db_query("select * from " . TABLE_COUPONS_DESCRIPTION . " where coupon_id = '" . $coupon_id . "' and language_id = '" . (int)$languages_id . "'");

    $coupon_desc = tep_db_fetch_array($coupon_desc_query);

    $insert_query = tep_db_query("insert into " . TABLE_COUPON_EMAIL_TRACK . " (coupon_id, customer_id_sent, sent_firstname, emailed_to, date_sent) values ('" . $coupon_id ."', '0', 'Admin', '" . $email_address . "', now() )");

    $email_text .= EMAIL_COUPON_INCENTIVE_HEADER .  "\n" .

                   sprintf("%s", $coupon_desc['coupon_description']) ."\n\n" .

                   sprintf(EMAIL_COUPON_REDEEM, $coupon['coupon_code']) . "\n\n" .

                   "\n\n";



  }

  // Points/Rewards system V2.1rc2a BOF

      if ((USE_POINTS_SYSTEM == 'true') && (NEW_SIGNUP_POINT_AMOUNT > 0)) {

	      tep_add_welcome_points($customer_id);

	      

	      $points_account = '<a href="' . tep_href_link(FILENAME_MY_POINTS, '', 'SSL') . '"><b><u>' . EMAIL_POINTS_ACCOUNT . '</u></b></a>.';

	      $points_faq = '<a href="' . tep_href_link(FILENAME_MY_POINTS_HELP, '', 'NONSSL') . '"><b><u>' . EMAIL_POINTS_FAQ . '</u></b></a>.';

	      $text_points = sprintf(EMAIL_WELCOME_POINTS , $points_account, number_format(NEW_SIGNUP_POINT_AMOUNT,POINTS_DECIMAL_PLACES), $currencies->format(tep_calc_shopping_pvalue(NEW_SIGNUP_POINT_AMOUNT)), $points_faq) ."\n\n";

	      

	      $points_text .= $text_points ;

      } 

// Points/Rewards system V2.1rc2a EOF

  //// #12 10Jan2014 (MA) BOF

  $template_query = tep_db_query("SELECT `email_templates_content` FROM `email_templates` WHERE `email_templates_key` = 'EMAIL_TEMPLATE_SIGNUP'");

  if(tep_db_num_rows($template_query)){

    $template_array = tep_db_fetch_array($template_query);

    $var_to_replace = array('{FIRST_NAME}','{LAST_NAME}','{DISCOUNT_COUPON}','{GIFT_VOUCHER}','{TITLE}','{GIFT_VOUCHER_AMOUNT}','{DISCOUNT_COUPON_AMOUNT}','{STORE_NAME}','{STORE_OWNER}','{STORE_OWNER_EMAIL}','{GIFT_VOUCHER_LINK}','{POINTS_TEXTS}');

    if ($gender == 'm') {

        $title = 'Mr.';

    }elseif($gender == 'f'){

        $title = 'Ms.';

    }else{

        $title = '';

    }

    $var_values = array($firstname,$lastname,$discount_code,$gift_voucher,$title,$gv_amount,'',STORE_NAME,STORE_OWNER,STORE_OWNER_EMAIL_ADDRESS,$gv_link,$points_text);

    $email_text = str_replace($var_to_replace, $var_values, $template_array['email_templates_content']);

  }

  //// #12 10Jan2014 (MA) EOF



  

  



  

//    $email_text .= EMAIL_TEXT . EMAIL_CONTACT . EMAIL_WARNING;

// ###### End Added CCGV Contribution #########



    //  tep_mail($name, $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

  // BOF Separate Pricing Per Customer: alert shop owner of account created by a company

// if you would like to have an email when either a company name has been entered in

// the appropriate field or a tax id number, or both then uncomment the next line and comment the default

// setting: only email when a tax_id number has been given

//    if ( (ACCOUNT_COMPANY == 'true' && tep_not_null($company) ) || (ACCOUNT_COMPANY == 'true' && tep_not_null($company_tax_id) ) ) {

      if ( ACCOUNT_COMPANY == 'true' && tep_not_null($company_tax_id) ) {

      $alert_email_text = "Please note that " . $firstname . " " . $lastname . " of the company: " . $company . " has created an account.";

      tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, 'Company account created', $alert_email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

      }else{

		  tep_mail($name, $email_address, EMAIL_SUBJECT, $email_text, STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS);

	  }

// EOF Separate Pricing Per Customer: alert shop owner of account created by a company



	if ($newsletter){

		tep_db_query("insert into subscribers (email, name, is_registered) values ('" . tep_db_input($email_address) . "', '" . tep_db_input($firstname . ' ' . $lastname) . "', '1') on duplicate key update name='" . tep_db_input($firstname) . "', is_registered='1'");

	}



//BEGIN DIRECT TO CHECKOUT MODIFICATION CJ 083115

      //tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, '', 'SSL'));

          if ($cart->count_contents() < 1) {        tep_redirect(tep_href_link(FILENAME_CREATE_ACCOUNT_SUCCESS, '', 'SSL'));   } else {   tep_redirect(tep_href_link(FILENAME_CHECKOUT_SHIPPING));   }

//BEGIN DIRECT TO CHECKOUT MODIFICATION CJ 083115 

    }

  }

// Ingo PWA

  if (tep_session_is_registered('pwa_array_customer') && tep_session_is_registered('pwa_array_address')) {

    $gender = isset($pwa_array_customer['customers_gender'])?$pwa_array_customer['customers_gender']:'';

    $company = isset($pwa_array_address['entry_company'])? $pwa_array_address['entry_company']:'';

    $firstname = isset($pwa_array_customer['customers_firstname'])? $pwa_array_customer['customers_firstname']:'';

    $lastname = isset($pwa_array_customer['customers_lastname'])? $pwa_array_customer['customers_lastname']:'';

    $dob = isset($pwa_array_customer['customers_dob'])? substr($pwa_array_customer['customers_dob'],-2).'.'.substr($pwa_array_customer['customers_dob'],4,2).'.'.substr($pwa_array_customer['customers_dob'],0,4):'';

    $email_address = isset($pwa_array_customer['customers_email_address'])? $pwa_array_customer['customers_email_address']:'';

    $street_address = isset($pwa_array_address['entry_street_address'])? $pwa_array_address['entry_street_address']:'';

    $suburb = isset($pwa_array_address['entry_suburb'])? $pwa_array_address['entry_suburb']:'';

    $postcode = isset($pwa_array_address['entry_postcode'])? $pwa_array_address['entry_postcode']:'';

    $city = isset($pwa_array_address['entry_city'])? $pwa_array_address['entry_city']:'';

    $state = isset($pwa_array_address['entry_state'])? $pwa_array_address['entry_state']:'0';

    $country = isset($pwa_array_address['entry_country_id'])? $pwa_array_address['entry_country_id']:'';

    $telephone = isset($pwa_array_customer['customers_telephone'])? $pwa_array_customer['customers_telephone']:'';

    $fax = isset($pwa_array_customer['customers_fax'])? $pwa_array_customer['customers_fax']:'';

  }

  /*

  $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));

  */

 // PWA BOF

 if (!isset($HTTP_GET_VARS['guest']) && !isset($HTTP_POST_VARS['guest'])){

   $breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CREATE_ACCOUNT, '', 'SSL'));

 }else{

   $breadcrumb->add(NAVBAR_TITLE_PWA, tep_href_link(FILENAME_CREATE_ACCOUNT, 'guest=guest', 'SSL'));

 }

// PWA EOF

?>



<?php

if ($messageStack->size('create_account') > 0) {

    $sts->template['message'] = $messageStack->output('create_account');

	

}else{

	$sts->template['message'] = '';

}



$sts->template['action_url'] =  tep_href_link(FILENAME_CREATE_ACCOUNT, (isset($HTTP_GET_VARS['guest'])? 'guest=guest':''), 'SSL');

$sts->template['form_information_text'] =  sprintf(TEXT_ORIGIN_LOGIN, tep_href_link(FILENAME_LOGIN, tep_get_all_get_params(), 'SSL'), tep_href_link(FILENAME_PRIVACY, tep_get_all_get_params(), 'SSL'));



$sts->template['account_gender_enabled'] =  ACCOUNT_GENDER;

$sts->template['account_company_enabled'] =  ACCOUNT_COMPANY;

$sts->template['account_suburb_enabled'] =  ACCOUNT_SUBURB;

$sts->template['account_state_enabled'] =  ACCOUNT_STATE;

$sts->template['account_dob_enabled'] =  ACCOUNT_DOB;

if (!isset($HTTP_GET_VARS['guest']) && !isset($HTTP_POST_VARS['guest'])) {

	$sts->template['is_guest'] =  false;

}else{

	$sts->template['is_guest'] =  true;

}



?>

<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">

<html <?php echo HTML_PARAMS; ?>>

<head>

<?php

// BOF: Header Tag Controller v2.6.0

if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {

  require(DIR_WS_INCLUDES . 'header_tags.php');

} else {

?> 

  <title><?php echo TITLE; ?></title>

<?php

}

// EOF: Header Tag Controller v2.6.0

?>

<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">

<style type="text/css">

$stylesheet

</style>

<?php 

ob_start();

require('includes/form_check.js.php');

$javascript_validation_code = ob_get_contents();

ob_end_clean();

echo $javascript_validation_code;

$sts->template['javascript_validation_code'] = $javascript_validation_code;

?>

</head>

<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">

<!-- header //-->

<?php require(DIR_WS_INCLUDES . 'header.php'); ?>

<!-- header_eof //-->



<!-- body //-->

<table border="0" width="100%" cellspacing="3" cellpadding="3">

  <tr>

    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">

<!-- left_navigation //-->

<?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>

<!-- left_navigation_eof //-->

    </table></td>

<!-- body_text //-->

	<?php /*

    <td width="100%" valign="top"><?php echo tep_draw_form('create_account', tep_href_link(FILENAME_CREATE_ACCOUNT, (isset($HTTP_GET_VARS['guest'])? 'guest=guest':''), 'SSL'), 'post', 'onSubmit="return check_form(create_account);"'). tep_draw_hidden_field('action', 'process'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">

	<?php */ ?>

    <!-- PWA BOF -->

    <td width="100%" valign="top"><?php echo tep_draw_form('create_account', tep_href_link(FILENAME_CREATE_ACCOUNT, (isset($HTTP_GET_VARS['guest'])? 'guest=guest':''), 'SSL'), 'post', 'onSubmit="return check_form(create_account);"') . tep_draw_hidden_field('action', 'process'); ?><table border="0" width="100%" cellspacing="0" cellpadding="0">

    <!-- PWA EOF -->

      <tr>

        <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="0">

        </table></td>

      </tr>



	  <tr><td width="5" height="20" align="left" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgL.jpg"></td><td class="infoBoxHeadingLogin" align="left"><b><?php echo CATEGORY_PERSONAL; ?></b></td><td width="5" height="20" align="right" background="images/template/infoboxbg.jpg"><img src="images/template/infoboxbgR.jpg"></td></tr>

<?php

  if ($messageStack->size('create_account') > 0) {

?>



      <tr>

        <td colspan="3"><?php echo $messageStack->output('create_account'); ?></td>

      </tr>

      <tr>

        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

<?php

  } ?>

      <?php /*<tr>

        <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

          <tr>

            <td class="main"><b><?php echo CATEGORY_PERSONAL; ?></b></td>

           <td class="inputRequirement" align="right"><?php echo FORM_REQUIRED_INFORMATION; ?></td>

          </tr>

        </table></td>

      </tr>

	  */?>

	   <tr>

        <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="0" style="border:1px solid #cacaca;">

					  <tr>

						<td class="main"><table border="0" width="100%" cellspacing="" cellpadding="0">

									<tr><td class="inputRequirement" align="right"><?php echo FORM_REQUIRED_INFORMATION; ?></td></tr>

                                     <tr>

        <td class="smallText" colspan="3"><?php echo sprintf(TEXT_ORIGIN_LOGIN, tep_href_link(FILENAME_LOGIN, tep_get_all_get_params(), 'SSL'), tep_href_link(FILENAME_PRIVACY, tep_get_all_get_params(), 'SSL')); ?></td>

      </tr>

					<tr class="infoBoxContents">

						<td><table border="0" cellspacing="2" cellpadding="2">

                                         

      <tr><td colspan="3">&nbsp;</td></tr>

			 <?php

			  if (ACCOUNT_GENDER == 'true') {

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_GENDER; ?></td>

							<td class="main"><?php echo tep_draw_radio_field('gender', 'm') . '&nbsp;&nbsp;' . MALE . '&nbsp;&nbsp;' . tep_draw_radio_field('gender', 'f') . '&nbsp;&nbsp;' . FEMALE . '&nbsp;' . (tep_not_null(ENTRY_GENDER_TEXT) ? '<span class="inputRequirement">' . ENTRY_GENDER_TEXT . '</span>': ''); ?></td>

						  </tr>

			<?php

			  }

			?>

           <tr>

							<td class="main setSize"><?php echo ENTRY_FIRST_NAME; ?></td>

							<td class="main" align="left"><?php echo tep_draw_input_field('firstname') . '&nbsp;' . (tep_not_null(ENTRY_FIRST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_FIRST_NAME_TEXT . '</span>': ''); ?></td>

						  </tr>

						  <tr>

							<td class="main"><?php echo ENTRY_LAST_NAME; ?></td>

							<td class="main"><?php echo tep_draw_input_field('lastname') . '&nbsp;' . (tep_not_null(ENTRY_LAST_NAME_TEXT) ? '<span class="inputRequirement">' . ENTRY_LAST_NAME_TEXT . '</span>': ''); ?></td>

						  </tr>

			<?php

			  if (ACCOUNT_DOB == 'true') {

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_DATE_OF_BIRTH; ?></td>

							<td class="main"><?php echo tep_draw_input_field('dob') . '&nbsp;' . (tep_not_null(ENTRY_DATE_OF_BIRTH_TEXT) ? '<span class="inputRequirement">' . ENTRY_DATE_OF_BIRTH_TEXT . '</span>': ''); ?></td>

						  </tr>

			<?php

			  }

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_EMAIL_ADDRESS; ?></td>

							<td class="main"><?php echo tep_draw_input_field('email_address') . '&nbsp;' . (tep_not_null(ENTRY_EMAIL_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_EMAIL_ADDRESS_TEXT . '</span>': ''); ?></td>

						  </tr>

						</table></td>

					  </tr>

					</table></td>

				  </tr>

			<?php

			  if (ACCOUNT_COMPANY == 'true') {

			?>

				 <?php /* <tr>

					<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

				  </tr>

				  <tr>

					<td class="main"><b><?php echo CATEGORY_COMPANY; ?></b></td>

				  </tr>*/?>

				  <tr>

					<td><table border="0" width="100%" cellspacing="0" cellpadding="0">

					  <tr class="infoBoxContents">

						<td><table border="0" cellspacing="2" cellpadding="2">

						  <tr>

							<td class="main setSize"><?php echo ENTRY_COMPANY; ?></td>

							<td class="main"><?php echo tep_draw_input_field('company') . '&nbsp;' . (tep_not_null(ENTRY_COMPANY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TEXT . '</span>': ''); ?></td>

						  </tr>

                                                  <!-- BOF Separate Pricing Per Customer: field for tax id number -->

              <tr>

                <td class="main"><?php echo ENTRY_COMPANY_TAX_ID; ?></td>

                <td class="main"><?php echo tep_draw_input_field('company_tax_id') . '&nbsp;' . (tep_not_null(ENTRY_COMPANY_TAX_ID_TEXT) ? '<span class="inputRequirement">' . ENTRY_COMPANY_TAX_ID_TEXT . '</span>': ''); ?></td>

              </tr>

<!-- EOF Separate Pricing Per Customer: field for tax id number -->



						</table></td>

					  </tr>

					</table></td>

				  </tr>

			<?php

			  }



			?>

				<?php /*  <tr>

					<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

				  </tr>

				  <tr>

					<td class="main"><b><?php echo CATEGORY_ADDRESS; ?></b></td>

				  </tr>*/?>

				  <tr>

					<td><table border="0" width="100%" cellspacing="0" cellpadding="0">

					  <tr class="infoBoxContents">

						<td><table border="0" cellspacing="2" cellpadding="2">

						  <tr>

							<td class="main setSize"><?php echo ENTRY_STREET_ADDRESS; ?></td>

							<td class="main"><?php echo tep_draw_input_field('street_address') . '&nbsp;' . (tep_not_null(ENTRY_STREET_ADDRESS_TEXT) ? '<span class="inputRequirement">' . ENTRY_STREET_ADDRESS_TEXT . '</span>': ''); ?></td>

						  </tr>

			<?php

			  if (ACCOUNT_SUBURB == 'true') {

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_SUBURB; ?></td>

							<td class="main"><?php echo tep_draw_input_field('suburb') . '&nbsp;' . (tep_not_null(ENTRY_SUBURB_TEXT) ? '<span class="inputRequirement">' . ENTRY_SUBURB_TEXT . '</span>': ''); ?></td>

						  </tr>

			<?php

			  }

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_CITY; ?></td>

							<td class="main"><?php echo tep_draw_input_field('city') . '&nbsp;' . (tep_not_null(ENTRY_CITY_TEXT) ? '<span class="inputRequirement">' . ENTRY_CITY_TEXT . '</span>': ''); ?></td>

						  </tr><?php

			  if (ACCOUNT_STATE == 'true') {

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_STATE; ?></td>

							<td class="main">

			<?php

				if ($process == true) {

				  if ($entry_state_has_zones == true) {

					$zones_array = array();

					$zones_query = tep_db_query("select zone_name from " . TABLE_ZONES . " where zone_country_id = '" . (int)$country . "' order by zone_name");

					while ($zones_values = tep_db_fetch_array($zones_query)) {

					  $zones_array[] = array('id' => $zones_values['zone_name'], 'text' => $zones_values['zone_name']);

					}

					$sts->template['state'] = tep_draw_pull_down_menu('state', $zones_array);

					echo tep_draw_pull_down_menu('state', $zones_array);

				  } else {

					echo tep_draw_input_field('state');

					$sts->template['state'] = tep_draw_input_field('state');

				  }

				} else {

				  echo tep_draw_input_field('state');

				  $sts->template['state'] = tep_draw_input_field('state');

				}

			

				if (tep_not_null(ENTRY_STATE_TEXT)) echo '&nbsp;<span class="inputRequirement">' . ENTRY_STATE_TEXT;

			?>

							</td>

						  </tr>

						  <tr>

							<td class="main"><?php echo ENTRY_POST_CODE; ?></td>

							<td class="main"><?php echo tep_draw_input_field('postcode') . '&nbsp;' . (tep_not_null(ENTRY_POST_CODE_TEXT) ? '<span class="inputRequirement">' . ENTRY_POST_CODE_TEXT . '</span>': ''); ?></td>

						  </tr>

						  

			

			<?php

			  }

			?>

						  <tr>

							<td class="main"><?php echo ENTRY_COUNTRY; ?></td>

							<!--	//Categories Status MOD by tech1@outdoorbusinessnetwork.com -->

							<td class="main"><?php echo tep_get_country_list('country', '223') . '&nbsp;' . (tep_not_null(ENTRY_COUNTRY_TEXT) ? '<span class="inputRequirement">' . ENTRY_COUNTRY_TEXT . '</span>': ''); ?></td>

						   <!--	//Categories Status MOD by tech1@outdoorbusinessnetwork.com -->

							</tr>

						</table></td>

					  </tr>

					</table></td>

				  </tr>

				<?php /*  <tr>

					<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

				  </tr>

				  <tr>

					<td class="main"><b><?php echo CATEGORY_CONTACT; ?></b></td>

				  </tr>

				  */?>

				  <tr>

					<td><table border="0" width="100%" cellspacing="0" cellpadding="0">

					  <tr class="infoBoxContents">

						<td><table border="0" cellspacing="2" cellpadding="2">

						  <tr>

							<td class="main setSize"><?php echo ENTRY_TELEPHONE_NUMBER; ?></td>

							<td class="main"><?php echo tep_draw_input_field('telephone') . '&nbsp;' . (tep_not_null(ENTRY_TELEPHONE_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_TELEPHONE_NUMBER_TEXT . '</span>': ''); ?></td>

						  </tr>

						  <tr>

							<td class="main"><?php echo ENTRY_FAX_NUMBER; ?></td>

							<td class="main"><?php echo tep_draw_input_field('fax') . '&nbsp;' . (tep_not_null(ENTRY_FAX_NUMBER_TEXT) ? '<span class="inputRequirement">' . ENTRY_FAX_NUMBER_TEXT . '</span>': ''); ?></td>

						  </tr>

						</table></td>

					  </tr>

					</table></td>

				  </tr>

<?php

// PWA BOF

  if (!isset($HTTP_GET_VARS['guest']) && !isset($HTTP_POST_VARS['guest'])) {

// PWA EOF

?>

				<?php /*  <tr>

					<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

				  </tr>

				  <tr>

					<td class="main"><b><?php echo CATEGORY_OPTIONS; ?></b></td>

				  </tr>

				 */ ?>

				  <tr>

					<td><table border="0" width="100%" cellspacing="0" cellpadding="0">

					  <tr class="infoBoxContents">

						<td><table border="0" cellspacing="2" cellpadding="2">

						  <tr>

							<td class="main setSize"><?php echo tep_draw_separator('pixel_trans.gif', '100', '1'); ?><?php //echo ENTRY_NEWSLETTER; ?></td>

							<td class="main" align="right"><?php echo tep_draw_checkbox_field('newsletter', '1') . '&nbsp;' . (tep_not_null(ENTRY_NEWSLETTER_TEXT) ? '<span class="inputRequirement">' . ENTRY_NEWSLETTER_TEXT . '</span>': ''); ?><?php echo 'Sign me up for the ' . STORE_NAME . ' newsletter.';?> </td>

						  </tr>

						</table></td>

					  </tr>

					</table></td>

				  </tr>

                  <tr>

        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>



	   <tr>

        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

      <tr>

        <td colspan="3"><table border="0" width="100%" cellspacing="0" cellpadding="0" class="infoBox">

          <tr class="infoBoxContents">

            <td><table border="0" cellspacing="2" cellpadding="2">

			

              <tr>

                <td class="main setSize"><?php echo ENTRY_PASSWORD; ?></td>

                <td class="main"><?php echo tep_draw_password_field('password') . '&nbsp;' . (tep_not_null(ENTRY_PASSWORD_TEXT) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_TEXT . '</span>': ''); ?></td>

              </tr>

              <tr>

                <td class="main"><?php echo ENTRY_PASSWORD_CONFIRMATION; ?></td>

                <td class="main"><?php echo tep_draw_password_field('confirmation') . '&nbsp;' . (tep_not_null(ENTRY_PASSWORD_CONFIRMATION_TEXT) ? '<span class="inputRequirement">' . ENTRY_PASSWORD_CONFIRMATION_TEXT . '</span>': ''); ?></td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

<?php

  // PWA BOF

  }

  else

  { // Ingo PWA Ende

?>

 <tr>

   <td><?php echo tep_draw_hidden_field('guest', 'guest'); ?></td>

 </tr>

<?php } 

// PWA EOF

?>

</table></td></tr>  

	<?php

// Ingo PWA Beginn

  if (!isset($HTTP_GET_VARS['guest'])) {

?>

      

<?php

  } // Ingo PWA Ende

?>      

      <tr>

        <td colspan="3"><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>

      </tr>

      <tr>

        <td colspan="3"><table border="0" width="100%" cellspacing="1" cellpadding="2">

          <tr class="infoBoxContents">

            <td><table border="0" width="100%" cellspacing="0" cellpadding="2">

              <tr>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

                <td><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>

                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>

              </tr>

            </table></td>

          </tr>

        </table></td>

      </tr>

    </table></form></td>

<!-- body_text_eof //-->

    <td width="<?php echo BOX_WIDTH; ?>" valign="top"><table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">

<!-- right_navigation //-->

<?php include(DIR_WS_INCLUDES . 'column_right.php'); ?>

<!-- right_navigation_eof //-->

    </table></td>

  </tr>

</table>

<!-- body_eof //-->



<!-- footer //-->

<?php include(DIR_WS_INCLUDES . 'footer.php'); ?>

<!-- footer_eof //-->

<br>

</body>

</html>

<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>

