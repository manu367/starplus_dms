<?php
require_once("../config/config.php");

$keyword = $_REQUEST['message'];

$systemPrompt = "
You are a software Enginner

Rules:
1. when anybody give you a task you complete in code and give only code withour any extra text
2. when user specific any programming langauge then write on those programing langauge otherwise programming language is not specific then write code in javascript
3. try to write code in better manger and professional way and write code like a professioanl way
";
/* ===============================
   5. OPENAI REQUEST
================================ */
$requestData = [
    "model" => "gpt-4o-mini",
    "temperature" => 0,
    "messages" => [
        ["role" => "system", "content" => $systemPrompt],
        ["role" => "user", "content" => $keyword]
    ]
];

$ch = curl_init("https://api.openai.com/v1/chat/completions");


curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode([
        "model" => "gpt-4o-mini",
        "temperature" => 0,
        "messages" => [
            ["role" => "system", "content" => $systemPrompt],
            ["role" => "user", "content" => $keyword]
        ]
    ]),
    CURLOPT_HTTPHEADER => [
        "Content-Type: application/json",
        "Authorization: Bearer $apiKey"
    ]
]);

$response = curl_exec($ch);
if (curl_errno($ch)) {
    curl_close($ch);
    echo json_encode([
        "choices" => [[
            "message" => ["content" => "Server error. Please try again later."]
        ]]
    ]);
    exit;
}
curl_close($ch);
echo $response;
?>
