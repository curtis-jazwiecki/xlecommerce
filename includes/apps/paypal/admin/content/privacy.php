<?php
/*
  $Id$

 CloudCommerce - Multi-Channel eCommerce Solutions
  http://www.cloudcommerce.org
  Copyright (c) 2016 Outdoor Business Network, Inc.
*/
?>

<h2><?php echo $OSCOM_PayPal->getDef('privacy_title'); ?></h2>

<?php echo $OSCOM_PayPal->getDef('privacy_body', array('api_req_countries' => implode(', ', $OSCOM_PayPal->getReqApiCountries()))); ?>
