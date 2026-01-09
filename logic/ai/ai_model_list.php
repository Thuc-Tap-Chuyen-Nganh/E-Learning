<?php
$apiKey = "AIzaSyBBr1Rx6764KqdjpY7LcrTf2a1FHDTTFVY";

$url = "https://generativelanguage.googleapis.com/v1beta/models?key=" . $apiKey;

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true
]);

$response = curl_exec($curl);

if (curl_errno($curl)) {
    echo "Lỗi CURL: " . curl_error($curl);
    exit;
}

curl_close($curl);

header("Content-Type: application/json; charset=utf-8");
echo $response;
