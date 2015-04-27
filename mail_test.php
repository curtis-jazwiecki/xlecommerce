<?php
$to      = 'office@focusindia.com';
$subject = 'test subject';
$message = 'test message';
$headers = 'From: support@outdoorbusinessnetwork.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

if (mail($to, $subject, $message, $headers)){
	echo 'sent';
} else {
	echo 'not sent';
}
?>