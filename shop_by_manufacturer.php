<?php
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
require('includes/application_top.php');

$index = isset($_GET['index']) && !empty($_GET['index']) ? trim($_GET['index']) : null;
$columns_count = 4;
$manufacturers_count = 0;
$cur_manufacturers = array();
if ($index){
    if ($index!='|'){
        $manufacturers_query = tep_db_query("select manufacturers_name as name, manufacturers_id as id from manufacturers where manufacturers_name like '" . $index . "%' order by manufacturers_name");
    } else {
        $manufacturers_query = tep_db_query("select manufacturers_name as name, manufacturers_id as id from manufacturers where manufacturers_name regexp '^[[:<:]][^a-zA-Z]' order by manufacturers_name");
    }
    
    if ($manufacturers_count = tep_db_num_rows($manufacturers_query)){
        while ($manufacturer = tep_db_fetch_array($manufacturers_query)){
            $cur_manufacturers[$manufacturer['id']] = $manufacturer['name'];
        }

        $entries_per_column = ceil($manufacturers_count / $columns_count);

    }
}
if (isset($_SESSION['filter_p'])) $_SESSION['filter_p'] = '';
if (isset($_SESSION['filter_m'])) $_SESSION['filter_m'] = array();
if (isset($_SESSION['filter_s'])) $_SESSION['filter_s'] = array();
if (isset($_SESSION['filter_c'])) $_SESSION['filter_c'] = '';

?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
    <head>
    <?php
    // BOF: Header Tag Controller v2.6.0
    if ( file_exists(DIR_WS_INCLUDES . 'header_tags.php') ) {
        require(DIR_WS_INCLUDES . 'header_tags.php');
    } else {
    ?>
        <title><?php echo TITLE; ?></title>
    <?php
    }
    // EOF: Header Tag Controller v2.6.0
    ?>
        <base href="<?php echo (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG; ?>">
    </head>
    <body marginwidth="0" marginheight="0" topmargin="0" bottommargin="0" leftmargin="0" rightmargin="0">
    <!-- header //-->
    <?php require(DIR_WS_INCLUDES . 'header.php'); ?>
    <!-- header_eof //-->
    <!-- body //-->
        <table border="0" width="100%" cellspacing="3" cellpadding="3">
            <tr>
                <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                    <!-- left_navigation //-->
                    <?php require(DIR_WS_INCLUDES . 'column_left.php'); ?>
                    <!-- left_navigation_eof //-->
                    </table>
                </td>
                <!-- body_text //-->
                <td width="100%" valign="top">
                    <table border="0" width="100%" cellspacing="0" cellpadding="0">
                        <tr>
                            <td>
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <?php /*<tr>
                                        <td class="main">Brands</td>
                                    </tr>*/ ?>
                                    <tr>
                                        <td><?php echo tep_draw_separator('pixel_trans.gif', '100%', '10'); ?></td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        <tr>
                            <td>
                            <?php echo tep_draw_form('manufacturer_filter', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'post'); ?>
                                <input name="m_" type="hidden" value="" />
                                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td>
                                            <?php if ($cur_manufacturers){ ?>
                                            <div style="display:flex;">
                                                <?php 
                                                $start = $div_open = false;
                                                $count = 0;
                                                foreach($cur_manufacturers as $id => $name){ 
                                                    if (!$start){
                                                      echo '<div style="width:25%">' ;
                                                      $start = true;
                                                      $div_open = true;
                                                    } elseif (!$count) {
                                                        echo '<aside style="width:25%">' ;
                                                    }
                                                    echo '<a href="' . tep_href_link('shop.php', 'manufacturers_id=' . $id) . '">' . $name . '</a><br>';
                                                    $count++;
                                                    if ($count == $entries_per_column){
                                                        if ($div_open){
                                                            $div_open = false;
                                                            echo '</div>';
                                                        } else {
                                                            echo '</aside>';
                                                        }
                                                        $count = 0;
                                                    }
                                                } 
                                                if ($count<$entries_per_column){
                                                    echo '</aside>';
                                                }
                                                ?>
                                            </div>
                                            <?php } else { ?>
                                            No entries to display
                                            <?php } ?>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                            </form>
                        </tr>
                    </table>
                </td>
                <!-- body_text_eof //-->
                <td width="<?php echo BOX_WIDTH; ?>" valign="top">
                    <table border="0" width="<?php echo BOX_WIDTH; ?>" cellspacing="0" cellpadding="2">
                    <!-- right_navigation //-->
                    <?php require(DIR_WS_INCLUDES . 'column_right.php'); ?>
                    <!-- right_navigation_eof //-->
                    </table>
                </td>
            </tr>
        </table>
        <!-- body_eof //-->
        <!-- footer //-->
        <?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
        <!-- footer_eof //-->
        <br>
    </body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>