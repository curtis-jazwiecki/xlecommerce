<?php
$sts->start_capture();
require_once(DIR_WS_LANGUAGES . $language . '/' . FILENAME_ADVANCED_SEARCH);
?>
<div>
    <style>
    #advancedSearch_ {width: 350px;}
    #advancedSearch td[colspan="2"]:not(.pageHeading) {font-size: 12px;}
    table#advancedSearch input {height: 8px;max-width: 200px;}
    table#advancedSearch input[type="image"] {height: 18px;}
    table#advancedSearch select {font-size: 10px;height: 15px;width: 134px;}
    table#advancedSearch .smallText {font-size: 11px;height: 11px;}
    td.criteria , td.criteria span{font-size:11px !important;}
    </style>
    <script>
    function popupWindow2(url) {
        window.open(url,'popupWindow','toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=yes,copyhistory=no,width=450,height=280,screenX=150,screenY=150,top=150,left=150')
    }
    </script>
    <?php echo tep_draw_form('advanced_search', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get', 'onSubmit="return check_form(this);"') . tep_hide_session_id(); ?>
    <table>
        <tr>
            <td align="left">
                <table border="0" width="100%" cellspacing="0" cellpadding="2" id="advancedSearch">
                   
                    <tr>
                        <td colspan="2" class="criteria"><?php echo HEADING_SEARCH_CRITERIA;?></td>
                    </tr>
                    <tr>
                        <td colspan="2"><?php echo tep_draw_input_field('keywords', '', 'style="width: 50%"');?></td>
                    </tr>
                    <tr>
                        <td colspan="2" class="criteria"><?php echo tep_draw_checkbox_field('search_in_description1', '1');?><?php echo TEXT_SEARCH_IN_DESCRIPTION; ?></td>
                    </tr>
                    <tr>
                        <td class="smallText"><?php echo '<a href="javascript:popupWindow2(\'' . tep_href_link(FILENAME_POPUP_SEARCH_HELP) . '\')">' . TEXT_SEARCH_HELP_LINK . '</a>'; ?></td>
                        <td class="smallText" align="right"><?php echo tep_image_submit('button_search.gif', IMAGE_BUTTON_SEARCH); ?></td>
                    </tr>
                    <tr>
                        <td class="fieldKey"><?php echo ENTRY_CATEGORIES; ?></td>
                        <td class="fieldValue">
                        <?php echo tep_draw_pull_down_menu('categories_id', tep_get_categories_enabled(array(array('id' => '', 'text' => TEXT_ALL_CATEGORIES)))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldKey">&nbsp;</td>
                        <td class="smallText">
                        <?php echo tep_draw_checkbox_field('inc_subcat', '1', true) . ' ' . ENTRY_INCLUDE_SUBCATEGORIES; ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldKey"><?php echo ENTRY_MANUFACTURERS; ?></td>
                        <td class="fieldValue">
                        <?php echo tep_draw_pull_down_menu('manufacturers_id', tep_get_manufacturers(array(array('id' => '', 'text' => TEXT_ALL_MANUFACTURERS)))); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="fieldKey"><?php echo ENTRY_PRICE_FROM; ?></td>
                        <td class="fieldValue"><?php echo tep_draw_input_field('pfrom'); ?></td>
                    </tr>
                    <tr>
                        <td class="fieldKey"><?php echo ENTRY_PRICE_TO; ?></td>
                        <td class="fieldValue">
                        <?php echo tep_draw_input_field('pto'); ?>
                        </td>
                    </tr>    
                </table>
            </td>
        </tr>
    </table>
    </form>
</div>
<?php
$sts->stop_capture('advanced_search_mini_block');
?>