<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$post = \App\Models\Post::where('slug', 'learn-python-or-data-science-first')->first();
if ($post) {
    file_put_contents('db_content_dump.html', $post->content);
    echo "Saved DB content to db_content_dump.html\n";
    echo "DB content length: " . strlen($post->content) . "\n";
} else {
    echo "Post not found!\n";
}
