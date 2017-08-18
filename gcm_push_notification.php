<?php // if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 * @package        MAD App
 * @subpackage      Helpers
 * @category        Helpers
 * @author          Chinnu
 * @copyright       Copyright (c) 2015 - 2016, OrisysIndia, LLP.
 * @link            http://orisys.in
 * @since           Version 1.0
 * @date            28/6/2017
 * @filesource
 */
 
$registatoin_ids = json_decode($_REQUEST['id']);
$message = json_decode($_REQUEST['msg'], true);
if(!empty($registatoin_ids)) {
    //Google cloud messaging GCM-API url
    $url = 'https://fcm.googleapis.com/fcm/send';
    $fields = array(
        'registration_ids' => $registatoin_ids,
        'data' => $message,
    );
  // print_r($registatoin_ids_fcm);
  // echo "<br><br>";
  // Update your Google Cloud Messaging API Key
  // define("GOOGLE_API_KEY2", "AIzaSyCOsIxjMPfzN-X4b4HInmNTc3qBUp9jam8"); 
    define("GOOGLE_API_KEY2", "AAAA_jcFElg:APA91bHvMq8w7zoDafvLpYjSY2iAcF8ufDoOtAa5xuQrOxi7budFa7z2N6Jzv0b_5SOw7DMtZbXb1OScvMt49sUFV9UBvSasoWSjttFl2tCr8tqHj65y0LCjQwDwKaRYLhEMdrL6BIdM"); 
    $headers = array('Authorization: key=' . GOOGLE_API_KEY2, 'Content-Type: application/json');

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);	
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
    echo $result = curl_exec($ch);	
    echo "<br><br>";
    if ($result === FALSE) {
        die('Curl failed: ' . curl_error($ch));
    }
    curl_close($ch);
    // let's check the response
   $data = json_decode($result);
}
