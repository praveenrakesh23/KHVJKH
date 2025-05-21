<?php
require 'vendor/autoload.php';

use Endroid\QrCode\QrCode;

$reflection = new ReflectionClass(QrCode::class);
echo "QR Code Library Version: " . $reflection->getFileName() . "\n\n";
echo "Constructor Parameters:\n";
$constructor = $reflection->getConstructor();
foreach ($constructor->getParameters() as $param) {
    echo "- " . $param->getName() . " (type: " . ($param->getType() ? $param->getType()->getName() : 'mixed') . ")\n";
}

echo "\nAvailable Methods:\n";
foreach ($reflection->getMethods() as $method) {
    echo "- " . $method->getName() . "(";
    $params = [];
    foreach ($method->getParameters() as $param) {
        $params[] = $param->getName() . ($param->isOptional() ? '?' : '');
    }
    echo implode(', ', $params) . ")\n";
}
?> 