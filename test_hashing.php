<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

use Illuminate\Support\Facades\Hash;
use App\Models\User;

// Bootstrap the application enough for Facades
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

$password = 'password123';
$hashedManually = Hash::make($password);

echo "Original Password: $password\n";
echo "Hashed Manually: $hashedManually\n";

$user = new User();
$user->password = $hashedManually;
echo "User Password (after setting manually hashed): " . $user->password . "\n";

if (Hash::check($password, $user->password)) {
    echo "Check Passed: Passwords match!\n";
} else {
    echo "Check Failed: Passwords do NOT match!\n";
    if (Hash::check($hashedManually, $user->password)) {
        echo "Reason: Double hashing detected.\n";
    }
}

$user2 = new User();
$user2->password = $password;
echo "User Password (after setting plain): " . $user2->password . "\n";
if (Hash::check($password, $user2->password)) {
    echo "Check Passed for plain set: Passwords match!\n";
}
