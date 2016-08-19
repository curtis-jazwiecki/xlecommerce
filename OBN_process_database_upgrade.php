<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');
include(DIR_FS_ADMIN . 'includes/configure.php');
$headers = apache_request_headers();
if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && base64_decode($_SERVER['PHP_AUTH_USER'])==OBN_RETAILER_TOKEN && base64_decode($_SERVER['PHP_AUTH_PW'])==OBN_RETAILER_TOKEN ){
    $response = '';
    $db_upgrade_file = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/db_update.sql';
    if (file_exists($db_upgrade_file)){
        if ($handle = @fopen($db_upgrade_file, "r")){
            $count = 0;
            while ( ($query = fgets($handle))!==false ){
                if (!empty($query)){
                    tep_db_query($query);
                    $count++;
                }
            }
            fclose($handle);
            $response = 'SUCCESS:' . $count . ' queries executed against retailer\'s database.';
        } else {
            $response = 'ERROR:Unable to load database upgrade file: ' . $db_upgrade_file . '. Contact support.';
        }
    } else {
        $response = 'ERROR:Database upgrade file: ' . $db_upgrade_file . ' has not reached at retailer\'s end. Contact support.';
    }
    unlink($db_upgrade_file);
} else {
    $response = 'Unauthorized Access: Please contact admin';
}
echo $response;
?>