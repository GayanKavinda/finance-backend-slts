<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$k = $app->make(Illuminate\Contracts\Console\Kernel::class);
$k->bootstrap();

try {
    $tenders = App\Models\Tender::with('customer')->withCount('jobs')->latest()->paginate(5);
    echo "Tenders OK! Count: " . $tenders->total() . "\n";
} catch (\Exception $e) {
    echo "Tenders Error: " . $e->getMessage() . "\n";
}

try {
    $jobs = App\Models\ProjectJob::with(['tender', 'customer'])->withCount('purchaseOrders')->latest()->paginate(5);
    echo "Jobs OK! Count: " . $jobs->total() . "\n";
} catch (\Exception $e) {
    echo "Jobs Error: " . $e->getMessage() . "\n";
}
