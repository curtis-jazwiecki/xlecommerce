<?

   // if (DISPLAY_PAGE_PARSE_TIME == 'true') {
    $time_start = explode(' ', PAGE_PARSE_START_TIME);
    $time_end = explode(' ', microtime());
    $parse_time = number_format(($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0])), 3);
    echo '<div align="center"><span class="smallText">Current Parse Time: <b>' . $parse_time . ' s</b> with <b>'.sizeof($debug['QUERIES']).' queries</b></span></div>';
	if ($_REQUEST['output'] == '0') $_SESSION['output'] = '0';
//	if ($_REQUEST['output'] == '1' || $_SESSION['output'] == '1')
//		{
			$_SESSION['output'] = '1';
			echo '<b>QUERY DEBUG:</b> ';
			echo '<pre>';
			print_r($debug);
			echo '</pre>';
			echo '<hr>';
			echo '<b>SESSION:</b> ';
			echo '<pre>';
			print_r($_SESSION);
			echo '</pre>';
			echo '<hr>';
			echo '<b>COOKIE:</b> ';
			echo '<pre>';
			print_r($_COOKIE);
			echo '</pre>';
			echo '<b>POST:</b> ';
			echo '<pre>';
			print_r($_POST);
			echo '</pre>';
			echo '<hr>';
			echo '<b>GET:</b> ';
			echo '<pre>';
			print_r($_GET);
			echo '</pre>';
	//	} # END if request
    //}
	unset($debug);
?>