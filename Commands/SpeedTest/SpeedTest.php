<?php

# /usr/local/bin/ /usr/bin/
exec('/usr/local/bin/speedtest-cli --simple', $output);

$replacers = ["Ping:", "ms", "Download:", "Upload:", "Mbit/s"];

// convert to useful array
$json['value1'] = trim(str_replace($replacers, '', $output[0]));
$json['value2'] = trim(str_replace($replacers, '', $output[1]));
$json['value3'] = trim(str_replace($replacers, '', $output[2]));

$config = include('config.php');
$key = $config['key'];
$event = $config['event'];

//IFTTT URL
$url = 'https://maker.ifttt.com/trigger/'.$event.'/with/key/'.$key;

//Initiate cURL.
$ch = curl_init($url);

//Tell cURL that we want to send a POST request.
curl_setopt($ch, CURLOPT_POST, 1);

//Attach our encoded JSON string to the POST fields.
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($json));

//Set the content type to application/json
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

//Execute the request
$result = curl_exec($ch);

echo( $result ) . PHP_EOL;
