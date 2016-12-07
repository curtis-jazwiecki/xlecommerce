<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
$mcontent = '';
$manufacture_query = tep_db_query("SELECT m.manufacturers_id, m.manufacturers_name, m.manufacturers_image, count(p.products_id) as 'total_product' FROM manufacturers m, products p WHERE m.manufacturers_id = p.manufacturers_id and m.manufacturers_status = '1' and`products_status` = '1' group by p.manufacturers_id order by manufacturers_image desc, total_product desc limit 5");
while($manufacture = tep_db_fetch_array($manufacture_query)){
    if(!empty($manufacture['manufacturers_image'])){
        $mcontent .= '<li><a href="'. tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'manufacturers_id=' . $manufacture['manufacturers_id']).'"><div style="margin-top:-5px;font-weight:bold;width:180px;height:46px;font-size:18px;vertical-align:middle;" ><img alt="'.strtoupper($manufacture['manufacturers_name']).'" height="45" src="images/'.$manufacture['manufacturers_image'].'"></div></a></li>';
    }else{
        $mcontent .= '<li><a href="'.tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, 'manufacturers_id=' . $manufacture['manufacturers_id']).'"><div style="margin-top:20px;font-weight:bold;width:180px;height:46px;font-size:18px;vertical-align:middle;" >'.strtoupper($manufacture['manufacturers_name']).'</div></a></li>';
    }
    
}
?>
<ul class="test">
    <?php echo $mcontent;?>
</ul>