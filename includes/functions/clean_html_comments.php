<?php
// /catalog/includes/functions/clean_html_comments.php
//
// BE CAREFUL NOT to use this function where it will effect currencies.php or the listings for product name
// This is used for cosmetic purposes for what the visitor sees and not for what the php code sees. The php code needs to see the HTML comment tags.
//
// Removes the <!--//* and *//--> from Product Names

////
// Clean out HTML comments code
/*
  CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
function clean_html_comments($clean_html) {
global $its_cleaned;

if ( strpos($clean_html,'<!--//*')>1 ) {
  $the_end1= strpos($clean_html,'<!--//*')-1;
  $the_start2= strpos($clean_html,'*//-->')+7;

  $its_cleaned= substr($clean_html,0,$the_end1);
  $its_cleaned.= substr($clean_html,$the_start2);
} else {
  $its_cleaned= $clean_html;
}
  return $its_cleaned;
}

?>
