<tr><td>
<?php
//if (!isset($_SESSION['filter_p'])) $_SESSION['filter_p'] = array();

//if (!isset($_SESSION['filter_p'])) $_SESSION['filter_p'] = '';
//if (!isset($_SESSION['filter_m'])) $_SESSION['filter_m'] = array();
//if (!isset($_SESSION['filter_s'])) $_SESSION['filter_s'] = array();

//if (!isset($_SESSION['filter_k'])) $_SESSION['filter_k'] = '';

//if (!isset($_SESSION['filter_c'])) $_SESSION['filter_c'] = '';

if (isset($_GET['rc_']) && $_GET['rc_']=='1'){
	$_SESSION['filter_c'] = '';
} elseif ($_SERVER['PHP_SELF'] == "/product_info.php"){
	if (isset($_GET['products_id'])){
		$category_query = tep_db_query("select categories_id from products_to_categories where products_id='" . (int)$_GET['products_id'] . "' limit 0, 1");
		if (tep_db_num_rows($category_query)){
			$info = tep_db_fetch_array($category_query);
			if (!empty($info['categories_id'])){
				$_SESSION['filter_c'] = $info['categories_id'];
			}
		}
	}
} elseif (isset($_GET['cPath'])){
    $_SESSION['filter_c'] = $_GET['cPath'];
}

ob_start();
?>
<table style="width:100%" id="filters">
<tr>
		<td colspan="2" class="smallText searchFlFtClr" style="color:white;font-size:12px;font-weight:bold;margin-top:5px;">
		By Price
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="0|50" <?php echo ($_SESSION['filter_p'] == '0|50' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo '< ' . $currencies->display_price('50', 0); ?>
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="50|100" <?php echo ($_SESSION['filter_p']=='50|100' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $currencies->display_price('50', 0) . ' - ' . $currencies->display_price('100', 0); ?>
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="100|250" <?php echo ($_SESSION['filter_p']=='100|250' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $currencies->display_price('100', 0) . ' - ' . $currencies->display_price('250', 0); ?>
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="250|500" <?php echo ($_SESSION['filter_p']=='250|500' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $currencies->display_price('250', 0) . ' - ' . $currencies->display_price('500', 0); ?>
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="500|750" <?php echo ($_SESSION['filter_p']=='500|750' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $currencies->display_price('500', 0) . ' - ' . $currencies->display_price('750', 0); ?>
		</td>
	</tr>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="750|99000" <?php echo ($_SESSION['filter_p']=='750|99000' ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo '> ' . $currencies->display_price('750', 0); ?>
		</td>
	</tr>
<?php
	if (!empty($_SESSION['filter_p'])){
?>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
			<input type="radio" name="p_" value="" <?php echo ( empty($_SESSION['filter_p']) ? ' checked ' : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		Unset
		</td>
	</tr>
<?php
	}
?>
<?php
        if (isset($HTTP_GET_VARS['categories_id'])) $_SESSION['filter_c'] = $HTTP_GET_VARS['categories_id'];
        if (!isset($_POST['m_'])) $_SESSION['filter_m'] = null;
        if (isset($_GET['manufacturers_id'])) $_SESSION['filter_m'] = $_GET['manufacturers_id'];

        //if (!empty($_SESSION['filter_c'])) $_SESSION['filter_m'] = null;
        $specifications = getDistinctSpecifications($_SESSION['filter_c'], $_SESSION['filter_m']);
//print_r($specifications);
//exit;
        if (sizeof($specifications) > 0) {
        foreach($specifications as $specification => $info){ 
?>
	<tr>
		<td colspan="2" class="smallText searchFlFtClr" style="color:white;font-size:12px;font-weight:bold;margin-top:5px;">
		<?php echo 'By ' . $specification; ?>
		</td>
	</tr>
<?php
        foreach($info['values'] as $value_name => $value_id){
            $count++;
?>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
            <input type="checkbox" name="<?php echo 's_[]'; ?>" value="<?php echo $info['id'] . '|' . $value_id; ?>" <?php echo (is_array($_SESSION['filter_s']) ? (in_array($info['id'] . '|' . $value_id, $_SESSION['filter_s']) ? ' checked ' : '') : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $value_name; ?>
		</td>
	</tr>
<?php
	}
?>
<?php 
  } 
}
?>
<?php
if (!isset($_GET['manufacturers_id'])){
	if (!empty($_SESSION['filter_c'])){
		$manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id inner join products_to_categories p2c on p.products_id=p2c.products_id where p2c.categories_id='" . $_SESSION['filter_c'] . "' and p.manufacturers_id!=0 group by p.manufacturers_id order by m.manufacturers_name");
	} else {
		$manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id where p.manufacturers_id!=0 group by p.manufacturers_id order by m.manufacturers_name");
	}
    if (tep_db_num_rows($manufacturers_query)){
?>
	<tr>
		<td colspan="2" class="smallText searchFlFtClr" style="color:white;font-size:12px;font-weight:bold;margin-top:5px;">
		By Manufacturer
		</td>
	</tr>
<?php
        while ($manufacturer = tep_db_fetch_array($manufacturers_query)){
?>
    <tr>
        <td class="smallText searchFlFtClr" style="color:white;margin-right:10px;">
            <input type="checkbox" name="m_[]" value="<?php echo $manufacturer['manufacturers_id']; ?>" <?php echo (!empty($_SESSION['filter_m']) && is_array($_SESSION['filter_m']) ? (in_array($manufacturer['manufacturers_id'], $_SESSION['filter_m']) ? ' checked ' : '') : ''); ?> />
		</td>
		<td class="smallText searchFlFtClr1" style="color:white;">
		<?php echo $manufacturer['manufacturers_name']; ?>
		</td>
	</tr>
<?php
        }
    }
}
?>
</table>
<input type="hidden" name="c_" value="<?php echo $_SESSION['filter_c']; ?>" />
<?php//<input type="hidden" name="m_" value="<?php echo $_SESSION['filter_m']; >" /> ?>
<script>
    jQuery(function(){
       jQuery('#filters input:checkbox').click(function(){
            jQuery('form[name="search_filter"]').submit();
       }); 
		jQuery('#filters input:radio').click(function(){
            jQuery('form[name="search_filter"]').submit();
       }); 
    });
</script>
<?php
$content = ob_get_contents();
ob_end_clean();

  $info_box_contents = array();
  $info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'filter.jpg'));

  new infoBoxHeading($info_box_contents, false, false);

  $info_box_contents = array();
  $info_box_contents[] = array('form' => tep_draw_form('search_filter', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'post'),
                               'align' => 'center',
                               'text' =>$content);

  new columnBox($info_box_contents);
?>
</td></tr>