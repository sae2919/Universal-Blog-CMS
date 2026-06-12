<?php
$content = file_get_contents('rendered.html');
$pos = strpos($content, '35:');
if ($pos !== false) {
    echo "Found '35:' at $pos:\n";
    echo substr($content, $pos - 100, 200) . "\n";
} else {
    echo "'35:' not found\n";
}
