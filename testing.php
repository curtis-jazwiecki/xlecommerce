<?php require('includes/application_top.php');
include(DIR_WS_CLASSES . 'ctctWrapper.php');
$breadcrumb->add('Lists', tep_href_link('ctct_lists.php'));

$action = $_GET['action'];
switch ($action){
	case 'add_update_contact':
		$lists_d = $_POST['lists'];
		$email_d = $_POST['email'];
		$fname_d = $_POST['fname'];
		$lname_d = $_POST['lname'];
		
		$params = array('email_address' => $email_d, 
						'first_name'	=> $fname_d, 
						'last_name'		=> $lname_d, 
						'lists'			=> $lists_d);
		$contact_obj = new Contact($params);
		
		$contacts_collection_obj = new ContactsCollection();
		$resp = $contacts_collection_obj->searchByEmail($email_d);
		if ($resp){
			$status = $contacts_collection_obj->updateContact($resp[0][0], $contact_obj);
		} else {
			$status = $contacts_collection_obj->createContact($contact_obj);
		}
		break;
}

$lists = array();
$sql = tep_db_query("select * from ctct_lists where list_name not in ('Active', 'Do Not Mail', 'Removed') order by list_sort_order, list_name");
while($entry = tep_db_fetch_array($sql)){
	$lists[] = $entry;
}

$fname = '';
$lname = '';
$email = '';
if (!empty($customer_id)){
	$sql = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from customers where customers_id='" . $customer_id . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		$fname = $info['customers_firstname'];
		$lname = $info['customers_lastname'];
		$email = $info['customers_email_address'];
	}
} 
?>
<!DOCTYPE html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
<title><?php echo 'Lists'; ?></title>
<?php
// Begin Template Check
$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_TEMPLATE_FOLDER'");
$check = tep_db_fetch_array($check_query);
echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/'.$check['configuration_value'].'/stylesheet.css">';
// End Template Check
?>
<title><?php echo 'Newsletters'; ?></title>
<script language="javascript" src="includes/general.js"></script>
<link type="text/css" href="jquery/css/smoothness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.6.custom.min.js"></script>
</head>
<body style="margin:0;">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<script type="text/javascript">
	jQuery(function(){
		jQuery( "#tabs" ).tabs();
	});
</script>
<!-- header_eof //-->
<table><tr><td>
      					<div>
      					<div id="tabs">
      						<ul>
      							<li><a href="#tabs-1">Add/Update</a></li>
      							<li><a href="#tabs-2">Delete</a></li>
      						</ul>
      						<div id="tabs-1">
		    					<form name="form_list" method="post" action="ctct_lists.php?action=add_update_contact">
		      					<table cellpadding="0" cellspacing="10" border="0">
		      						<tr>
		      							<td class="main" valign="top">List(s)*</td>
		      							<td class="main" valign="top">:&nbsp;</td>
		      							<td class="main" valign="top">
		      								<table cellpadding="0" cellspacing="5" border="0">
		      								<?php foreach($lists as $list){ ?>
		      									<tr>
		      										<td class="main" valign="top">
														<input type="checkbox" name="lists[]" value="<?php echo $list['ctct_list_id']; ?>" <?php echo ($list['list_is_default'] ? ' checked ' : '') ?> />
													</td>
		      										<td class="main" valign="top">
														<?php echo $list['list_name']; ?>
													</td>
		      									</tr>
		      								<?php } ?>
											</table>
										</td>
		      						</tr>
		      						<tr>
		      							<td class="main" valign="top">Email*</td>
		      							<td class="main" valign="top">:&nbsp;</td>
		      							<td class="main" valign="top">
		      								<input type="text" name="email" value="<?php echo $email; ?>" />
										</td>
		      						</tr>
		      						<tr>
		      							<td class="main" valign="top">First name</td>
		      							<td class="main" valign="top">:&nbsp;</td>
		      							<td class="main" valign="top">
		      								<input type="text" name="fname" value="<?php echo $fname; ?>" />
										</td>
		      						</tr>
		      						<tr>
		      							<td class="main" valign="top">Last name</td>
		      							<td class="main" valign="top">:&nbsp;</td>
		      							<td class="main" valign="top">
		      								<input type="text" name="lname" value="<?php echo $lname; ?>" />
										</td>
		      						</tr>
		      						<tr>
		      							<td class="main" valign="top" colspan="3">
		      								<input type="reset" />
		      								<div style="display:inline;float:right;"><input type="submit" value="Register"/></div>
										</td>
		      						</tr>
		      					</table>
		      					</form>
							</div>
							<div id="tabs-2">
								Delete contact
							</div>
						</div>
						</div>
						</td></tr></table>
</body>
</html>
