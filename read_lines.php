<?php

$lines = file('rendered.html');
$start = 580;
$end = 650;

for ($i = $start; $i <= $end; $i++) {
    if (isset($lines[$i - 1])) {
        echo $i . ": " . htmlspecialchars($lines[$i - 1]);
    }
}
