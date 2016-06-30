<?php
/*
  $Id: ffl_dealers.php,v 1.65 2016/03/22 23:03:52 hpdl Exp $
  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Copyright (c) 2016 osCommerce
  Released under the GNU General Public License
*/

  require('includes/application_top.php');
  
  if (ONEPAGE_CHECKOUT_ENABLED != 'True') {
  
	  if (!tep_session_is_registered('customer_id')) {
		echo '<script>window.opener.location.reload();</script>';
		echo '<script>self.close();</script>';
	  }
	  
	  if ($cart->count_contents() < 1){
		echo '<script>window.opener.location.reload();</script>';
		echo '<script>self.close();</script>';
	  }
  }
  
  $vendors_id = (int)$_GET['vendors_id'];
  
  
  if($_POST['action'] == 'setDealersDetails'){
  	$_SESSION['ffl_selected'][$_POST['datay']] = $_POST['datax'];
	echo "success";
	die();
  }
  
  if($_POST['action'] == 'removeDealersDetails'){
	unset($_SESSION['ffl_selected'][$_POST['datay']]);
	echo "success";
	die();
  }
  
  if($_POST['action'] == 'getDealersDetails'){
  	
	$ffl_dealer_details = tep_db_query("select * from ffl_dealers_data where ffl_dealers_data_id = '".tep_db_prepare_input($_POST['data'])."'");
	
	if(tep_db_num_rows($ffl_dealer_details)){
		$ffl_data = tep_db_fetch_array($ffl_dealer_details);
		$ffl_data_string = '<ul>';
		$ffl_data_string .= '<li><b>licensee Name:</b> '.$ffl_data['license_name'].'</li>';
		$ffl_data_string .= '<li><b>Phone Number:</b> '.$ffl_data['voice_phone'].'</li>';
		$ffl_data_string .= '<li><b>Street Address:</b> '.$ffl_data['premise_street'].'</li>';
		$ffl_data_string .= '<li><b>City:</b> '.$ffl_data['premise_city'].'</li>';
		$ffl_data_string .= '<li><b>State:</b> '.$ffl_data['premise_state'].'</li>';
		$ffl_data_string .= '<li><b>Zip:</b> '.$ffl_data['premise_zip_code'].'</li>';
		$ffl_data_string .= '</ul>';
		$ffl_data_string .= '<p align="center" id="btn_select_'.$_POST['datay'].'"><input type="button" name="btn-select-licensee" id="" value="Select Licensee" onclick="selectLicensee('.$ffl_data['ffl_dealers_data_id'].','.$_POST['datay'].');"></p>';
		echo $ffl_data_string;
	}else{
		echo '-1';
	}
	
	die();
  }
  
  
  if($_POST['mode'] == 'getDealersList'){
	  
		$latN = '';
		$latS = '';
		$lonE = '';
		$lonW = '';
		$json_array = array();
	  
	  $center_lat_long = tep_db_query("SELECT * FROM  zip_csv WHERE zip_code = '".(int)$_POST['premise_zip_code']."'");
	  if(tep_db_num_rows($center_lat_long)){
			$row  = tep_db_fetch_array($center_lat_long);
			$lat1 = $row['latitude'];
			$lon1 = $row['longitude'];
			$d    = $_POST['under_distance'];
			$r    = 3959;
			
			//compute max and min latitudes / longitudes for search square
			$latN = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(0))));
			$latS = rad2deg(asin(sin(deg2rad($lat1)) * cos($d / $r) + cos(deg2rad($lat1)) * sin($d / $r) * cos(deg2rad(180))));
			$lonE = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(90)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
			$lonW = rad2deg(deg2rad($lon1) + atan2(sin(deg2rad(270)) * sin($d / $r) * cos(deg2rad($lat1)), cos($d / $r) - sin(deg2rad($lat1)) * sin(deg2rad($latN))));
	  
	 $lat_long_array = array();
	  $cond = '';
	  if($latN != '' && $latS != '' && $lonE != '' && $lonW != ''){
		  $cond = " and (latitude <= '".$latN."' AND latitude >= '".$latS."' AND longitude <= '".$lonE."' AND longitude >= '".$lonW."') && (latitude <> '0.000000' AND longitude <> '0.000000') ";
	  }
	  
	  
	   $ffl_dealers_query = tep_db_query("select ffl_data.ffl_dealers_data_id,ffl_data.license_name,ffl_data.latitude,ffl_data.longitude,ffl_data.premise_street,ffl_data.premise_city,ffl_data.premise_zip_code,ffl_data.premise_state from ffl_dealers_data as ffl_data join ffl_dealers_docs as ffl_doc USING(ffl_dealers_docs_id) where ffl_doc.vendors_id = '".tep_db_prepare_input($_POST['vendors_id'])."' $cond ");
	  
	  
	  
	 
	 if(tep_db_num_rows($ffl_dealers_query)){
	 	//$ffl_dealer_string = '<ul>';
		while($ffl_dealers = tep_db_fetch_array($ffl_dealers_query)){
			
			//$lat_long_array[$ffl_dealers['ffl_dealers_data_id']] = $ffl_dealers['latitude'].'@'.$ffl_dealers['longitude'];
			
			//$ffl_dealer_string .= '<li style="cursor:pointer;" onclick="showFFLDealerDetails('.$ffl_dealers['ffl_dealers_data_id'].','.(int)$_POST['vendors_id'].');">'.$ffl_dealers['license_name'].'</li>';
			
			$json_array[] = array(
				"ffl_dealers_data_id"	=>$ffl_dealers['ffl_dealers_data_id'],
				"latitude"				=>$ffl_dealers['latitude'],
				"longitude"				=>$ffl_dealers['longitude'],
				"license_name"			=>$ffl_dealers['license_name'],
				"vendors_id"			=>(int)$_POST['vendors_id'],
				"premise_street"		=>$ffl_dealers['premise_street'],
				"premise_city"			=>$ffl_dealers['premise_city'],
				"premise_zip_code"		=>$ffl_dealers['premise_zip_code'],
				"premise_state"			=>$ffl_dealers['premise_state'],
			);
			
			
		
		}
	 	//$ffl_dealer_string .= '</ul>';
		
		//$hidden_field = '<input type="hidden" name="lat_long" id="lat_long" value="'.implode("XX",$lat_long_array).'">';
		
		echo json_encode($json_array);
	 
	 }else{
	 	
		$json_array = array(
				"error"	=> "failed"
		); // no records found
		
		echo json_encode($json_array);
	 }
	 
	
	  
	  
	  
	  }else{
	 	
		$json_array = array(
				"error"	=> "failed"
		); // no records found
		
		echo json_encode($json_array);
	 }
	  
	  
	  
	  
	 
	 die();
  }
  
  if(!isset($_GET['vendors_id'])){
	  die(); // no direct access
  }
  
  
  

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Select FFL Dealers</title>
<link rel="stylesheet" type="text/css" href="<?php echo STS_TEMPLATE_DIR; ?>stylesheet.css" />
</head>
<body>
<table cellpadding="0" cellspacing="0" class="ffl_table">
<input type="hidden" name="mode" id="mode" value="getDealersList" />
<input type="hidden" name="vendors_id" id="vendors_id" value="<?php echo $vendors_id; ?>" />
<tr>
	<th>Zipcode</th>
    <th>Max Distance</th>
    <th>Sort By</th>
    <th>&nbsp;</th>
</tr>
<tr>
	<th>
    	<input type="text" name="premise_zip_code" id="premise_zip_code" value="" />
    </th>
    <th>
    	<select name="under_distance" id="under_distance">
            <option value="5">5 miles</option>
            <option value="15">15 miles</option>
            <option value="25">25 miles</option>
            <option value="50">50 miles</option>
            <option value="100">100 miles</option>
		</select>
    </th>
    <th>
    	<select name="sortby" id="sortby">
          <option value="1">Nearest</option>
        </select>
	</th>
    <th><input type="button" name="filter" value="Find Dealers" onclick="getDealers();" /></th>
</tr>
</table>

<div class="ffl_table_left" id="ffl_dealers_list"></div>


<div class="ffl_table_right" id="ffl_dealers_details"></div>


<!-- container for google map #start -->
<div class="clear"></div>
<div class="center"> 
  <!-- Map Placeholder -->
  <div id="myMap"></div>
</div>
<!-- container for google map #ends -->


<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
<script type="text/javascript">
function getDealers(){
	
	 jQuery.ajax({
		url: 'ffl_dealers.php',
		type: 'post',
		data: jQuery('input[name="premise_zip_code"], input[name="vendors_id"] , input[name="mode"], select[name="sortby"], select[name="under_distance"]'),
		dataType: 'json',
		beforeSend: function() {
			jQuery('#ffl_dealers_list').html('');
			jQuery('#ffl_dealers_details').html('');
			jQuery('#ffl_dealers_list').html('<img src="images/ajax_loader.gif" alt="Please Wait....." title="Please Wait..." align="absmiddle">&nbsp; Fetching FFL Dealers List!');
        },
		success: function(json) {
			if (json['error']) {
				jQuery('#ffl_dealers_list').hide().fadeIn('slow').html('<p align="center" class="ffl_dealers_color">No data found...</p>');
			}else{
				var html =  '<ul>';
				jQuery.map(json, function(item) {
					
					html += '<li onclick="showFFLDealerDetails('+item['ffl_dealers_data_id']+','+item['vendors_id']+');" class="is_selected ffl_sele-color" id="ffl_licensee_li_'+item['ffl_dealers_data_id']+'">'+item['license_name']+'</li>';	
				
				});
				html +=  '</ul>';
				
				jQuery('#ffl_dealers_list').hide().fadeIn('slow').html(html);
				loadMap(json);
				
			}
		}
	});
}

var fflDealerDetailsHtml = '';
function showFFLDealerDetails(x,y){
	
	jQuery.ajax({
		url: 'ffl_dealers.php',
		type: 'post',
		data: "action=getDealersDetails&data="+x+"&datay="+y,
		dataType: 'html',
		beforeSend: function() {
			jQuery('#ffl_dealers_details').html('');
            jQuery('#ffl_dealers_details').html('<img src="images/ajax_loader.gif" alt="Please Wait....." title="Please Wait..." align="absmiddle">&nbsp; Fetching FFL Dealers Details!');
			fflDealerDetailsHtml = '';
			jQuery('.is_selected').removeClass('ffl_selected');
			jQuery('#ffl_licensee_li_'+x).addClass('ffl_selected');
			
			
        },
		success: function(response) {
			if(response == -1){
				jQuery('#ffl_dealers_details').hide().fadeIn('slow').html('<p align="center" class="ffl_dealers_color">No data found...</p>');
			}else{
				jQuery('#ffl_dealers_details').hide().fadeIn('slow').html(response);
				fflDealerDetailsHtml = response;
				showMarker(x);
			}
		}
	});
}

function selectLicensee(x,y){
	jQuery.ajax({
		url: 'ffl_dealers.php',
		type: 'post',
		data: "action=setDealersDetails&datax="+x+"&datay="+y,
		dataType: 'html',
		beforeSend: function() {
			jQuery('#ffl_dealers_details').html('<p align="center"><img src="images/ajax_loader.gif" alt="Please Wait....." title="Please Wait..." align="absmiddle"></p>');
			jQuery('#ffl_dealers_list').html('<p align="center"><img src="images/ajax_loader.gif" alt="Please Wait....." title="Please Wait..." align="absmiddle"></p>');
        },
		success: function(response) {
			if(response == -1){
				// error 
				alert("Error:: Unable to select!");
			}else{
				alert("Cart updated Successfully..");
				jQuery('#ffl_dealers_html_'+y, window.parent.document).hide('slow');
				jQuery('#selected_ffl_'+y, window.parent.document).html('<br>'+fflDealerDetailsHtml);
				jQuery('#btn_select_'+y, window.parent.document).hide('fast');
				jQuery('#selected_ffl_'+y, window.parent.document).append('<br> <input type="button" onclick="showfflData('+y+')" value="edit">');
				
			}
		}
	});
}
</script>

<!-- Include google map lib -->
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyAO97IpOYq_INt2cFSdZdCbPM3vUa9n5Bw&sensor=false"></script>
<style type="text/css"> 
	label { font-size: large; display: block; }
	#myMap{width: 94%; height:400px;margin-top:10px;}
	.center { width:100%} 
	.clear {float: none; clear:both;}
</style>

<script type="text/javascript"> 
        
	var newDate = new Date;				
	
	// set default map properties
	var defaultLatlng = new google.maps.LatLng(37.09024, -95.712891);
	
	// zoom level of the map		
	var defaultZoom = 4;
	
	// variable for map
	var map;
	
	// variable for marker info window
	var infowindow;
 
	// List with all marker to check if exist
	var markerList = {};
 
	// set error handler for jQuery AJAX requests
	jQuery.ajaxSetup({"error":function(XMLHttpRequest,textStatus, errorThrown) {   
		alert(textStatus + ' / ' + errorThrown + ' / ' + XMLHttpRequest.responseText);
	}});

	// option for google map object
	var myOptions = {
		zoom: defaultZoom,
		center: defaultLatlng,
		mapTypeId: google.maps.MapTypeId.HYBRID
	};


	/**
	 * Load Map
	 */
	function loadMap(json){

		console.log('loadMap');

		// create new map make sure a DIV with id myMap exist on page
		map = new google.maps.Map(document.getElementById("myMap"), myOptions);

		// create new info window for marker detail pop-up
		infowindow = new google.maps.InfoWindow();
		
		// create array of lat long
		
		jQuery.map(json, function(item) {
					
			var item_data = [];
			
			item_data['id'] = item['ffl_dealers_data_id'];
			item_data['lat'] = item['latitude'];
			item_data['long'] = item['longitude'];
			item_data['creator'] = item['license_name'];
			item_data['premise_street'] = item['premise_street'];
			item_data['premise_zip_code'] = item['premise_zip_code'];
			item_data['premise_city'] = item['premise_city'];
			item_data['premise_state'] = item['premise_state'];
			
			loadMarker(item_data);
			
				
				
		});
		
	}
 
 
	/**
	 * Load marker to map
	 */
	function loadMarker(markerData){
	
		// get date
		var mDate = new Date( markerData['created']*1000 );
	
		// create new marker location
		var myLatlng = new google.maps.LatLng(markerData['lat'],markerData['long']);

		// create new marker				
		var marker = new google.maps.Marker({
			id: markerData['id'],
			map: map, 
			title: markerData['creator'],
			position: myLatlng,
			custom: markerData['creator']+'<br>'+markerData['premise_street']+'<br>'+markerData['premise_zip_code']+'<br>'+markerData['premise_city']+'<br>'+markerData['premise_state'] 
		});


		// add marker to list used later to get content and additional marker information
		markerList[marker.id] =  marker;

		// add event listener when marker is clicked
		// currently the marker data contain a dataurl field this can of course be done different
		google.maps.event.addListener(marker, 'click', function() {
			
			// show marker when clicked
			showMarker(marker.id);

		});

		// add event when marker window is closed to reset map location
		google.maps.event.addListener(infowindow,'closeclick', function() { 
			//map.setCenter(defaultLatlng);
			//map.setZoom(defaultZoom);
		}); 	
	}

	/**
	 * Show marker info window
	 */
	function showMarker(markerId){
		
		// get marker information from marker list
		var marker = markerList[markerId];
		
		// check if marker was found
		
		if( marker ){
		
		   infowindow.setContent(marker['custom']);
				infowindow.open(map,marker);	
		}else{
			alert('Error marker not found: ' + markerId);
		}
	}	
	loadMap();
</script> 
</body>
</html>