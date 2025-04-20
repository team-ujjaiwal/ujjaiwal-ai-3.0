<?php
// edith.php

$botToken = "7746509381:AAEtrmCbWqyBMyoopzf33SUa7OaAzqgo-68";
$openaiKey = "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";
$apiURL = "https://api.telegram.org/bot$botToken/";

// Read the incoming Telegram update
$update = json_decode(file_get_contents("php://input"), true);
if (!isset($update["message"]["text"])) {
    exit();
}

$chatId = $update["message"]["chat"]["id"];
$userMessage = $update["message"]["text"];

// Create OpenAI request
$payload = json_encode([
    "model" => "gpt-3.5-turbo",
    "messages" => [
        ["role" => "user", "content" => $userMessage]
    ]
]);

$ch = curl_init("https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer $openaiKey"
]);

$response = curl_exec($ch);
curl_close($ch);

if (!$response) {
    sendTelegram("Sorry, I couldn't reach the AI server.", $chatId, $apiURL);
    exit();
}

$result = json_decode($response, true);
$botReply = $result["choices"][0]["message"]["content"] ?? "I'm not sure how to reply.";

sendTelegram($botReply, $chatId, $apiURL);

// Function to send message
function sendTelegram($message, $chatId, $apiURL) {
    $url = $apiURL . "sendMessage?chat_id=$chatId&text=" . urlencode($message);
    file_get_contents($url);
}