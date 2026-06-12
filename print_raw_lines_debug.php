<?php

$lines = file('rendered.html');
$start = 338;
$end = 352;

$out = "";
for ($i = $start; $i <= $end; $i++) {
    if (isset($lines[$i - 1])) {
        $out .= $i . ": " . $lines[$i - 1];
    }
}
file_put_contents('debug_lines.txt', $out);
echo "Lines written to debug_lines.txt\n";
