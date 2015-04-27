<div class="select_wrapper" id="select_top">
    <select name="select1" id="select1" tabindex="1">
        <option value="<?php echo TEXT_CHOOSE_BELOW; ?>"><?php echo TEXT_CHOOSE_BELOW; ?></option>
        <option value="<?php echo OPTION_1; ?>"><?php echo OPTION_1; ?></option>
        <option value="<?php echo OPTION_2; ?>"><?php echo OPTION_2; ?></option>

    </select>

    <div id="search_dresscode">
        <?php
        $search_string .= '<div id="search">';
        $search_string .= tep_draw_input_field('keywords', ET_SEARCH_KEYWORD, 'id="search_input" onfocus="if(this.value == \''.ET_SEARCH_KEYWORD.'\') {this.value = \'\';}" onblur="if (this.value == \'\') {this.value = \''.ET_SEARCH_KEYWORD.'\';}"') . '&nbsp;' . tep_hide_session_id() .'';
        $search_string .= '</div>';
        $search_string .= '';
        $search_string .= '<input name="search" value="" type="submit"/>';
        $search_string .= '';
        $info_box_contents = tep_draw_form('et_quick_find', tep_href_link(FILENAME_ADVANCED_SEARCH_RESULT, '', 'NONSSL', false), 'get').$search_string.'</form>
  ';
        echo $info_box_contents;
        ?>
    </div>
</div>









