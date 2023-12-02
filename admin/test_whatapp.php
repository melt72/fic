<?php

// Require the Composer autoloader.
require 'assets/vendor/autoload.php';

use Netflie\WhatsAppCloudApi\WhatsAppCloudApi;

// Instantiate the WhatsAppCloudApi super class.
$whatsapp_cloud_api = new WhatsAppCloudApi([
    'from_phone_number_id' => '133933946459323',
    'access_token' => 'EAAI4LA1OBPUBOznZCimmLvUuQPALYNucyzaIhKPWn3ozBzFXZBkdgcLRSun2bqGbwMhgnIT2RyyfGr5lCtMKx2qZBrwBexOJkXnSjrutCPixVBk0YiDvhqPZCGUOPlXyW9TD2ZC3gYzIKZBZCZBaxcsZC4AuReVP8RZC3zKzEYhPqtmVTxBRLxWuQ1aAMEitniWP997Gk3DSFL231Az52AtDydfpiimKLHd',
]);

$whatsapp_cloud_api->sendTextMessage('+393337229875', 'Hey there! ');

// $whatsapp_cloud_api->sendTemplate('+393337229875', 'hello_world', 'en_US'); // Language is optional
