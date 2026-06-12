<?php
$content = file_get_contents('rendered.html');
echo "Context between first and second copies:\n";
$start = 50000;
$length = 5000;
echo substr($content, $start, $length) . "\n";
