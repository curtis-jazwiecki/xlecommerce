<?php
require ('includes/application_top.php');
$data = array();
$term = $_GET['term'];
$manufacturers_query = tep_db_query("select manufacturers_id, manufacturers_name from manufacturers where manufacturers_name like '%" . tep_db_prepare_input($term) . "%'");
if (tep_db_num_rows($manufacturers_query)){
    while($entry = tep_db_fetch_array($manufacturers_query)){
        $data[] = array(
            'value' => $entry['manufacturers_name'], 
            'id' => $entry['manufacturers_id'], 
        );
    }
}
$sts->template['json'] = json_encode($data);
require (DIR_WS_INCLUDES . 'application_bottom.php');
?>