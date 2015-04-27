<?php
require('includes/application_top.php');

$existcustomer = tep_db_query("SELECT 1 FROM `customers` WHERE `email` = '".$_POST['email']."'");
$extcus = tep_db_num_rows($existcustomer);
if(!$extcus)
{
$dob = explode('/',$_POST['birthday']);
   $dob = $dob[2].'-'.$dob[1].'-'.$dob[0];
  tep_db_query("insert into customers(customers_gender,customers_firstname,customers_lastname,customers_dob,customers_email_address,customers_telephone,customers_password) VALUES('".$_POST['gender']."','".$_POST['first_name']."','".$_POST['last_name']."','".$dob."','".$_POST['email']."','123456','123456')");  
  tep_db_query("insert into customers_info(customers_info_id) VALUES('".tep_db_insert_id()."')");
}   
  tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . (int)tep_db_insert_id() . "'");
        
        $customer_id = tep_db_insert_id();
        $customer_default_address_id = 1;
        $customer_first_name = $_POST['first_name'];
        $customer_country_id = 1;
        $customer_zone_id = 1;
        tep_session_register('customer_id');
        tep_session_register('customer_default_address_id');
        tep_session_register('customer_first_name');
        tep_session_register('customer_country_id');
        tep_session_register('customer_zone_id');

        tep_db_query("update " . TABLE_CUSTOMERS_INFO . " set customers_info_date_of_last_logon = now(), customers_info_number_of_logons = customers_info_number_of_logons+1 where customers_info_id = '" . (int)$customer_id . "'");

// reset session token
        $sessiontoken = md5(tep_rand() . tep_rand() . tep_rand() . tep_rand());

         echo 'ok';
?>