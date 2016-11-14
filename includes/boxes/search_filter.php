 <?php 

 /*

  CloudCommerce - Multi-Channel eCommerce Solutions

  http://www.cloudcommerce.org

  Copyright (c) 2016 Outdoor Business Network, Inc.

*/

 	$current_template = basename(STS_TEMPLATE_DIR);

	$template_id = str_ireplace('template', '', $current_template);

 if(file_exists(DIR_FS_CATALOG . DIR_WS_INCLUDES . 'sts_templates/' . "full/$current_template/search_filter.css")){

    echo '<link rel="stylesheet" type="text/css" href="includes/sts_templates/full/'.$current_template.'/search_filter.css" />';

 }else{?>

 

    <style type="text/css">

    .Price_heading{

         background: #fff none repeat scroll 0 0;

         border: 1px solid #dadada;

		

    }

	

	.Price_heading h4 {

        -moz-border-bottom-colors: none;

        -moz-border-left-colors: none;

        -moz-border-right-colors: none;

        -moz-border-top-colors: none;

        border-color: #ccc;

        border-image: none;

        border-radius: 0;

        border-style: solid;

        border-width: 0 0 1px;

        color: #008080;

        cursor: pointer;

    	

    	font-weight:bold;

        

       

        margin: 0;

        padding: 5px 10px;

    }

	<?php if($template_id ==1) { ?>

	.color_template h4{color:#fff !important; }

	.color_template span{color:#fff !important; }

	<?php } ?>

	

	<?php if($template_id ==2) { ?>

	.color_template h4{color:#006eff !important; }

	.color_template span{color:#006eff !important; }

	<?php } ?>

	

	<?php if($template_id ==3) { ?>

	.color_template h4{color:#f00 !important; }

	.color_template span{color:#f00 !important; }

	<?php } ?>

	

	<?php if($template_id ==4) { ?>

	.color_template h4{color:#f00 !important; }

	.color_template span{color:#f00 !important; }

	<?php } ?>

	

	<?php if($template_id ==5) { ?>

	.color_template h4{color:#f00 !important; }

	.color_template span{color:#f00 !important; }

	<?php } ?>

	

	<?php if($template_id ==6) { ?>

	.color_template h4{color:#f00 !important; }

	.color_template span{color:#f00 !important; }

	<?php } ?>

	

	

  	<?php if($template_id ==18) { ?>

	.color_template h4{color:#008080 !important; }

	.color_template span{color:#008080 !important; }

	<?php } ?>

	

	<?php if($template_id ==19) { ?>

	.color_template h4{color:#008080 !important; }

	.color_template span{color:#008080 !important; }

	<?php } ?>

	

	<?php if($template_id ==21) { ?>

	.color_template h4{color:#2e3131 !important; }

	.color_template span{color:#2e3131 !important; }

	<?php } ?>

	

	

	.Price_heading h4 span {

        /*border:2px solid #008080;*/

    	/*color:#008080;*/

        float: left;

        height: 18px;

        /*margin: 2px 7px 0 0;*/

        width: 18px;

    	/*border-radius:10px;*/

    }

    .Price_heading .price_headin_open_box {

        padding: 8px 0;

    }

    .Price_heading div{

         border-bottom: 1px solid #dadada;

        margin: 0;

        max-height: 167px;

        min-height: 0;

    	overflow-x: auto;

            

    }



/* for ISO */

@media screen and (-webkit-min-device-pixel-ratio:0) {.Price_heading div{

    border-bottom: 1px solid #dadada;

    margin: 0;

    max-height: 167px;

    min-height: 0;

	overflow-x: hidden;

    overflow-y: visible;

	

    

} 

::-webkit-scrollbar {

    -webkit-appearance: none;

    width: 8px;

}



  ::-webkit-scrollbar-track {

    background-color: #fff;

    /*border-radius: 8px;*/

}

::-webkit-scrollbar-thumb {

    /*border-radius: 8px;*/

    background-color: #A6A6A6;

}







}



/*End Of ISO*/





.price_headin_open_box > input{margin-left:5px;}

.Price_heading .price_headin_open_box a, .Price_heading .price_headin_open_box a:hover {

    background: #fafafa none repeat scroll 0 0;

    border: 1px solid #ccc;

    border-radius: 3px;

    display: block;

    

    font-size: 10px;

    margin: 0 0 8px 7px;

    padding: 3px 0;

    text-align: center;

    width: 26px;

}

</style>

    

 <?php } ?>  



  

<script type="application/javascript">

  function showHide(element){

  	jQuery('#'+element).toggle('slow');

	if(jQuery('#img_'+element).html() == '+'){

		jQuery('#img_'+element).html('-');

	}else{

		jQuery('#img_'+element).html('+');

	}

  }

</script>

  

  

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

     <!-- html for By Price filter starts -->

     

      <tr>

        <td colspan="2">

        	<div class="Price_heading color_template">

				<h4 class="" onclick="showHide('price');"><span id="img_price" class="initial_action_img">-</span>By Price</h4>

					<div id="price" class="initial_action">

                    <div class="price_headin_open_box">

						<input type="radio" name="p_" value="0|50" <?php echo ($_SESSION['filter_p'] == '0|50' ? ' checked ' : ''); ?> /> &nbsp; <?php echo '< ' . $currencies->display_price('50', 0); ?>

                        

					</div>

                    

                    <div class="price_headin_open_box">

						<input type="radio" name="p_" value="50|100" <?php echo ($_SESSION['filter_p']=='50|100' ? ' checked ' : ''); ?> /> &nbsp; <?php echo $currencies->display_price('50', 0) . ' - ' . $currencies->display_price('100', 0); ?>

                        

					</div>

                    

                    

                    <div  class="price_headin_open_box">

						<input type="radio" name="p_" value="100|250" <?php echo ($_SESSION['filter_p']=='100|250' ? ' checked ' : ''); ?> /> &nbsp; <?php echo $currencies->display_price('100', 0) . ' - ' . $currencies->display_price('250', 0); ?>

                        

					</div>

                    

                    

                    <div class="price_headin_open_box">

						<input type="radio" name="p_" value="250|500" <?php echo ($_SESSION['filter_p']=='250|500' ? ' checked ' : ''); ?> /> &nbsp; <?php echo $currencies->display_price('250', 0) . ' - ' . $currencies->display_price('500', 0); ?>

                        

					</div>

                    

                    <div class="price_headin_open_box">

						<input type="radio" name="p_" value="500|750" <?php echo ($_SESSION['filter_p']=='500|750' ? ' checked ' : ''); ?> /> &nbsp; <?php echo $currencies->display_price('500', 0) . ' - ' . $currencies->display_price('750', 0); ?>

                        

					</div>

                    

                    <div class="price_headin_open_box">

						<input type="radio" name="p_" value="750|99000" <?php echo ($_SESSION['filter_p']=='750|99000' ? ' checked ' : ''); ?> /> &nbsp; <?php echo '> ' . $currencies->display_price('750', 0); ?>

                        

					</div>

                    

                    <?php

						if (!empty($_SESSION['filter_p'])){?>

		                    <div class="price_headin_open_box">

								<input type="radio" name="p_" value="" <?php echo ( empty($_SESSION['filter_p']) ? ' checked ' : ''); ?> /> &nbsp; Unset		</div>

                    

                    <?php }?>

                    

                    

                    

                    </div>

                    

                    

			</div>

        </td>

      </tr>

      

     

      

     

      

      

      

      

  

    

	<!-- html for By Price filter starts -->

	

	

	

	

	

	

	

	<?php

     //   if (!isset($_POST['m_'])) $_SESSION['filter_m'] = null;

        if (isset($_GET['manufacturers_id'])) 
             $_SESSION['filter_m'][] = $_GET['manufacturers_id'];
        else {
            if (isset($_GET['filter_id']) && isset($_GET['cPath'])) {
                        $_SESSION['filter_m'][] = $_GET['filter_id'];
            }
        }      



        $specifications = getDistinctSpecifications($_SESSION['filter_c'], $_SESSION['filter_m']);

//print_r($specifications);

//exit;

    $array_spec_opt_names = array(); // array to handle duplicate names between specifications and options

	if (sizeof($specifications) > 0) {

        $z = 0;

		foreach($specifications['specs'] as $specification => $info){

    	      if(empty($specification)){

    	          continue;

    	      }

              

              

              ?>

        	

            <tr>

            <td colspan="2">

            <div class="Price_heading color_template">

				<h4 class="" onclick="showHide('<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>');"><span class="initial_action_img" id="img_<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>">-</span><?php echo 'By ' . $specification; ?></h4>

            	    <div id="<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>" class="initial_action">

        

				 

				  <?php

					foreach($info['values'] as $value_name => $value_id){

						$count++;

			?>

             <div class="price_headin_open_box">

            <input type="checkbox" name="<?php echo 's_[]'; ?>" value="<?php echo $info['id'] . '|' . $value_id; ?>" <?php echo (is_array($_SESSION['filter_s']) ? (in_array($info['id'] . '|' . $value_id, $_SESSION['filter_s']) ? ' checked ' : '') : ''); ?> /> &nbsp; <?php echo $value_name; ?>

				 </div>

                 

                

                   <?php

                  

				}?>

                </div>

                </div>

                 </td>

                 </tr>

				

  		<?php  $z++; } 

  	

		if(sizeof($specifications['options'])>0){

			foreach($specifications['options'] as $specification => $info){

				

		  

          if(empty($specification)){

	           continue;

          }?>

          <tr>

            <td colspan="2">

				<div class="Price_heading color_template">

				<h4 class="" onclick="showHide('<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>');"><span class="initial_action_img" id="img_<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>">-</span><?php echo 'By ' . $specification; ?></h4>

            	    <div id="<?php echo preg_replace('/[^A-Za-z0-9\-]/', '', str_replace(' ', '-', $specification)).$z; ?>" class="initial_action">

             

                    <?php

            foreach($info['values'] as $value_name => $value_id){ ?>

                    <div class="price_headin_open_box">

                    	<input type="checkbox" name="<?php echo 'o_[]'; ?>" value="<?php echo $info['id'] . '|' . $value_id; ?>" <?php echo (is_array($_SESSION['filter_o']) ? (in_array($info['id'] . '|' . $value_id, $_SESSION['filter_o']) ? ' checked ' : '') : ''); ?> /> &nbsp; <?php echo $value_name; ?>

                    </div>

                    <?php } ?>

                    

                    </div>

                 </div>

			</td>

          </tr>

        <?php 

  		

			} 

		}

	}?>

    

	<?php

 

	if (!isset($_GET['manufacturers_id'])){

			

		if (!empty($_SESSION['filter_c'])){

			$manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id inner join " . TABLE_PRODUCTS_TO_CATEGORIES. " p2c on p.products_id=p2c.products_id where p.products_status='1' and m.manufacturers_status='1' and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."') and p2c.categories_id='" . $_SESSION['filter_c'] . "' and p.manufacturers_id!=0 group by p.manufacturers_id order by m.manufacturers_name");

	

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

								

		   $manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id  inner join products_description pd on p.products_id=pd.products_id and pd.language_id='1' where p.products_status='1' and m.manufacturers_status='1' and IF(p.products_bundle = 'no',p.products_quantity+p.store_quantity > '".(int)STOCK_MINIMUM_VALUE."',p.products_quantity > '".(int)STOCK_MINIMUM_VALUE."') and  p.manufacturers_id!=0 " . $keywords_str . " group by p.manufacturers_id order by m.manufacturers_name");

		  // echo "select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id inner join " . TABLE_PRODUCTS_TO_CATEGORIES. " p2c on p.products_id=p2c.products_id inner join products_description pd on p.products_id=pd.products_id and pd.language_id='1' where p.products_status='1' and m.manufacturers_status='1' and p.products_quantity >= '" . (int)STOCK_MINIMUM_VALUE . "' and p2c.categories_id='" . $_SESSION['filter_c'] . "' and p.manufacturers_id!=0 " . $keywords_str . " group by p.manufacturers_id order by m.manufacturers_name";

		   

		  } else {

			$manufacturers_query = tep_db_query("select p.manufacturers_id, m.manufacturers_name from products p inner join manufacturers m on p.manufacturers_id=m.manufacturers_id where p.manufacturers_id!=0 group by p.manufacturers_id order by m.manufacturers_name");

		}

		if (tep_db_num_rows($manufacturers_query)){?>

			<tr>

          

          	<td colspan="2">

            <div class="Price_heading color_template">

				<h4 class="" onclick="showHide('manufacturer');"><span class="initial_action_img" id="img_manufacturer">-</span>By Manufacturer</h4>

					

                    <div id="manufacturer" class="initial_action">

                    <?php

			while ($manufacturer = tep_db_fetch_array($manufacturers_query)){ ?>

                    <div class="price_headin_open_box">

                    	<input type="checkbox" name="m_[]" value="<?php echo $manufacturer['manufacturers_id']; ?>" <?php echo (!empty($_SESSION['filter_m']) && is_array($_SESSION['filter_m']) ? (in_array($manufacturer['manufacturers_id'], $_SESSION['filter_m']) ? ' checked ' : '') : ''); ?> /> &nbsp; <?php echo $manufacturer['manufacturers_name']; ?>

                    </div>

             <?php } 

			  ?>

                    

                    </div>

                 </div>  

                 </td>

                 </tr>

		<?php

        }

	}?>

    

    

    

    

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

    <?php //<input type="hidden" name="m_" value="<?php echo $_SESSION['filter_m']; >" /> ?>

    <script>

    jQuery(function(){

		   jQuery('#filters input:checkbox').click(function(){

				jQuery('form[name="filter"]').submit();

		   }); 

			jQuery('#filters input:radio').click(function(){

				jQuery('form[name="filter"]').submit();

		   }); 



			jQuery('#mobilefilters input:checkbox').click(function(){

				jQuery('form[name="mobilefilter"]').submit();

		   }); 

			jQuery('#mobilefilters input:radio').click(function(){

				jQuery('form[name="mobilefilter"]').submit();

		   }); 				

		

	   

	<?php if($template_id == 2) { ?>

			jQuery('.initial_action').toggle();

			jQuery('.initial_action_img').html('+');

	<?php } ?>

	<?php if($template_id == 17) { ?>
	
			jQuery('.initial_action').toggle();

			jQuery('.initial_action_img').html('+');
	
	
	<?php } ?>

	   
	   
	   
	   

	   

	   

    });

</script>

    <?php

	$content = ob_get_contents();

	ob_end_clean();



  	$info_box_contents = array();

  	//$info_box_contents[] = array('text' => tep_image(DIR_WS_IMAGES . 'filter.jpg'));

     $info_box_contents[] = array('text' => 'Search filter');



  	new infoBoxHeading($info_box_contents, false, false);



  	$info_box_contents = array();

  	$info_box_contents[] = array(

		'form' => tep_draw_form('filter', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'post'),

        'align' => 'center',

        'text' =>$content

	);



   new columnBox($info_box_contents); ?>

  

 