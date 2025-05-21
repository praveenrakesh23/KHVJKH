<?php
require 'vendor/autoload.php';

use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\RoundBlockSizeMode;

echo "ErrorCorrectionLevel enum values:\n";
$reflection = new ReflectionEnum(ErrorCorrectionLevel::class);
foreach ($reflection->getCases() as $case) {
    echo "- " . $case->getName() . "\n";
}

echo "\nRoundBlockSizeMode enum values:\n";
$reflection = new ReflectionEnum(RoundBlockSizeMode::class);
foreach ($reflection->getCases() as $case) {
    echo "- " . $case->getName() . "\n";
}

$dir = 'vendor/endroid/qr-code/src';
echo "Available classes in $dir:\n\n";

function listClasses($dir) {
    $files = scandir($dir);
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') continue;
        $path = $dir . '/' . $file;
        if (is_dir($path)) {
            listClasses($path);
        } elseif (str_ends_with($file, '.php')) {
            $class = str_replace('/', '\\', substr($path, strlen('vendor/endroid/qr-code/src/'), -4));
            if (strpos($class, 'ErrorCorrection') !== false) {
                echo "- $class\n";
            }
        }
    }
}

listClasses($dir);
?> 