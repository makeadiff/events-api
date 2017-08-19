<?php
$_REQUEST['id'] = '["cxmrLje1_q8:APA91bHcqQfz9m9ok9HgIm4YvdXL7JtmVWCKmrMYStbpkq8oGnXIDpsIieJOCZOoa_pqWDjRpDw53gtstpeAj_9i_-jnC0gu8J9ek5dc_zbpS8qH5a1KvNC-v_ZOXwNXj3BeAQRvIMqJ"]';
$_REQUEST['msg'] = '{"status": 1,
"title": "Event has been cancelled",
"type": 3,
"event_id": 1998,
"event_name": "Testing New",
"event_details": "New Details",
"event_date": "02-August-2017",
"event_location": "New Location",
"latitude": 3,
"longitude": 4,
"event_city": 28,
"event_time": "05:30 AM",
"user_choice": 1,
"event_going_count": 1,
"event_invited_count": 5,
"event_maybe_count": 0,
"event_cantgo_count": 0}';
$registation_ids = json_decode($_REQUEST['id']);
$message = json_decode($_REQUEST['msg'], true);
if(!empty($registation_ids)) {
	//Google cloud messaging GCM-API url
	$url = 'https://fcm.googleapis.com/fcm/send';
	$fields = array(
		'registration_ids' => $registation_ids,
		'data' => $message,
	);

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

dump($data, $result);
/*
dump($result, $data, $registration_ids, $message);
{"status":"1"}
<pre>-------------------------------------------------------------------------------------------------------------------
</pre>=========&gt;string(143) "{"multicast_id":5529898243055371907,"success":1,"failure":0,"canonical_ids":0,"results":[{"message_id":"0:1503124153109611%e6606c70f9fd7ecd"}]}"
&lt;=========
<pre>
stdClass::__set_state(array(
   'multicast_id' => 5529898243055371907,
   'success' => 1,
   'failure' => 0,
   'canonical_ids' => 0,
   'results' => 
  array (
    0 => 
    stdClass::__set_state(array(
       'message_id' => '0:1503124153109611%e6606c70f9fd7ecd',
    )),
  ),
))=======================================================
Array
(
    [0] => cxmrLje1_q8:APA91bHcqQfz9m9ok9HgIm4YvdXL7JtmVWCKmrMYStbpkq8oGnXIDpsIieJOCZOoa_pqWDjRpDw53gtstpeAj_9i_-jnC0gu8J9ek5dc_zbpS8qH5a1KvNC-v_ZOXwNXj3BeAQRvIMqJ
)
=======================================================
Array
(
    [status] => 1
    [title] => Event has been cancelled
    [type] => 3
    [event_id] => 1998
    [event_name] => Testing New
    [event_details] => New Details
    [event_date] => 02-August-2017
    [event_location] => New Location
    [event_type] => 
    [latitude] => 3
    [longitude] => 4
    [event_city] => 28
    [event_time] => 05:30 AM
    [user_choice] => 1
    [event_going_count] => 1
    [event_invited_count] => 5
    [event_maybe_count] => 0
    [event_cantgo_count] => 0
)
=======================================================
-------------------------------------------------------------------------------------------------------------------</pre>
*/