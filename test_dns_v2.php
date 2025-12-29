<?php
echo "Starting DNS Check...\n";

$domain = 'abcd.com';
echo "Checking $domain...\n";

// Test A record first (usually faster/more reliable)
$a = checkdnsrr($domain, 'A');
var_dump($a);

// Test MX record
$mx = checkdnsrr($domain, 'MX');
var_dump($mx);

echo "Done Checking abcd.com.\n";

$domain2 = 'google.com';
echo "Checking $domain2...\n";
$mx2 = checkdnsrr($domain2, 'MX');
var_dump($mx2);

echo "Finished.\n";
