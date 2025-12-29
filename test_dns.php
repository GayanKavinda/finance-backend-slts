<?php
$domain = 'abcd.com';
$mxUrl = checkdnsrr($domain, 'MX');
$mxHost = checkdnsrr($domain, 'A'); // Fallback check often used by checkdnsrr
echo "Checking $domain:\n";
echo "MX Record Exists: " . ($mxUrl ? 'YES' : 'NO') . "\n";
echo "A Record Exists: " . ($mxHost ? 'YES' : 'NO') . "\n";

$trusted = ['gmail.com'];
echo "Trusted Check: " . (in_array($domain, $trusted) ? 'YES' : 'NO') . "\n";
