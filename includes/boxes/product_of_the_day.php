<?php
if ($PHP_SELF=='/index.php'){
	$pod_query = tep_db_query("select pftd.id, pftd.products_id, pftd.start_date, pftd.status, p.products_image, p.products_mediumimage, p.products_price, p.products_tax_class_id, pd.products_name from product_for_the_day pftd inner join products p on pftd.products_id=p.products_id inner join products_description pd on  (p.products_id=pd.products_id and pd.language_id='1') where pftd.start_date='" . date('Y-m-d', time()) . "' and pftd.status='1' and p.products_status='1' order by id desc limit 0, 1");

	if (tep_db_num_rows($pod_query)){
		$pod = tep_db_fetch_array($pod_query);
	?>
	<tr>
		<td align="center">
		<?php
		$feed_status = is_xml_feed_product($pod['products_id']);
		if ($_SERVER['HTTPS'] != "on"){
			if ($feed_status) { 
				$image = tep_small_image($pod['products_image'], $pod['products_name'], '', '','class="subcatimages"'); 
			} else { 
				$image = tep_image(DIR_WS_IMAGES . $pod['products_image'], $pod['products_name'], SMALL_IMAGE_WIDTH, SMALL_IMAGE_HEIGHT); 
			}
		}
		
		$info_box_contents = array();
		$info_box_contents[] = array(
			'text' => '<h4>Product of the Day</h4>' , 
		);
		new infoBoxHeading($info_box_contents);
		
		$info_box_contents = array();
		$info_box_contents[] = array(
			'align' => 'center',
			'width' => '100',
			'text' => '<a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $pod["products_id"]) . '">' . $image . '</a><br><a href="' . tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $pod['products_id']) . '">' . $pod['products_name'] . '</a><br>' . $currencies->display_price($pod['products_price'], tep_get_tax_rate($pod['products_tax_class_id']))
		);

		new columnBox($info_box_contents);
		?>
		</td>
	</tr>
	<?php
	}
}
?>
