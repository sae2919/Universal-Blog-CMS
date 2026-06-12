<?php
$html = file_get_contents('http://127.0.0.1:8000/it-courses/learn-python-or-data-science-first');
echo "HTML length from HTTP request: " . strlen($html) . "\n";

$needle = "One question comes up constantly";
$offset = 0;
$count = 0;
while (($pos = strpos($html, $needle, $offset)) !== false) {
    $count++;
    echo "Found '$needle' in live HTML at position $pos\n";
    $offset = $pos + strlen($needle);
}
echo "Total occurrences in live HTML: $count\n";

if ($count > 1) {
    // Let's print the context of the second occurrence
    $second_pos = strpos($html, $needle, strpos($html, $needle) + 1);
    echo "Context of second occurrence:\n";
    echo substr($html, $second_pos - 500, 1000) . "\n";
}
