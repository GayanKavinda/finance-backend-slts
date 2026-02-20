<?php
$token = null;
$ch = curl_init('http://localhost:8000/api/login');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode(['email' => 'admin@finance.com', 'password' => 'password']),
    CURLOPT_HTTPHEADER => ['Content-Type: application/json', 'Accept: application/json'],
    CURLOPT_RETURNTRANSFER => true,
]);
$res = json_decode(curl_exec($ch), true);
$token = $res['token'] ?? null;
curl_close($ch);

$ch = curl_init('http://localhost:8000/api/admin/roles');
curl_setopt_array($ch, [
    CURLOPT_HTTPHEADER => ['Accept: application/json', 'Authorization: Bearer ' . $token],
    CURLOPT_RETURNTRANSFER => true,
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

echo "Status: $code\n";
echo substr($resp, 0, 600) . "\n";
