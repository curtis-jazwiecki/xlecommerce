<?php
class sts_contact_us {

  var $template_file, $template_type;
  
  function sts_contact_us (){
    $this->code = 'sts_contact_us';
    $this->title = MODULE_STS_CONTACT_US_TITLE;
    $this->description = MODULE_STS_CONTACT_US_DESCRIPTION.' (v1.0.6)';
	$this->sort_order=3;
    $this->enabled = ((MODULE_STS_CONTACT_US_STATUS == 'true') ? true : false);
	$this->template_file = STS_DEFAULT_TEMPLATE; // Should not be needed but just in case something goes weird
	$this->content_template_file='';
  }
  
  function find_content_template () {
	$check_file= STS_TEMPLATE_DIR . "contact_us.php.html"; 
	if (file_exists($check_file)) return $check_file;

	return '';
  }

  function find_template (){
	$this->template_file = $this->content_template_file = $this->find_content_template();
    if (!empty($this->template_file))
        return $this->template_file;
    else
        return STS_DEFAULT_TEMPLATE;
  }

  function capture_fields () {
    //if ($this->content_template_file!='') {
	//  return MODULE_STS_CONTACT_US_CONTENT;
	//} else {
	    $temp= MODULE_STS_CONTACT_US_NORMAL;
		if (MODULE_STS_CONTACT_US_V3COMPAT=='true') $temp.=';contactus_sts3.php';
	    return $temp;
	 //}	
  }

  function replace (&$template) {
    //if ($this->content_template_file=='') {
	  $template['content']=sts_strip_content_tags($template['content'], 'Contact Us Content');
	  //return;
	//}
	
	/*global $template_contactus;
	
    // Read content template file
	$template_html = sts_read_template_file($this->content_template_file);
    if (defined(STS_END_CHAR)==false) { // If no end char defined for the placeholders, have to sort the placeholders.
      uksort($template_contactus, "sortbykeylength"); // Sort array by string length, so that longer strings are replaced first
	  define ('STS_CONTENT_END_CHAR', ''); // An end char must be defined, even if empty.
    }	
    foreach ($template_contactus as $key=>$value) {
	  $template_html = str_replace('$' . $key . STS_CONTENT_END_CHAR , $value, $template_html);
    }

    $template['content'] = $template_html;*/
  }
  
//======================================
// Functions needed for admin
//======================================
  
    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_CONTACT_US_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }

      return $this->_check;
    }

    function keys() {
      return array('MODULE_STS_CONTACT_US_STATUS','MODULE_STS_CONTACT_US_V3COMPAT' ,'MODULE_STS_CONTACT_US_NORMAL', 'MODULE_STS_CONTACT_US_CONTENT');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use template for contact us page', 'MODULE_STS_CONTACT_US_STATUS', 'false', 'Do you want to use templates for product info pages?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Enable STS3 compatibility mode', 'MODULE_STS_CONTACT_US_V3COMPAT', 'false', 'Do you want to enable the STS v3 compatibility mode (only for templates made with STS v2 and v3)?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Files for normal template', 'MODULE_STS_CONTACT_US_NORMAL', 'sts_user_code.php', 'Files to include for a normal template, separated by semicolon', '6', '2', now())");
	  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Files for content template', 'MODULE_STS_CONTACT_US_CONTENT', 'sts_user_code.php;contact_us.php', 'Files to include for a content template, separated by semicolon', '6', '3', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }  
  
}// end class
?>