<?php
$domain = 'definitely-non-existent-domain-12345-xyz.com';
echo "Checking $domain...\n";

// Test A record
$a = checkdnsrr($domain, 'A');
var_dump($a);

// Test MX record
$mx = checkdnsrr($domain, 'MX');
var_dump($mx);

echo "Finished.\n";
