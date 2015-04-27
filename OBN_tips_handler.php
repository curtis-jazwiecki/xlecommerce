<?php
require_once('includes/application_top.php');
$split_path = explode('/', OBN_TIPS_FEED_URL);
//$feed_file = DIR_FS_OBN_FEED . OBN_RETAILER_TOKEN . '/' . $split_path[count($split_path)-1];
$feed_file = DIR_FS_CATALOG . $split_path[count($split_path)-1];
if (file_exists($feed_file)){
	$file_timestamp = filemtime($feed_file);
	$file_day = date('j', $file_timestamp);
	$file_month = date('n', $file_timestamp);
	$file_year = date('Y', $file_timestamp);
	
	$cur_timestamp = time();
	$cur_day = date('j', $cur_timestamp);
	$cur_month = date('n', $cur_timestamp);
	$cur_year = date('Y', $cur_timestamp);
	
	if (($cur_day!=$file_day) || ($cur_month!=$file_month) || ($cur_year!=$file_year)){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, OBN_TIPS_FEED_URL);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$content = curl_exec($ch);
		curl_close($ch);
		
		file_put_contents($feed_file, $content);
	} else {
		$content = file_get_contents($feed_file);
	}
} else {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, OBN_TIPS_FEED_URL);
	curl_setopt($ch, CURLOPT_HEADER, 0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$content = curl_exec($ch);
	curl_close($ch);
	
	$handle = fopen($feed_file, 'w');
	fwrite($handle, $content);
	fclose($handle);
}
$xml = simplexml_load_string($content);
?>
<table>
	<!--<tr>
		<td><?php //echo (string)$xml->channel->title; ?></td>
		<td rowspan="4">
			<img src="<?php //echo (string)$xml->channel->image->url; ?>" />
		</td>
	</tr>
	<tr>
		<td></td>
	</tr>
	<tr>
		<td><?php //echo (string)$xml->channel->description; ?></td>
	</tr>-->
	<tr>
		<td><?php echo (string)$xml->channel->item->title; ?></td>
	</tr>
	<tr>
		<td><?php echo (string)$xml->channel->item->description; ?></td>
	</tr>
</table>