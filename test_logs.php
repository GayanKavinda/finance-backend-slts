<?php

use Illuminate\Support\Facades\File;

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$kernel->bootstrap();

$logPath = storage_path('logs/laravel.log');
echo "Log path: " . $logPath . "\n";

if (file_exists($logPath)) {
    echo "File exists.\n";
    $cmd = "powershell -Command \"Get-Content '$logPath' -Tail 100\"";
    echo "Command: " . $cmd . "\n";
    $fileContent = shell_exec($cmd);
    echo "File content length: " . strlen($fileContent) . "\n";
    // echo "First 100 chars: " . substr($fileContent, 0, 100) . "\n";

    $lines = explode("\n", trim($fileContent));
    echo "Line count: " . count($lines) . "\n";

    foreach ($lines as $index => $line) {
        if ($index > 5) break;
        echo "Line $index: " . trim($line) . "\n";

        preg_match('/^\[(?P<time>.*?)\] (?P<env>.*?)\.(?P<type>.*?): (?P<description>.*)$/', $line, $matches);
        if ($matches) {
            echo "  Matched!\n";
        } else {
            echo "  No match.\n";
        }
    }
} else {
    echo "File does not exist.\n";
}
