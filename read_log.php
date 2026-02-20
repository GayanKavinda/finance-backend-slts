<?php
$log = file_get_contents('storage/logs/laravel.log');
$entries = array_filter(explode('[2026-', $log));
$last = array_slice($entries, -3);
foreach ($last as $e) {
    echo '[2026-' . substr($e, 0, 400) . "\n---\n";
}
