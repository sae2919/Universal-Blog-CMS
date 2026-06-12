<?php

// Make a request to the local Laravel server
$html = file_get_contents('http://127.0.0.1:8000/it-courses/learn-python-or-data-science-first');
if ($html === false) {
    die("Error fetching page\n");
}

file_put_contents('rendered.html', $html);
echo "Rendered HTML saved to rendered.html (Length: " . strlen($html) . " bytes)\n";

// Count tags
$divOpen = substr_count($html, '<div');
$divClose = substr_count($html, '</div');
echo "Overall page Div: Open=$divOpen, Close=$divClose\n";

libxml_use_internal_errors(true);
$dom = new DOMDocument();
$dom->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
$errors = libxml_get_errors();
foreach ($errors as $error) {
    echo "Libxml Error: " . trim($error->message) . " on line " . $error->line . "\n";
}
libxml_clear_errors();
