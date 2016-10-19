<?php
/*
  $Id: contact_us.php,v 1.42 2003/06/12 12:17:07 hpdl Exp $
  
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.

*/
require('includes/application_top.php');
require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_CONTACT_US);

$error = false;
if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send')) {
    $name = tep_db_prepare_input($HTTP_POST_VARS['name']);
    $email_address = tep_db_prepare_input($HTTP_POST_VARS['email']);
    $enquiry = tep_db_prepare_input($HTTP_POST_VARS['enquiry']);
    // BOF Super Contact us enhancement 1.41
    $order_id = tep_db_prepare_input($HTTP_POST_VARS['order_id']);

    if ($order_id <> NULL){
        $enquiry = 'Order ID: ' . $order_id . "\n\n" . tep_db_prepare_input($HTTP_POST_VARS['enquiry']);
    } else {
        $enquiry = tep_db_prepare_input($HTTP_POST_VARS['enquiry']);
    }

    $emailsubject = tep_db_prepare_input($HTTP_POST_VARS['reason']) . ' ' . EMAIL_SUBJECT;

    if (tep_validate_email($email_address)) {
        //tep_mail(STORE_OWNER, STORE_OWNER_EMAIL_ADDRESS, $emailsubject, $enquiry, $name, $email_address);
        
        $send_to = tep_db_prepare_input($HTTP_POST_VARS['name']);
        list($department_id, $department_email) = explode('|', $send_to);
        if (empty($department_email)){
            $department_email = STORE_OWNER_EMAIL_ADDRESS;
        }
        $department_name = '';
        $department_query = tep_db_query("select department_name from departments where department_id='" . (int)$department_id . "'");
        if (tep_db_num_rows($department_query)){
            $department_info = tep_db_fetch_array($department_query);
            $department_name = $department_info['department_name'];
        }
        if (!empty($department_name)){
            $enquiry = "Department: $department_name\n" . $enquiry;
        }
        if (!tep_mail(STORE_OWNER, $department_email, $emailsubject, $enquiry, $name, $email_address)){
            //$error = true;
            //$messageStack->add('contact', 'Issues noticed while firing mail. please retry after some time.');
        }

        /*
        if (CONTACT_US_LIST !=''){
            $send_to_array=explode("," ,CONTACT_US_LIST);
            preg_match('/\<[^>]+\>/', $send_to_array[$send_to], $send_email_array);
            $send_to_email= eregi_replace (">", "", $send_email_array[0]);
            $send_to_email= eregi_replace ("<", "", $send_to_email);
    
            tep_mail(preg_replace('/\<[^astriks]astriks/', '', $send_to_array[$send_to]), $send_to_email, $emailsubject, $enquiry, $name, $email_address);
        } else {
            //tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=success'));
            tep_redirect(tep_href_link(FILENAME_CONTACT_US, 'action=send'));
        }
         */
        // EOF Super Contact us enhancement 1.41
    } else {
        $error = true;
        $messageStack->add('contact', ENTRY_EMAIL_ADDRESS_CHECK_ERROR);
    }
}

$breadcrumb->add(NAVBAR_TITLE, tep_href_link(FILENAME_CONTACT_US));
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
</head>
<body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
<?php
/*$contact_us_template_0 = file("includes/sts_templates/".$selected_template."/contact_us.php");
$text_display = '';
for($p=0;sizeof($contact_us_template_0) > $p; $p++){
    $text_display .= $contact_us_template_0[$p];
}*/
?>
<!-- body //-->
    <table border="0" width="100%" cellspacing="3" cellpadding="3">
        <tr>
            <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                <!-- left_navigation //-->
                <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
                <!-- left_navigation_eof //-->
                </table>
            </td>
            <!-- body_text //-->
            <td width="100%" valign="top">
            <?php echo tep_draw_form('contact_us', tep_href_link(FILENAME_CONTACT_US, 'action=send')); ?>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td class="pageHeading"><?php echo HEADING_TITLE; ?></td>
                                    <td class="pageHeading" align="right"></td>
                                </tr>
                            </table>
                        </td>
                        
                    </tr>
                    
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <?php
                    if ($messageStack->size('contact') > 0) {
                    ?>
                    <tr>
                        <td><?php echo $messageStack->output('contact'); ?></td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <?php
                    }

                    if (isset($HTTP_GET_VARS['action']) && ($HTTP_GET_VARS['action'] == 'send')) {
                    ?>
                    <tr>
                        <td class="main" align="center"><?php echo tep_image(DIR_WS_IMAGES . 'table_background_man_on_board.gif', HEADING_TITLE, '0', '0', 'align="left"') . TEXT_SUCCESS; ?></td>
                    </tr>
                    <tr>
                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                    </tr>
                    <tr>
                        <td>
                            <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                                <tr class="infoBoxContents">
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                                <td align="right" class="Button_Continue"><?php echo '<a href="' . tep_href_link(FILENAME_DEFAULT) . '">' . tep_image_button('button_continue.gif', IMAGE_BUTTON_CONTINUE) . '</a>'; ?></td>
                                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <!-- BOF Super Contact us enhancement 1.41 //-->
                    <?php
                    } else {
                        if (tep_session_is_registered('customer_id')) {
                            $account_query = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from " . TABLE_CUSTOMERS . " where customers_id = '" . (int)$customer_id . "'");
                            $account = tep_db_fetch_array($account_query);
                            $name = $account['customers_firstname'] . ' ' . $account['customers_lastname'];
                            $email = $account['customers_email_address'];
                    }
                    ?>
                    <tr>
                        <td>
                        <?php
                        /*$contact_us_template_0 = file("includes/sts_templates/".$selected_template."/contact_us.php");
                        $text_display = '';
                        for($p=0;sizeof($contact_us_template_0) > $p; $p++){
                            $text_display .= $contact_us_template_0[$p];
                        }*/
                        ?>
                            <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                                <tr class="infoBoxContents">
                                    <td>
                                        <table>
                                            <tr>
                                                <td class="main" valign="top" width=30%>
                                                    <b><?php echo (STORE_NAME_ADDRESS); ?></b><br><br>
                                                    <?php
                                                    $information_id = 85;
                                                    $information_query = tep_db_query("select information_title, information_description from " . TABLE_INFORMATION . " where language_id = '" . (int)$languages_id . "' and information_id = '" . (int)$information_id . "'");
                                                    $info = tep_db_fetch_array($information_query);
                                                    echo $info['information_description'];
                                                    ?>
                                                </td>
                                                </tr>
                                                
                                                <tr>
                    <td>
                     <div class="contact_us_map" style="display:none">
                     <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2984.228655339001!2d-83.66694678456801!3d41.58593587924654!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x883c77587aa7412d%3A0x8b244cb5b97abc53!2s1512+S+Reynolds+Rd%2C+Maumee%2C+OH+43537%2C+USA!5e0!3m2!1sen!2sin!4v1475222344567" width="600" height="450" frameborder="0" style="border:0" allowfullscreen></iframe>
                     </div>
                    </td>
                    </tr>
                                                
                                                <tr>
                                                <td class="main" valign="top" width="40%"><br>
                                                    Name:<br>
                                                    <?php echo tep_draw_input_field('name'); ?><br />
                                                    <?php echo ENTRY_EMAIL; ?><br>
                                                    <?php echo tep_draw_input_field('email'); ?><br />
                                                    <?php echo ENTRY_ORDER_ID; ?><br>
                                                    <?php echo tep_draw_input_field('order_id'); ?><br />
                                                    <?php
                                                    //if (CONTACT_US_LIST !=''){
                                                       // echo SEND_TO_TEXT . '<br>';
                                                        
                                                        /*if(SEND_TO_TYPE=='radio'){
                                                            foreach(explode("," ,CONTACT_US_LIST) as $k => $v) {
                                                                if($k==0){
                                                                    $checked=true;
    
                                                                } else {
                                                                    $checked=false;
                                                                }
                                                                echo tep_draw_radio_field('send_to', "$k", $checked). " " .preg_replace('/\<[^astriks]astriks/', '', $v);
                                                            }
                                                        } else {
                                                            foreach(explode("," ,CONTACT_US_LIST) as $k => $v) {
                                                                $send_to_array[] = array('id' => $k, 'text' => preg_replace('/\<[^astriks]astriks/', '', $v));
                                                            }
                                                            echo tep_draw_pull_down_menu('send_to',  $send_to_array);
                                                        }
                                                        echo('<br>');
                                                    }*/
                                                        $departments_col = array();
                                                        $departments_query = tep_db_query("select department_id, department_name, department_email from departments order by department_name");
                                                        while($department = tep_db_fetch_array($departments_query)){
                                                            $departments_col[] = array(
                                                                'id' => $department['department_id'] . '|' . $department['department_email'], 
                                                                'text' => $department['department_name'], 
                                                            );
                                                        }
                                                        //echo tep_draw_pull_down_menu('send_to',  $departments_col);
                                                    ?>
                                                    <?php echo ENTRY_ENQUIRY; ?><BR>
                                                    <?php echo tep_draw_textarea_field('enquiry', 'soft', 50, 15, tep_sanitize_string($_POST['enquiry']), '', false); ?>
                                                    <br />
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                            <br />
                            <table border="0" width="100%" cellspacing="1" cellpadding="2" class="infoBox">
                                <tr class="infoBoxContents">
                                    <td>
                                        <table border="0" width="100%" cellspacing="0" cellpadding="2">
                                            <tr>
                                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                                <td align="right" class="Button_Continue"><?php echo tep_image_submit('button_continue.gif', IMAGE_BUTTON_CONTINUE); ?></td>
                                                <td width="10"><?php echo tep_draw_separator('pixel_trans.gif', '10', '1'); ?></td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>   
                    <?php
                    }
                    ?>
                    <!-- EOF Super Contact us enhancement 1.41 //-->
                </table>
                </form>
            </td>
            <!-- body_text_eof //-->
            <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                <!-- right_navigation //-->
                <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
                <!-- right_navigation_eof //-->
                </table>
            </td>
        </tr>
    </table>
    <!-- body_eof //-->
    <?php //echo $text_display; ?>
    <!-- footer //-->
    <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
    <!-- footer_eof //-->
    <br>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>