<?php `git pull origin release > git_update_log.txt`;
// Include application configuration parameters
require('includes/configure.php');

// include the database functions
require(DIR_WS_FUNCTIONS . 'database.php');

// make a connection to the database... now
$$link = tep_db_connect() or die('Unable to connect to database server!');

$filename = "database_changes.sql";

if (file_exists($filename)) {

    $error_queries = array();

    $lines = file($filename);

    foreach ($lines as $line) {

        // Skip it if it's a comment
        if (substr($line, 0, 2) == '--' || $line == '') {
            continue;
        }

        $templine .= $line;

        // If it has a semicolon at the end, it's the end of the query
        if (substr(trim($line), -1, 1) == ';') {

            if (class_exists('mysqli')) {

                $result = mysqli_query($$link, $templine);

                if ($result === false) {

                    $error_queries[] = $templine . " :: " . mysqli_errno($$link) . " :: " . mysqli_error($$link);
                }
            } else {

                mysql_query($query, $$link) or $error_queries[] = $templine . " :: " . mysql_errno() . " :: " . mysql_error();
            }

            $templine = '';
        }
    }

    if (count($error_queries) > 0) {
        mail("gitlog@outdoorbusinessnetwork.com", "Query error", HTTP_SERVER . "\n\n" . implode("\n\n", $error_queries));
    }

    @unlink("database_changes.sql");
}
// code to send git pull message to P1 server #start
define('REMOTE_SERVER_PATH', 'http://67.227.172.78/parse_log.php');

$logstring = "SiteName: " . HTTP_SERVER . "\n\n";
$logstring .= "Git Output:" . "\n" . "===========" . "\n\n";
$logstring .= file_get_contents("git_update_log.txt");
@unlink("git_update_log.txt");

$send_data = array(
    'action' => 'writelog',
    'logstring' => $logstring,
    'log_date' => date("Y-m-d")
);

$ch = curl_init(REMOTE_SERVER_PATH);
$request = json_encode(array("request" => $send_data));
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$result = curl_exec($ch);
curl_close($ch);
// code to send git pull message to P1 server #ends
?>