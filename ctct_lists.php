<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');
include(DIR_WS_CLASSES . 'ctctWrapper.php');
$breadcrumb->add('Lists', tep_href_link('ctct_lists.php'));

$action = $_GET['action'];
$reg_lists = array();
$ack = '';
switch ($action){
	case 'add_update_contact':
	case 'modify_contact':
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
			//$resp[0][0]->setStatus('Active');
			$status = $contacts_collection_obj->updateContact($resp[0][0], $contact_obj);
			if ($status=='204'){
				$messageStack->add_session('cc_lists', 'Subscription list(s) for email: ' . $email_d . ' updated [status:' . $status . ']', 'success');
			} else {
				$messageStack->add_session('cc_lists', 'Error occured while updating subscription list(s) for email: ' . $email_d . ' [status:' . $status . ']', 'error');
			}
		} else {
			$status = $contacts_collection_obj->createContact($contact_obj);
			if ($status=='204'){
				$messageStack->add_session('cc_lists', 'Subscription list(s) for email: ' . $email_d . ' registered [status:' . $status . ']', 'success');
			} else {
				$messageStack->add_session('cc_lists', 'Error occured while registering subscription list(s) for email: ' . $email_d . ' [status:' . $status . ']', 'error');
			}
		}
		tep_redirect('ctct_lists.php');
		break;
	case 'remove_contact':
		$email_d = $_POST['email'];
		
		$contacts_collection_obj = new ContactsCollection();
		$resp = $contacts_collection_obj->searchByEmail($email_d);
		if ($resp){
			$status = $contacts_collection_obj->removeContact($resp[0][0]);
			if ($status=='204'){
				$messageStack->add_session('cc_lists', 'Email: ' . $email_d . ' removed from subscription [status:' . $status . ']', 'success');
			} else {
				$messageStack->add_session('cc_lists', 'Error occured while removing email: ' . $email_d . ' [status:' . $status . ']', 'error');
			}
		} else {
			$messageStack->add_session('cc_lists', 'Error occured while removing email: ' . $email_d . ' [status:' . $status . ']', 'error');
		}
		tep_redirect('ctct_lists.php');
		break;
	case 'get_lists':
		$email_d = $_POST['email'];
		
		$contacts_collection_obj = new ContactsCollection();
		$resp = @$contacts_collection_obj->searchByEmail($email_d);
		if ($resp){
			$info = $contacts_collection_obj->listContactDetails($resp[0][0]);
			$reg_lists = $info->getLists();
			$reg_firstname = $info->getFirstName();
			$reg_lastname = $info->getLastName();
		} else {
			$messageStack->add_session('cc_lists', 'Error occured while checking email: ' . $email_d . ' for modification', 'error');
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
$msg_not_subscribed = '';
if (!empty($customer_id)){
	$sql = tep_db_query("select customers_firstname, customers_lastname, customers_email_address from customers where customers_id='" . $customer_id . "'");
	if (tep_db_num_rows($sql)){
		$info = tep_db_fetch_array($sql);
		$fname = $info['customers_firstname'];
		$lname = $info['customers_lastname'];
		$email = $info['customers_email_address'];
	}
	//$contacts_collection_obj = new ContactsCollection();
	//$resp = $contacts_collection_obj->searchByEmail('test02@focusindia.com');
	//$info = $contacts_collection_obj->listContactDetails($resp[0][0]);
	//print_r($info->getLists());
	//exit();
	if (!$resp || $resp[0][0]->getStatus()=='Do Not Mail'){
		$msg_not_subscribed = 'You have not yet subscribed for Lists. Please click on "Subscribe" for registering';
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
<script language="javascript" src="includes/general.js"></script>
<?php /**/ ?>
<link type="text/css" href="jquery/css/smoothness/jquery-ui-1.8.6.custom.css" rel="stylesheet" />
<script type="text/javascript" src="jquery/js/jquery-1.4.2.min.js"></script>
<script type="text/javascript" src="jquery/js/jquery-ui-1.8.6.custom.min.js"></script>
<?php /**/ ?>
<script type="text/javascript">
	$(document).ready(function(){
		var speed = 'slow';
		var headings = $("div#top div[id*='top_'] h1");
		headings.css('cursor', 'pointer');
		$("div#top div[id*='top_'] div#content").hide(speed, function(){});
		if (location.search.indexOf('get_lists')!=-1){
			$("div#top div[id='top_modify'] div#content").show(speed, function(){
				$("div#top div#top_modify div#content div#panel_1").css('display', 'none');
				$("div#top div#top_modify div#content div#panel_2").css('display', 'block');
			});
		}
		headings.click(function(){
			$("div#top div[id*='top_'] div#content").hide(speed, function(){});
			var current_heading = $(this);
			var heading_id = current_heading.attr('id');
			if (heading_id=='head_modify'){
				$("div#top div#top_modify div#content div#panel_1").css('display', 'block');
				$("div#top div#top_modify div#content div#panel_2").css('display', 'none');
			}
			$('#' + current_heading.attr('id') + ' ~ #content').show(speed, function(){});
			
		});
	});
</script>
</head>
<body style="margin:0;">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->
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
			<table border="0" width="100%" cellspacing="0" cellpadding="0">
      			<tr>
        			<td>
						<table border="0" width="100%" cellspacing="0" cellpadding="0">
          					<tr>
            					<td class="pageHeading"><?php echo 'Lists'; ?></td>
            					<td class="pageHeading" align="right"><?php //echo tep_image(DIR_WS_IMAGES . '', HEADING_TITLE_1, HEADING_IMAGE_WIDTH, HEADING_IMAGE_HEIGHT); ?></td>
          					</tr>
        				</table>
					</td>
      			</tr>
      			<tr>
        			<td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
      			</tr>
      			<tr>
      				<td>
      					<?php if ($messageStack->size('cc_lists')){ ?>
      					<table cellpadding="0" cellspacing="10" border="0">
      						<tr>
      							<td class="main" valign="top" style="font-weight:bold;" align="center">
									<?php 
										echo $messageStack->output('cc_lists');
										$messageStack->reset(); 
									?>
								</td>
      						</tr>
      					</table>
      					<?php } ?>
      					<?php if (!empty($msg_not_subscribed)){ ?>
      					<div class="main" style="font-weight:bolder"><?php echo $msg_not_subscribed; ?></div>
      					<?php } ?>
      					<div id="top">
      						<div id="top_subscribe">
      							<h1 id="head_subscribe" title="Subscribe Lists">Subscribe</h1>
      							<div id="content">
			    					<form name="form_subscribe" method="post" action="ctct_lists.php?action=add_update_contact">
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
							</div>
      						<?php /**/ ?><div id="top_modify">
      							<h1 id="head_modify" title="Modify Lists">Modify</h1>
      							<div id="content">
      								<div id="panel_1" style="display:block;">
      								<form name="form_email_01" method="post" action="ctct_lists.php?action=get_lists">
      								<table cellpadding="0" cellspacing="10" border="0">
			      						<tr>
			      							<td class="main" valign="top">Email*</td>
			      							<td class="main" valign="top">:&nbsp;</td>
			      							<td class="main" valign="top">
			      								<input type="text" name="email" value="" />
											</td>
			      						</tr>
			      						<tr>
			      							<td class="main" valign="top" colspan="3">
			      								<div style="display:inline;float:right;"><input type="submit" value="Continue"/></div>
											</td>
			      						</tr>
			      					</table>
									</form>
									</div>
									<div id="panel_2" style="display:block;">
			    					<form name="form_update" method="post" action="ctct_lists.php?action=modify_contact">
			      					<table cellpadding="0" cellspacing="10" border="0">
			      						<tr>
			      							<td class="main" valign="top">List(s)*</td>
			      							<td class="main" valign="top">:&nbsp;</td>
			      							<td class="main" valign="top">
			      								<table cellpadding="0" cellspacing="5" border="0">
			      								<?php foreach($lists as $list){ ?>
			      									<tr>
			      										<td class="main" valign="top">
															<input type="checkbox" name="lists[]" value="<?php echo $list['ctct_list_id']; ?>" <?php echo (in_array($list['ctct_list_id'], $reg_lists) ? ' checked ' : '') ?> />
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
			      								<input type="text" name="email" value="<?php echo $email_d; ?>" />
											</td>
			      						</tr>
			      						<tr>
			      							<td class="main" valign="top">First name</td>
			      							<td class="main" valign="top">:&nbsp;</td>
			      							<td class="main" valign="top">
			      								<input type="text" name="fname" value="<?php echo $reg_firstname; ?>" />
											</td>
			      						</tr>
			      						<tr>
			      							<td class="main" valign="top">Last name</td>
			      							<td class="main" valign="top">:&nbsp;</td>
			      							<td class="main" valign="top">
			      								<input type="text" name="lname" value="<?php echo $reg_lastname; ?>" />
											</td>
			      						</tr>
			      						<tr>
			      							<td class="main" valign="top" colspan="3">
			      								<input type="reset" />
			      								<div style="display:inline;float:right;"><input type="submit" value="Modify"/></div>
											</td>
			      						</tr>
			      					</table>
			      					</form>
			      					</div>
								</div>
							</div><?php /**/ ?>
      						<div id="top_remove">
      							<h1 id="head_remove" title="Remove from Lists">Remove</h1>
      							<div id="content">
      								<form name="form_email_02" method="post" action="ctct_lists.php?action=remove_contact">
      								<table cellpadding="0" cellspacing="10" border="0">
			      						<tr>
			      							<td class="main" valign="top">Email*</td>
			      							<td class="main" valign="top">:&nbsp;</td>
			      							<td class="main" valign="top">
			      								<input type="text" name="email" value="" />
											</td>
			      						</tr>
			      						<tr>
			      							<td class="main" valign="top" colspan="3">
			      								<input type="reset" />
			      								<div style="display:inline;float:right;"><input type="submit" value="Remove"/></div>
											</td>
			      						</tr>
			      					</table>
									</form>
								</div>
							</div>
			  			</div>
      				<?php /* ?>
					<script type="text/javascript">
						var j = jQuery.noConflict();
						j(function($){
							$( "#tabs" ).tabs();
						});
					</script>
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
						<?php */ ?>
    					<?php /*if (empty($action)){ ?>
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
												<?php echo $list['list_name'] . ($list['list_is_default'] ? ' (recommended)' : '');  ?>
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
      					<?php } elseif (!empty($ack)){ ?>
      					<table cellpadding="0" cellspacing="10" border="0">
      						<tr>
      							<td class="main" valign="top" style="font-weight:bold;" align="center">
									<?php echo $ack; ?>
									<br /><br />
									<input type="button" value="Lists Main Page" onclick="location.href='ctct_lists.php'" />
								</td>
      						</tr>
      					</table>
      					<?php }*/ ?>
      				</td>
      			</tr>
      		</table>
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
<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br/>
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>
