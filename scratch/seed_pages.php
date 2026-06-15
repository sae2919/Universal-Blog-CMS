<?php

require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Add 'About Us' page
App\Models\Page::firstOrCreate(
    ['slug' => 'about-us'],
    [
        'title' => 'About Us',
        'locale' => 'en',
        'content' => '<p>This is the About Us page content.</p>',
        'status' => 'published',
        'meta_title' => 'About Us',
        'meta_description' => 'Learn more about our team and vision.',
    ]
);

// Add 'Contact Us' page
App\Models\Page::firstOrCreate(
    ['slug' => 'contact-us'],
    [
        'title' => 'Contact Us',
        'locale' => 'en',
        'content' => '<p>Contact us at contact@blogcms.com</p>',
        'status' => 'published',
        'meta_title' => 'Contact Us',
        'meta_description' => 'Get in touch with us.',
    ]
);

echo "Pages seeded successfully!\n";
