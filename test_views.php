<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $h1 = view('retention.index', [
        'filters' => ['from' => '2026-08-01', 'to' => '2026-08-06'],
        'dailyBoard' => [],
        'dailyBoardTotals' => ['rewrite'=>0,'fixed'=>0,'correspondence'=>0,'new_policy'=>0,'total_sales'=>0,'level'=>0,'gi'=>0,'level_pct'=>0,'m_rewrite'=>0,'m_fixed'=>0,'m_corr'=>0,'m_new_policy'=>0,'m_total_sales'=>0,'m_level'=>0,'m_gi'=>0],
        'teamsSummary' => [],
        'clientsSummary' => [],
        'closers' => collect([]),
        'teams' => collect([]),
        'clients' => collect([]),
        'carriers' => collect([]),
        'canEdit' => true,
        'isCloser' => false,
        'lastSyncedAt' => '12:00 PM',
    ])->render();
    echo "retention.index OK: " . strlen($h1) . " bytes\n";

    $h2 = view('retention.attendance', [
        'date' => '2026-08-06',
        'closers' => collect([]),
        'existing' => [],
        'monthlySummary' => [],
        'canEdit' => true,
        'isCloser' => false,
    ])->render();
    echo "retention.attendance OK: " . strlen($h2) . " bytes\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n";
}
