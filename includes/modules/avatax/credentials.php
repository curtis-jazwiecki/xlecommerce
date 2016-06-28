<?php

// ATConfig object is how credentials are set
// Tax or Address Service Objects take an argument
// which is the name of the ATConfig object ('Development' or 'Production' below)


/* This is a configuration called 'Development'. 
 * It includes the account number and license key for the TRIAL use of the AvaTax service:
 *
 * $service = new AddressServiceSoap('Development');
 * $service = new TaxServiceSoap('Development');
 */
new ATConfig('Development', array(
    'url'       => MODULE_ORDER_TOTAL_AVATAX_SERVICE_URL,
    'account'   => MODULE_ORDER_TOTAL_AVATAX_ACCOUNT_NUMBER, //replace with Avalara production account number 
    'license'   => MODULE_ORDER_TOTAL_AVATAX_LICENSE_KEY, //replace with Avalara development license key
    'client'    => 'a0o33000004Aysr',
    'name'      => 'Outdoor Business Network',
    'trace'     => true) // true for development
);

/* This is a configuration called 'Production' 
 * Example:
 *
 * $service = new AddressServiceSoap('Production');
 * $service = new TaxServiceSoap('Production');
 */
new ATConfig('Production', array(
    'url'       => MODULE_ORDER_TOTAL_AVATAX_SERVICE_URL,
    'account'   => MODULE_ORDER_TOTAL_AVATAX_ACCOUNT_NUMBER,  // Insert Avalara production account number
    'license'   => MODULE_ORDER_TOTAL_AVATAX_LICENSE_KEY,  // Insert Avalara production license key
    'client'    => 'a0o33000004Aysr',
    'name'      => 'Outdoor Business Network',
    'trace'     => false) // false for production
);

