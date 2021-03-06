<?php
/*
$Id: sts_display_output.php,v 4.1 2006/01/25 05:57:12 rigadin Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2005 osCommerce

Released under the GNU General Public License

Based on: Simple Template System (STS) - Copyright (c) 2004 Brian Gallagher - brian@diamondsea.com
STS v4.1 by Rigadin (rigadin@osc-help.net)
*/

// Get list of modules to include
$sts_modules_inc = $sts->capture_fields(); // Returns an array of files to include
$sts_modules_str = ''; // Added in v4.0.7 for debug

if (!empty($sts_modules_inc)) {
  foreach ($sts_modules_inc as $sts_mod){
    include(DIR_WS_MODULES.'sts_inc/'.$sts_mod);
	  $sts_modules_str.= $sts_mod .' - '; // Added in v4.0.7 for debug
  }
}

 if ($sts->display_template_output == 1) {
    $sts->read_template_file();
    }
  
$sts->template["content"] = str_replace("\$featuredproducts", $sts->template["featuredproducts"], $sts->template["content"]);


  
// If we use a template, replace placeholders and display the page
  if ($sts->display_template_output == 1) {
    $sts->replace();  // Read file and replace placeholders with variables content
    echo $sts->template['template_html'];  // Display the page
  }

// Display debug information if we are in template debug mode
 if ($sts->display_debug_output) {
  // Print Debugging Info
  print "\n<pre><hr>\n";
  print "STS_VERSION=[" . $sts->version . "]<br>\n";
  print "OSC_VERSION=[".PROJECT_VERSION."]\n";
  print "STS_TEMPLATE=[" . $sts->template_file . "]\n";
  print "STS_MODULE=[" . $sts->script->code . "]\n";
  print "STS_INC_MODULES=[". $sts_modules_str . "]<hr>\n"; // Added in v4.0.7 to know what modules are included

  $sts->template['template_html'] = 'This variable is the source code of this page, without debug informations. As it is quite long, we dont show it here.';
  foreach ($sts->template as $key=>$value) {
    print "<b>\$sts->template['$key']</b><hr>" . htmlspecialchars($value) . "<hr>\n";
  }

 }
?>
