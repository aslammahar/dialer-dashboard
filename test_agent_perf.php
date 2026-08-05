<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Load saved HTML
$html = file_get_contents(__DIR__ . '/test_agent_perf_full.html');

$lines = preg_split('/\r\n|\r|\n/', $html);

foreach ($lines as $line) {
    $line = strip_tags($line);
    $line = html_entity_decode($line, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $trimmed = trim($line);

    if (!str_starts_with($trimmed, '|') || !str_ends_with($trimmed, '|')) continue;

    $cols = array_map('trim', explode('|', $trimmed));
    $cols = array_values(array_slice($cols, 1, -1));

    if (count($cols) < 30) continue;
    if (str_contains($cols[0], '---') || strtolower($cols[0]) === 'name' || empty($cols[0])) continue;
    if (!is_numeric($cols[4])) continue;

    // Print first non-header row with column indices
    if ($cols[0] === 'Alaska Jacob') {
        echo "Row for Alaska Jacob:\n";
        foreach ($cols as $i => $col) {
            echo "  [$i] = '$col'\n";
        }
        echo "\nTotal cols: " . count($cols) . "\n";
        break;
    }
}
