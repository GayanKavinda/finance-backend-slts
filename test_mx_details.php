<?php
$domain = 'abcd.com';
echo "Checking MX details for $domain...\n";

$hosts = [];
$weights = [];
$result = getmxrr($domain, $hosts, $weights);

echo "Result: " . ($result ? 'TRUE' : 'FALSE') . "\n";
echo "Hosts Count: " . count($hosts) . "\n";

if (!empty($hosts)) {
    echo "MX Records:\n";
    foreach ($hosts as $index => $host) {
        echo " - " . $host . " (Weight: " . ($weights[$index] ?? 'N/A') . ")\n";
    }
} else {
    echo "No MX hosts returned.\n";
}
