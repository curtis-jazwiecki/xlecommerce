<?php

/*

  $Id: eProcessingNetwork.php,v 1.40 2002/11/25 18:23:14 dgw_ Exp $



  CloudCommerce - Multi-Channel eCommerce Solutions

  http://www.cloudcommerce.org

  Copyright (c) 2016 Outdoor Business Network, Inc.



  eProcessingNetwork.php was developed for eProcessingNetwork



  http://www.eProcessingNetwork.com



  by



  Julian Brown

  julian@jlbprof.com

*/



	class eProcessingNetwork

	{

		var $code, $title, $description, $enabled;



		// class constructor

		function eProcessingNetwork ()

		{

			$this->code = 'eProcessingNetwork';

			$this->title = MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_TITLE;

			$this->description = MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_DESCRIPTION;

			$this->enabled = ((MODULE_PAYMENT_EPROCESSINGNETWORK_STATUS == 'True') ?

			true : false);



			$this->eproc_gets_cc =

			((MODULE_PAYMENT_EPROCESSINGNETWORK_CC == 'True') ? true : false);



			$this->logo_url = MODULE_PAYMENT_EPROCESSINGNETWORK_LOGO_URL;

			$this->bck_color = MODULE_PAYMENT_EPROCESSINGNETWORK_BCK_COLOR;

			$this->text_color = MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_COLOR;



			$this->accept_visa = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_VISA;

			$this->accept_mc = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_MC;

			$this->accept_amex = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_AMEX;

			$this->accept_ds = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DS;

			$this->accept_dn = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DN;

			$this->accept_jcb = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_JCB;

			$this->accept_cb = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CB;

			$this->accept_ccfs = MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CCFS;





			if (strcmp ($this->accept_ccfs, "False") == 0)

				$this->accept_ccfs = 0;

			else

				$this->accept_ccfs = 1;





			// Choose a url depending on whether oscommerce is collecting the CC#

			// or if eProcessingNetwork is.



			if ($this->eproc_gets_cc)

			{

				$this->form_action_url =

					'https://www.eProcessingNetwork.com/cgi-bin/dbe/order.pl';

			}

			else

			{

				$this->form_action_url =

					tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL', false);

			}



			$this->log_fh = 0;



			#$this->logVARS ("init");

		}



		// class methods

		function javascript_validation()

		{



			// if eProcessing is collecting the CC#, then OSCommerce does not

			// allow the user to enter CC#, therefore we do not need to validate it



			if (!$this->eproc_gets_cc)

			{

				$cc_owner_len = CC_OWNER_MIN_LENGTH;

				$cc_number_len = CC_NUMBER_MIN_LENGTH;

				$err_cvv2_type = MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_TYPE_ERROR;

				$err_cc_owner = MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_JS_CC_OWNER;

				$err_cc_number = MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_JS_CC_NUMBER;



				$err_ccfs_routing = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING_ERROR;

				$err_ccfs_account = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT_ERROR;

				$err_ccfs_check = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK_ERROR;

				$err_ccfs_bankname = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME_ERROR;



				$epn_code = $this->code;



				$js = <<<EOT

	if (payment_value == "$epn_code")

	{

		var cc_owner_len = "$cc_owner_len";

		var cc_number_len = "$cc_number_len";

		var err_cvv2_type = "$err_cvv2_type";

		var err_cc_owner = "$err_cc_owner";

		var err_cc_number = "$err_cc_number";



		var payment_type_radios = document.checkout_payment.elements ['eprocessing_payment_type'];



		var bIsCheck;



		bIsCheck = false;

		var	iLen = payment_type_radios.length;



		if (iLen != undefined)

		{

			var		i;

			var		payment_type;



			for (i = 0; i < iLen; ++i)

			{

				payment_type = payment_type_radios [i];

				if (payment_type.checked &&

					payment_type.value == "Check")

				{

					bIsCheck = true;

					break;

				}

			}

		}

	

		if (bIsCheck)

		{

			var ccfs_account = document.checkout_payment.eprocessing_ccfs_account;

			var ccfs_routing = document.checkout_payment.eprocessing_ccfs_routing;

			var ccfs_check = document.checkout_payment.eprocessing_ccfs_check;

			var ccfs_bankname = document.checkout_payment.eprocessing_ccfs_bankname;



			var err_ccfs_account = "$err_ccfs_account";

			var err_ccfs_routing = "$err_ccfs_routing";

			var err_ccfs_check = "$err_ccfs_check";

			var err_ccfs_bankname= "$err_ccfs_bankname";



			if (ccfs_account.value.length < 1)

			{

				error_message = error_message + " " + err_ccfs_account;

				error = 1;

			}

			else if (ccfs_routing.value.length != 9)

			{

				error_message = error_message + " " + err_ccfs_routing;

				error = 1;

			}

			else if (ccfs_check.value.length < 1)

			{

				error_message = error_message + " " + err_ccfs_check;

				error = 1;

			}

			else if (ccfs_bankname.value.length < 1)

			{

				error_message = error_message + " " + err_ccfs_bankname;

				error = 1;

			}

		}

		else

		{

			var cvv2_type = document.checkout_payment.eprocessing_cc_cvv2_type;

			var cvv2 = document.checkout_payment.eprocessing_cc_cvv2.value;

			var cc_owner = document.checkout_payment.eprocessing_cc_owner.value;

			var cc_number = document.checkout_payment.eprocessing_cc_number.value;



			if (cc_owner == "" ||

				cc_owner.length < cc_owner_len)

			{

				error_message = error_message + " " + err_cc_owner;

				error = 1;

			}

			if (cc_number == "" ||

				cc_number.length < cc_number_len)

				{

					error_message = error_message + " " + err_cc_number;

					error = 1;

			}



			var cvv2_type_selected = cvv2_type.selectedIndex;

			var cvv2_type_id = cvv2_type.options [cvv2_type_selected].value;

			if (cvv2_type_id == 1 && cvv2.length <= 0)

			{

				error_message = error_message + " " + err_cvv2_type;

				error = 1;

			}

		}

	  }

EOT;

			}

			else

			{

				$js = '';

			}



			return $js;

		}



		function getValue ($field)

		{

			global $HTTP_POST_VARS;

			global $HTTP_GET_VARS;



			$value = $HTTP_GET_VARS [$field];



			if (strlen ($value))

				return $value;



			$value = $HTTP_POST_VARS [$field];



			if (strlen ($value))

				return $value;



			return "";

		}



		function update_defaults ($defaults, $field, $post_field)

		{

			$value = $this->getValue ($post_field);



			if (strlen ($value))

				$defaults [$field] = $value;



			return $defaults;

		}



		function selection()

		{

			global $HTTP_POST_VARS;

			global $order;



			for ($i=1; $i<13; $i++)

			{

				$expires_month[] = array(

					'id' => sprintf('%02d', $i),

					'text' => strftime('%B',mktime(0,0,0,$i,1,2000)));

			}



			$today = getdate(); 

			for ($i=$today['year']; $i < $today['year']+10; $i++)

			{

				$expires_year[] = array(

					'id' => strftime('%y',mktime(0,0,0,1,1,$i)),

					'text' => strftime('%Y',mktime(0,0,0,1,1,$i)));

			}



			$this_mon = sprintf ("%02d", ($today ['month'] + 1));

			$this_year = sprintf ("%02d", $today ['year'] - 2000);



			$cvv2_types [] = array (

				'id' => '1',

				'text' => MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_1);



			$cvv2_types [] = array (

				'id' => '0',

				'text' => MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_0);



			$cvv2_types [] = array (

				'id' => '2',

				'text' => MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_2);



			$cvv2_types [] = array (

				'id' => '9',

				'text' => MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_9);



			$cvv2_types_default = '1';



			// Depending on whether OSCommerce is collecting the CC# or not.  If

			// OSCommerce is collecting then we must allow the user to enter it,

			// here we output the form fields to collect the cc#



			if (!$this->eproc_gets_cc)

			{

				$card_logos = "";

				if (!strcmp ($this->accept_visa, "True"))

				{

					$card_logos = $card_logos . ' ' .

						tep_image (DIR_WS_IMAGES . 'ePN_VISALogo.gif', 'VISA Logo', 36, 24, '');

				}



				if (!strcmp ($this->accept_mc, "True"))

				{

					$card_logos = $card_logos . ' ' .

						tep_image (DIR_WS_IMAGES . 'ePN_MCLogo.gif', 'Mastercard Logo', 36, 24, '');

				}



				if (!strcmp ($this->accept_amex, "True"))

				{

					$card_logos = $card_logos . ' ' .

						tep_image (DIR_WS_IMAGES . 'ePN_AMEXLogo.gif',

						'American Express Logo', 36, 24, '');

				}



				if (!strcmp ($this->accept_ds, "True"))

				{

					$card_logos = $card_logos . ' ' .

						tep_image (DIR_WS_IMAGES . 'ePN_DiscoverLogo.gif',

						'Discover Logo', 36, 24, '');

				}



				if (!strcmp ($this->accept_dn, "True"))

				{

					$card_logos = $card_logos . ' ' .

						tep_image (DIR_WS_IMAGES . 'ePN_DinersLogo.gif',

						'Diners Club Logo', 36, 24, '');

				}



				if (!strcmp ($this->accept_jcb, "True"))

				{

					$card_logos = $card_logos . ' ' . tep_output_string ('JCB');

				}



				if (!strcmp ($this->accept_cb, "True"))

				{

					$card_logos = $card_logos . ' ' . tep_output_string ('Carte Blanche');

				}



				$cvv2_js = "javascript:window.open('cvv_help.php', 'infowin_cvv2', 'scrollbars=1,resizable=1,width=300,height=225,dependent=1');";



				$what_is_cvv2 = '<input type=button name=cvv2_help value="What is CVV2?" OnClick="' . $cvv2_js . '">';



				$accounttypes [] = array (

					'id' => 'C',

					'text' => 'Company');



				$accounttypes [] = array (

					'id' => 'P',

					'text' => 'Personal');



				$accountclasses [] = array (

					'id' => 'Checking',

					'text' => 'Checking');



				$accountclasses [] = array (

					'id' => 'Savings',

					'text' => 'Savings');



				$defaults = array ();



				$defaults['pt_credit'] = true;

				$defaults['pt_check'] = false;

				$defaults['cc_owner'] = $order->billing ['firstname'] .

					' ' .  $order->billing ['lastname'];

				$defaults['cc_number'] = '';

				$defaults['cvv2_types_default'] = $cvv2_types_default;

				$defaults['cvv2_default'] = '';



				$defaults['accounttypes_default'] = 'P';

				$defaults['accountclasses_default'] = 'Checking';

				$defaults['ccfs_routing'] = '';

				$defaults['ccfs_account'] = '';

				$defaults['ccfs_check'] = '';

				$defaults['ccfs_bankname'] = '';

				$defaults['ccfs_companyname'] = $order->billing ['company'];

				$defaults['ccfs_firstname'] = $order->billing ['firstname'];

				$defaults['ccfs_lastname'] = $order->billing ['lastname'];

				$defaults['ccfs_address'] = $order->billing ['street_address'];

				$defaults['ccfs_city'] = $order->billing ['city'];

				$defaults['ccfs_state'] = $order->billing ['state'];

				$defaults['ccfs_zip'] = $order->billing ['postcode'];

				$defaults['ccfs_phone'] = $order->customer ['telephone'];

				$defaults['this_mon'] = $this_mon;

				$defaults['this_year'] = $this_year;



				# override if any exists



				if (strlen ($HTTP_POST_VARS ['eprocessing_payment_type']))

				{

					if (!strcmp (

						$HTTP_POST_VARS ['eprocessing_payment_type'], "Check"))

					{

						$defaults ['pt_credit'] = false;

						$defaults ['pt_check'] = true;

					}

					else

					{

						$defaults ['pt_credit'] = true;

						$defaults ['pt_check'] = false;

					}

				}



				$defaults = $this->update_defaults ($defaults, 'cc_owner', 'eprocessing_cc_owner');

				$defaults = $this->update_defaults ($defaults, 'cc_number', 'eprocessing_cc_number');

				$defaults = $this->update_defaults ($defaults, 'this_mon', 'eprocessing_cc_expires_month');

				$defaults = $this->update_defaults ($defaults, 'this_year', 'eprocessing_cc_expires_year');

				$defaults = $this->update_defaults ($defaults, 'cvv2_default', 'eprocessing_cc_cvv2');

				$defaults = $this->update_defaults ($defaults, 'cvv2_types_default', 'eprocessing_cc_cvv2_type');

				$defaults = $this->update_defaults ($defaults, 'accounttypes_default', 'eprocessing_ccfs_accounttype');

				$defaults = $this->update_defaults ($defaults, 'accountclasses_default',

					'eprocessing_ccfs_accountclass');

				$defaults = $this->update_defaults ($defaults, 'ccfs_routing', 'eprocessing_ccfs_routing');

				$defaults = $this->update_defaults ($defaults, 'ccfs_account', 'eprocessing_ccfs_account');

				$defaults = $this->update_defaults ($defaults, 'ccfs_check', 'eprocessing_ccfs_check');

				$defaults = $this->update_defaults ($defaults, 'ccfs_bankname', 'eprocessing_ccfs_bankname');

				$defaults = $this->update_defaults ($defaults, 'ccfs_companyname', 'eprocessing_ccfs_companyname');

				$defaults = $this->update_defaults ($defaults, 'ccfs_firstname', 'eprocessing_ccfs_firstname');

				$defaults = $this->update_defaults ($defaults, 'ccfs_lastname', 'eprocessing_ccfs_lastname');

				$defaults = $this->update_defaults ($defaults, 'ccfs_address', 'eprocessing_ccfs_address');

				$defaults = $this->update_defaults ($defaults, 'ccfs_city', 'eprocessing_ccfs_city');

				$defaults = $this->update_defaults ($defaults, 'ccfs_state', 'eprocessing_ccfs_state');

				$defaults = $this->update_defaults ($defaults, 'ccfs_zip', 'eprocessing_ccfs_zip');

				$defaults = $this->update_defaults ($defaults, 'ccfs_phone', 'eprocessing_ccfs_phone');



				$wa = array(

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_TYPE,

						'field' => tep_draw_radio_field

						('eprocessing_payment_type', 'Credit', $defaults ['pt_credit']) .

						MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_CREDIT),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_OWNER,

						'field' => tep_draw_input_field(

						'eprocessing_cc_owner', $defaults ['cc_owner'])),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_CARDS_ACCEPTED,

						'field' => $card_logos),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_NUMBER,

						'field' => tep_draw_input_field('eprocessing_cc_number',

							$defaults ['cc_number'])),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_EXPIRES,

						'field' => tep_draw_pull_down_menu('eprocessing_cc_expires_month',

						$expires_month, $defaults ['this_mon']) . '&nbsp;' .

						tep_draw_pull_down_menu('eprocessing_cc_expires_year',

						$expires_year, $defaults ['this_year'])),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_TYPE,

						'field' => tep_draw_pull_down_menu('eprocessing_cc_cvv2_type',

						$cvv2_types, $defaults ['cvv2_types_default'])),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2, 

						'field' =>

						tep_draw_input_field('eprocessing_cc_cvv2',

						$defaults ['cvv2_default'])),

					array('title' =>

					MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_HELP,

						'field' => $what_is_cvv2));



				if ($this->accept_ccfs)

				{

					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_TYPE,

							'field' => tep_draw_radio_field

							('eprocessing_payment_type', 'Check', $defaults ['pt_check']) .

							MODULE_PAYMENT_EPROCESSINGNETWORK_PAYMENT_CHECK));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING,

							'field' => tep_draw_input_field(

								'eprocessing_ccfs_routing', $defaults ['ccfs_routing'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_account', $defaults ['ccfs_account'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_check', $defaults ['ccfs_check'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_HOWTO,

							'field' => tep_output_string (

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_HOWTO_TXT)));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_IMAGE,

							'field' =>

								tep_image (DIR_WS_IMAGES . 'ePN_Check.jpg',

								'Image of Check', 169, 87, '')));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNTTYPE,

							'field' => tep_draw_pull_down_menu (

							'eprocessing_ccfs_accounttype',

							$accounttypes, $defaults ['accounttypes_default'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNTCLASS,

							'field' => tep_draw_pull_down_menu (

							'eprocessing_ccfs_accountclass',

							$accountclasses, $defaults ['accountclasses_default'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_bankname', $defaults ['ccfs_bankname'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_COMPANYNAME,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_companyname',

							$defaults ['ccfs_companyname'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_FIRSTNAME,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_firstname',

							$defaults ['ccfs_firstname'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_LASTNAME,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_lastname',

							$defaults['ccfs_lastname'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ADDRESS,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_address',

							$defaults ['ccfs_address'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CITY,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_city',

							$defaults ['ccfs_city'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_STATE,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_state',

							$defaults ['ccfs_state'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ZIP,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_zip',

							$defaults ['ccfs_zip'])));



					array_push ($wa,

						array('title' =>

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_PHONE,

							'field' => tep_draw_input_field(

							'eprocessing_ccfs_phone',

							$defaults ['ccfs_phone'])));

				}



				$selection = array(

					'id' => $this->code,

					'module' => $this->title,

					'fields' => $wa);

			}

			else

			{

				$selection = array(

					'id' => $this->code,

					'module' => $this->title,

					'fields' => array(

						array('title' => 'Continue',

							  'field' => tep_draw_hidden_field('eprocessing_cc_owner', ''))));

			}



			return $selection;

		}



		function RoutingMod10 ($routing_num)

		{

			$r1 = substr ($routing_num, 0, 1);

			$r2 = substr ($routing_num, 1, 1);

			$r3 = substr ($routing_num, 2, 1);

			$r4 = substr ($routing_num, 3, 1);

			$r5 = substr ($routing_num, 4, 1);

			$r6 = substr ($routing_num, 5, 1);

			$r7 = substr ($routing_num, 6, 1);

			$r8 = substr ($routing_num, 7, 1);

			$r9 = substr ($routing_num, 8, 1);

			

			$checksum =

				($r1 * 3) +

				($r2 * 7) +

				($r3 * 1) +

				($r4 * 3) +

				($r5 * 7) +

				($r6 * 1) +

				($r7 * 3) +

				($r8 * 7);



			$checksum = $checksum % 10;

			$checksum = 10 - $checksum;



			if ($checksum == 10) { $checksum = 0; }



			if ($checksum != $r9) { return 0; }



			return (1);

		}



		function myLog ($msg)

		{

			return;



			if ($this->log_fh == 0)

			{

				$this->log_fname = "/tmp/jlb_log.txt";

#				unlink ($this->log_fname);



#				$this->log_fh = fopen ($this->log_fname, "wb");

				$this->log_fh = fopen ($this->log_fname, "ab");

			}



			fputs ($this->log_fh, $msg);

			fflush ($this->log_fh);

		}



		function logVARS ($msg)

		{

			global $HTTP_POST_VARS;

			global $HTTP_GET_VARS;



			$this->myLog ("VARS ($msg)\n");

			$this->myLog ("SHOW POST VARS\n");

			foreach ($HTTP_POST_VARS as $key => $value)

			{

				$this->myLog ("   KEY ($key) VALUE ($value)\n");

			}



			$this->myLog ("SHOW POST VARS DONE\n");



			$this->myLog ("SHOW GET VARS\n");

			foreach ($HTTP_GET_VARS as $key => $value)

			{

				$this->myLog ("   KEY ($key) VALUE ($value)\n");

			}



			$this->myLog ("SHOW GET VARS DONE\n");

		}



		function duplicate_epn_vars ()

		{

			global $HTTP_POST_VARS;



			$return_string = '';

			$lookfor = 'eprocessing_';

			$looklen = strlen ($lookfor);



			foreach ($HTTP_POST_VARS as $key => $value)

			{

				if (!strcmp (substr ($key, 0, $looklen), $lookfor))

				{

					$return_string = $return_string . '&' .

						$key . '=' . urlencode ($value);

				}

			}



			return $return_string;

		}



		function pre_confirmation_check(){

			global $HTTP_POST_VARS;



			// We don't confirm if OSCommerce is not collecting the CC#



			if (!$this->eproc_gets_cc)

			{

				$error = '';

				$result = true;



				$payment_type = $HTTP_POST_VARS ['eprocessing_payment_type'];



				if (!strcmp ($payment_type, "Check"))

				{

					$routing  = $HTTP_POST_VARS ['eprocessing_ccfs_routing'];

					$account  = $HTTP_POST_VARS ['eprocessing_ccfs_account'];

					$check    = $HTTP_POST_VARS ['eprocessing_ccfs_check'];

					$bankname = $HTTP_POST_VARS ['eprocessing_ccfs_bankname'];



					if (strlen ($account) < 1 ||

					    !(ctype_digit ($account)))

					{

						$error = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT_ERROR;

						$result = false;

					}



					if (strlen ($check) < 1 ||

					    !(ctype_digit ($check)))

					{

						$error =

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK_ERROR;

						$result = false;

					}



					if (strlen ($bankname) < 1)

					{

						$error =

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME_ERROR;

						$result = false;

					}



					if (strlen ($routing) != 9 ||

					    !(ctype_digit ($routing)) ||

						($this->RoutingMod10 ($routing)) == 0)

					{

						$error = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING_ERROR;

						$result = false;

					}

				}

				else

				{

					include(DIR_WS_CLASSES . 'cc_validation.php');



					$cc_validation = new cc_validation();

					$result = $cc_validation->validate(

						$HTTP_POST_VARS['eprocessing_cc_number'],

						$HTTP_POST_VARS['eprocessing_cc_expires_month'],

						$HTTP_POST_VARS['eprocessing_cc_expires_year']);



					$error = '';

					switch ($result)

					{

					case -1:

						$error = sprintf(

							TEXT_CCVAL_ERROR_UNKNOWN_CARD,

							substr($cc_validation->cc_number, 0, 4));

						break;

					case -2:

					case -3:

					case -4:

						$error = TEXT_CCVAL_ERROR_INVALID_DATE;

						break;

					case false:

						$error = TEXT_CCVAL_ERROR_INVALID_NUMBER;

						$result = false;

						break;

					}



					$this->cc_card_type = $cc_validation->cc_type;

					$this->cc_card_number = $cc_validation->cc_number;

					$this->cc_expiry_month = $cc_validation->cc_expiry_month;

					$this->cc_expiry_year = $cc_validation->cc_expiry_year;

					$this->cc_cvv2_type = $HTTP_POST_VARS['eprocessing_cc_cvv2_type'];

					$this->cc_cvv2 = $HTTP_POST_VARS['eprocessing_cc_cvv2'];

				}



				if (($result == false) || ($result < 1))

				{

					$payment_error_return =

						'payment_error=' . $this->code .

						'&error=' . urlencode($error) .

						$this->duplicate_epn_vars ();



					tep_redirect(tep_href_link(

						FILENAME_CHECKOUT_PAYMENT,

						$payment_error_return,

						'SSL',

						true,

						false));

				}

			}

		}

		

		function onepage_pre_confirmation_check(){

			global $HTTP_POST_VARS;



			// We don't confirm if OSCommerce is not collecting the CC#



			if (!$this->eproc_gets_cc)

			{

				$error = '';

				$result = true;



				$payment_type = $HTTP_POST_VARS ['eprocessing_payment_type'];



				if (!strcmp ($payment_type, "Check"))

				{

					$routing  = $HTTP_POST_VARS ['eprocessing_ccfs_routing'];

					$account  = $HTTP_POST_VARS ['eprocessing_ccfs_account'];

					$check    = $HTTP_POST_VARS ['eprocessing_ccfs_check'];

					$bankname = $HTTP_POST_VARS ['eprocessing_ccfs_bankname'];



					if (strlen ($account) < 1 ||

					    !(ctype_digit ($account)))

					{

						$error = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT_ERROR;

						$result = false;

					}



					if (strlen ($check) < 1 ||

					    !(ctype_digit ($check)))

					{

						$error =

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK_ERROR;

						$result = false;

					}



					if (strlen ($bankname) < 1)

					{

						$error =

						MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME_ERROR;

						$result = false;

					}



					if (strlen ($routing) != 9 ||

					    !(ctype_digit ($routing)) ||

						($this->RoutingMod10 ($routing)) == 0)

					{

						$error = MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING_ERROR;

						$result = false;

					}

				}

				else

				{

					include(DIR_WS_CLASSES . 'cc_validation.php');



					$cc_validation = new cc_validation();

					$result = $cc_validation->validate(

						$HTTP_POST_VARS['eprocessing_cc_number'],

						$HTTP_POST_VARS['eprocessing_cc_expires_month'],

						$HTTP_POST_VARS['eprocessing_cc_expires_year']);



					$error = '';

					switch ($result)

					{

					case -1:

						$error = sprintf(

							TEXT_CCVAL_ERROR_UNKNOWN_CARD,

							substr($cc_validation->cc_number, 0, 4));

						break;

					case -2:

					case -3:

					case -4:

						$error = TEXT_CCVAL_ERROR_INVALID_DATE;

						break;

					case false:

						$error = TEXT_CCVAL_ERROR_INVALID_NUMBER;

						$result = false;

						break;

					}



					$this->cc_card_type = $cc_validation->cc_type;

					$this->cc_card_number = $cc_validation->cc_number;

					$this->cc_expiry_month = $cc_validation->cc_expiry_month;

					$this->cc_expiry_year = $cc_validation->cc_expiry_year;

					$this->cc_cvv2_type = $HTTP_POST_VARS['eprocessing_cc_cvv2_type'];

					$this->cc_cvv2 = $HTTP_POST_VARS['eprocessing_cc_cvv2'];

				}



				if (($result == false) || ($result < 1)){

					$payment_error_return = 'payment_error=' . $this->code .'&error=' . urlencode($error) .$this->duplicate_epn_vars();

					echo urlencode($error);

				}else{

					echo "success";

				}

			}

		}



		function confirmation()

		{

			global $HTTP_POST_VARS;



			// don't confirm if OSCommerce is not collecting the CC#



			if (!$this->eproc_gets_cc)

			{

				$payment_type = $HTTP_POST_VARS ['eprocessing_payment_type'];



				if (!strcmp ($payment_type, "Check"))

				{

					$confirmation = array('title' => $this->title . ': ' .

						$this->cc_card_type, 'fields' =>

							array(array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_OWNER,

								'field' => $HTTP_POST_VARS['eprocessing_cc_owner']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ROUTING,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_routing']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ACCOUNT,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_account']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CHECK,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_check']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_BANKNAME,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_bankname']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_COMPANYNAME,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_companyname']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_FIRSTNAME,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_firstname']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_LASTNAME,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_lastname']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ADDRESS,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_address']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_CITY,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_city']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_STATE,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_state']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_ZIP,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_zip']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CCFS_PHONE,

								'field' =>

								$HTTP_POST_VARS['eprocessing_ccfs_phone'])

					));

				}

				else

				{

					$confirmation = array('title' => $this->title . ': ' .

						$this->cc_card_type, 'fields' =>

							array(array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_OWNER,

								'field' => $HTTP_POST_VARS['eprocessing_cc_owner']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_NUMBER,

								'field' => substr($this->cc_card_number, 0, 4) .

									str_repeat('X', (strlen($this->cc_card_number) - 8)) .

									substr($this->cc_card_number, -4)),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_CREDIT_CARD_EXPIRES,

								'field' => strftime('%B, %Y',

									mktime(0,0,0,

										$HTTP_POST_VARS['eprocessing_cc_expires_month'],

										1,

										'20' .

										$HTTP_POST_VARS['eprocessing_cc_expires_year']))),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2_TYPE,

								'field' => $HTTP_POST_VARS['eprocessing_cc_cvv2_type']),

							array('title' =>

								MODULE_PAYMENT_EPROCESSINGNETWORK_CVV2,

								'field' => $HTTP_POST_VARS['eprocessing_cc_cvv2'])

					));

				}

			}

			else

			{

				$confirmation = '';

			}



			return $confirmation;

		}



		function process_button()

		{

			global $HTTP_SERVER_VARS, $order, $customer_id;

			global $HTTP_POST_VARS;



			if ($this->eproc_gets_cc)

			{

				$process_button_string =

					tep_draw_hidden_field ('ePNAccount',

						MODULE_PAYMENT_EPROCESSINGNETWORK_LOGIN);



				// if OSCommerce collected the CC# then send it on to eProc



				if (strlen ($this->logo_url) > 6)

				{

					$process_button_string .=

						tep_draw_hidden_field ('LogoURL', $this->logo_url);

				}



				if (strlen ($this->bck_color) > 2)

				{

					$process_button_string .=

						tep_draw_hidden_field ('BackgroundColor',

							$this->bck_color);

				}



				if (strlen ($this->text_color) > 2)

				{

					$process_button_string .=

						tep_draw_hidden_field ('TextColor', $this->text_color);

				}



				$process_button_string .=

					tep_draw_hidden_field ('Total',

						$order->info['total']) .

					tep_draw_hidden_field('Address',

				$order->billing['street_address']) .

					tep_draw_hidden_field('Zip',

				$order->billing['postcode']) .

					tep_draw_hidden_field('City',

				$order->billing['city']) .

					tep_draw_hidden_field('State',

				$order->billing['state']) .

					tep_draw_hidden_field('EMail',

				$order->billing['email_address']) .

					tep_draw_hidden_field('ID', tep_session_id ()) .

					tep_draw_hidden_field('ApprovedURL',

					tep_href_link(FILENAME_CHECKOUT_PROCESS, '', 'SSL',

						false)) .

					tep_draw_hidden_field('DeclinedURL',

					tep_href_link('eprocfail.php', '', 'SSL', false));



				// now take care of some cosmetic issues



				$process_button_string .=

					tep_draw_hidden_field(

						tep_session_name(),

						tep_session_id()

					);

			}

			else

			{

				# We will use TDBE to do the work but process through the

				# oscommerce system.



				$process_button_string =

					tep_draw_hidden_field ('ePNAccount',

						MODULE_PAYMENT_EPROCESSINGNETWORK_LOGIN);

				$process_button_string .= "\n";



				$process_button_string .=

					tep_draw_hidden_field ('HTML', 'No');

				$process_button_string .= "\n";



				$process_button_string .=

					tep_draw_hidden_field ('Swiped', '0');

				$process_button_string .= "\n";



				$process_button_string .=

					tep_draw_hidden_field ('Inv', 'report');

				$process_button_string .= "\n";



				$process_button_string .=

					tep_draw_hidden_field ('Total',

						$order->info['total']);

				$process_button_string .= "\n";



				if (strlen (MODULE_PAYMENT_EPROCESSINGNETWORK_RESTRICT_KEY)

						> 0)

				{

					$process_button_string .=

						tep_draw_hidden_field ('RestrictKey',

							MODULE_PAYMENT_EPROCESSINGNETWORK_RESTRICT_KEY);

					$process_button_string .= "\n";

				}



				$payment_type = $this->getValue ('eprocessing_payment_type');

				if (!strcmp ($payment_type, "Check"))

				{

					$process_button_string .=

						tep_draw_hidden_field ('PaymentType', 'Check');

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Routing',

							$this->getValue ('eprocessing_ccfs_routing'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('CheckAcct',

							$this->getValue ('eprocessing_ccfs_account'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Check',

							$this->getValue ('eprocessing_ccfs_check'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('BankName',

							$this->getValue ('eprocessing_ccfs_bankname'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('AccountType',

							$this->getValue ('eprocessing_ccfs_accounttype'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('AccountClass',

							$this->getValue ('eprocessing_ccfs_accountclass'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Company',

							$this->getValue ('eprocessing_ccfs_companyname'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('FirstName',

							$this->getValue ('eprocessing_ccfs_firstname'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('LastName',

							$this->getValue ('eprocessing_ccfs_lastname'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Address',

							$this->getValue ('eprocessing_ccfs_address'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('City',

							$this->getValue ('eprocessing_ccfs_city'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('State',

							$this->getValue ('eprocessing_ccfs_state'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Zip',

							$this->getValue ('eprocessing_ccfs_zip'));

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('Phone',

							$this->getValue ('eprocessing_ccfs_phone'));

					$process_button_string .= "\n";

				}

				else

				{

					/*$process_button_string .=  //Commented out 6/28/2013 to test - no longer sending transtype - David Harris

						tep_draw_hidden_field ('TranType', 'AuthConvert');

					$process_button_string .= "\n";

*/

					$process_button_string .=

						tep_draw_hidden_field ('CardNo',

							$HTTP_POST_VARS['eprocessing_cc_number']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('ExpMonth',

							$HTTP_POST_VARS['eprocessing_cc_expires_month']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('ExpYear',

							$HTTP_POST_VARS['eprocessing_cc_expires_year']);

					$process_button_string .= "\n";



					//added 7/1/2013 pass CVV2 code - David Harris

					

					$process_button_string .=

						tep_draw_hidden_field ('CVV2Type',

							$HTTP_POST_VARS['eprocessing_cc_cvv2_type']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field ('CVV2',

							$HTTP_POST_VARS['eprocessing_cc_cvv2']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('FirstName',

							$order->billing['firstname']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('LastName',

							$order->billing['lastname']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('Address',

							$order->billing['street_address']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('Zip',

							$order->billing['postcode']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('City',

							$order->billing['city']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('State',

							$order->billing['state']);

					$process_button_string .= "\n";



					$process_button_string .=

						tep_draw_hidden_field('EMail',

							$order->customer['email_address']);

					$process_button_string .= "\n";

				}

			}



			return $process_button_string;

		}



		function before_process()

		{

			global $HTTP_POST_VARS;



			while (list($key,$value) = each($HTTP_POST_VARS))

			{

				$data .= $key . '=' .

					urlencode(preg_replace('/,/', '', $value)) . '&';

			}



			$data = substr($data, 0, -1);



			unset($response);

			$ch=curl_init(

				"https://www.eProcessingNetwork.Com/cgi-bin/tdbe/transact.pl"); // Fixed applied on this line



			// normal

			// POST

			// request



			curl_setopt($ch,CURLOPT_POST,1);

			curl_setopt($ch,CURLOPT_POSTFIELDS,$data);



			// set

			// response

			// to

			// return

			// as

			// variable



			curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);



			// trap

			// response

			// into

			// $response

			// var



			global $response;



			$response = curl_exec($ch);



			// close

			// cURL

			// transfer



			curl_close($ch);



			$x_response_code = substr ($response, 1, 1);

			$x_remainder = substr ($response, 2);

			$x_idx = strpos ($x_remainder, '"');

			$x_message = substr ($x_remainder, 0, $x_idx);



			if ($x_response_code != 'Y')

			{

				if ($x_response_code == 'U')

				{

					tep_redirect(

						tep_href_link(FILENAME_CHECKOUT_PAYMENT,

							'error_message=' .

							urlencode(MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_UNABLE_MESSAGE . '/' . $x_message),

							'SSL', true, false));

				}

				else if ($x_response_code == 'N')

				{

					tep_redirect(

						tep_href_link(FILENAME_CHECKOUT_PAYMENT,

							'error_message=' .

							urlencode(MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_DECLINED_MESSAGE . '/' . $x_message),

							'SSL', true, false));

				}	

			}

		}



		function after_process()

		{

			return false;

		}



		function get_error()

		{

			global $HTTP_GET_VARS;



			$error = array('title' => MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_ERROR, 'error' => stripslashes(urldecode($HTTP_GET_VARS['error'])));



			return $error;

		}



		function check()

		{

			if (!isset($this->_check))

			{

				$check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_PAYMENT_EPROCESSINGNETWORK_STATUS'");

				$this->_check = tep_db_num_rows($check_query);

			}



			return $this->_check;

		}



		function install()

		{

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable eProcessingNetwork Module', 'MODULE_PAYMENT_EPROCESSINGNETWORK_STATUS', 'True', 'Do you want to accept eProcessingNetwork payments?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable eProcessingNetwork To Collect CC#', 'MODULE_PAYMENT_EPROCESSINGNETWORK_CC', 'False', 'Do you want eProcessingNetwork to Collect the Credit Card Number??', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Login Username', 'MODULE_PAYMENT_EPROCESSINGNETWORK_LOGIN', 'testing', 'The login username used for the eProcessingNetwork service', '6', '0', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('URL for Store Logo', 'MODULE_PAYMENT_EPROCESSINGNETWORK_LOGO_URL', '', 'The URL to a logo to be used by eProcessing to display during transactions', '6', '0', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Background Color', 'MODULE_PAYMENT_EPROCESSINGNETWORK_BCK_COLOR', '#FFFFFF', 'The Background Color in Hex format:', '6', '0', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Text Color', 'MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_COLOR', '#000000', 'The Color of the Text in Hex format:', '6', '0', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept VISA', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_VISA', 'False', 'Do you accept VISA?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept MASTERCARD', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_MC', 'False', 'Do you accept MASTERCARD?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept AMERICAN EXPRESS', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_AMEX', 'False', 'Do you accept AMERICAN EXPRESS?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept DISCOVER', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DS', 'False', 'Do you accept DISCOVER?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept DINERS CLUB', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DN', 'False', 'Do you accept DINERS CLUB?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept JCB', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_JCB', 'False', 'Do you accept JCB?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept CARTE BLANCHE?', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CB', 'False', 'Do you accept CARTE BLANCHE?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Restrict Key:', 'MODULE_PAYMENT_EPROCESSINGNETWORK_RESTRICT_KEY', '', 'If your account uses an ePN Restrict Key enter it here.', '6', '0', now())");

			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Do you accept CCFS', 'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CCFS', 'False', 'Do you accept Checks with CCFS?', '6', '0', 'tep_cfg_select_option(array(\'True\', \'False\'), ', now())");

		}



		function remove()

		{

			tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");

		}



		function keys()

		{

			return array(

				'MODULE_PAYMENT_EPROCESSINGNETWORK_STATUS',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_LOGIN',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_CC',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_LOGO_URL',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_BCK_COLOR',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_TEXT_COLOR',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_VISA',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_MC',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_AMEX',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DS',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_DN',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_JCB',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CB',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_RESTRICT_KEY',

				'MODULE_PAYMENT_EPROCESSINGNETWORK_ACCEPT_CCFS'

			);

		}



		function update_status ()

		{

		}

	}

?>

