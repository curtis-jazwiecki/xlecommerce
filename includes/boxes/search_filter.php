<tr><td>
<?php
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
    unset($_SESSION['keywords']);
    unset($_SESSION['categories_id']);
    unset($_SESSION['inc_subcat']);
}

$keywords = (isset($_GET['keywords']) ? $_GET['keywords'] : (isset($_POST['keywords']) ? $_POST['keywords'] : $_SESSION['keywords']));


if (isset($_GET['keywords'])) { 
    $_SESSION['keywords'] = $_GET['keywords'];
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
        if (!isset($_POST['m_'])) $_SESSION['filter_m'] = null;
        if (isset($_GET['manufacturers_id'])) $_SESSION['filter_m'] = $_GET['manufacturers_id'];

        $specifications = getDistinctSpecifications($_SESSION['filter_c'], $_SESSION['filter_m']);
//print_r($specifications);
//exit;
        if (sizeof($specifications) > 0) {
        foreach($specifications['specs'] as $specification => $info){ 
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
  foreach($specifications['options'] as $specification => $info){ 
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
            <input type="checkbox" name="<?php echo 'o_[]'; ?>" value="<?php echo $info['id'] . '|' . $value_id; ?>" <?php echo (is_array($_SESSION['filter_o']) ? (in_array($info['id'] . '|' . $value_id, $_SESSION['filter_o']) ? ' checked ' : '') : ''); ?> />
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
		$manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id inner join " . TABLE_PRODUCTS_TO_CATEGORIES. " p2c on p.products_id=p2c.products_id where p.products_status='1' and m.manufacturers_status='1' and p.products_quantity >= '" . (int)STOCK_MINIMUM_VALUE . "' and p2c.categories_id='" . $_SESSION['filter_c'] . "' and p.manufacturers_id!=0 group by p.manufacturers_id order by m.manufacturers_name");

	} elseif (isset($_SESSION['keywords']) &&  $_SESSION['keywords'] != '') { 
                    tep_parse_search_string($keywords, $search_keywords);
                                if (isset($search_keywords) && (sizeof($search_keywords) > 0)) {
                                    $keywords_str = " and ((";
                                    for ($i = 0, $n = sizeof($search_keywords); $i < $n; $i++) {
                                        switch ($search_keywords[$i]) {
                                            case '(':
                                            case ')':
                                            case 'and':
                                            case 'or':
                                                $keywords_str .= " " . $search_keywords[$i] . " ";
                                                break;
                                            default:
                                                $keyword = tep_db_prepare_input($search_keywords[$i]);
                                                $keywords_str .= "(pd.products_name like '%" . tep_db_input($keyword) . "%' or p.products_model like '%" . tep_db_input($keyword) . "%' or m.manufacturers_name like '%" . tep_db_input($keyword) . "%'";
                                                $keywords_str  .= ')';
                                                break;
                                        }
                                    }
                                }
                                $keywords_str .= " )";
                                
                              $keywords_str .= " )";
                            
       $manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id  inner join products_description pd on p.products_id=pd.products_id and pd.language_id='1' where p.products_status='1' and m.manufacturers_status='1' and p.products_quantity >= '" . (int)STOCK_MINIMUM_VALUE . "' and  p.manufacturers_id!=0 " . $keywords_str . " group by p.manufacturers_id order by m.manufacturers_name");
      // echo "select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id inner join " . TABLE_PRODUCTS_TO_CATEGORIES. " p2c on p.products_id=p2c.products_id inner join products_description pd on p.products_id=pd.products_id and pd.language_id='1' where p.products_status='1' and m.manufacturers_status='1' and p.products_quantity >= '" . (int)STOCK_MINIMUM_VALUE . "' and p2c.categories_id='" . $_SESSION['filter_c'] . "' and p.manufacturers_id!=0 " . $keywords_str . " group by p.manufacturers_id order by m.manufacturers_name";
       
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
<?php
if (isset($_SESSION['keywords'])) { 
    $keywords = $_SESSION['keywords'];
    echo tep_draw_hidden_field('keywords', $keywords);
 }
 if ($_POST['items_per_page'] && $_POST['items_per_page'] > 0) {
  $_SESSION['items_per_page'] = $_POST['items_per_page']; 
 }
 echo tep_draw_hidden_field('items_per_page', $_SESSION['items_per_page']); ?>
<?php//<input type="hidden" name="m_" value="<?php echo $_SESSION['filter_m']; >" /> ?>
<script>
    jQuery(function(){
       jQuery('#filters input:checkbox').click(function(){
            jQuery('form[name="filter"]').submit();
       }); 
		jQuery('#filters input:radio').click(function(){
            jQuery('form[name="filter"]').submit();
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
  $info_box_contents[] = array('form' => tep_draw_form('filter', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'post'),
                               'align' => 'center',
                               'text' =>$content);

  new columnBox($info_box_contents);
?>
</td></tr>