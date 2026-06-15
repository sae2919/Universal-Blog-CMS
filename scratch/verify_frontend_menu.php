<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Simulate request to /
$request = Illuminate\Http\Request::create('/', 'GET');
$response = $app->handle($request);

$html = $response->getContent();

// Let's verify the menu elements
if (strpos($html, 'Blog') !== false) {
    echo "[PASSED] Found 'Blog' in header.\n";
} else {
    echo "[FAILED] Did not find 'Blog' in header.\n";
}

if (strpos($html, '/category/technology') !== false) {
    echo "[PASSED] Found '/category/technology' link in dropdown.\n";
} else {
    echo "[FAILED] Did not find '/category/technology' link in dropdown.\n";
}

if (strpos($html, '/category/programming') !== false) {
    echo "[PASSED] Found '/category/programming' link in dropdown.\n";
} else {
    echo "[FAILED] Did not find '/category/programming' link in dropdown.\n";
}

if (strpos($html, '/about-us') !== false) {
    echo "[PASSED] Found '/about-us' link.\n";
} else {
    echo "[FAILED] Did not find '/about-us' link.\n";
}
