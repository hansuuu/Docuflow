<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Checking for database locks and open transactions...\n";

try {
    // Check for locks
    $locks = DB::select("SHOW OPEN TABLES WHERE In_use > 0");
    
    if (count($locks) > 0) {
        echo "Tables with locks:\n";
        foreach ($locks as $lock) {
            print_r($lock);
        }
    } else {
        echo "No locked tables found.\n";
    }
    
    // Check for open transactions
    $processes = DB::select("SHOW PROCESSLIST");
    
    $openTransactions = [];
    foreach ($processes as $process) {
        if (stripos($process->Info, 'begin') !== false || 
            stripos($process->Info, 'start transaction') !== false) {
            $openTransactions[] = $process;
        }
    }
    
    if (count($openTransactions) > 0) {
        echo "\nOpen transactions:\n";
        foreach ($openTransactions as $transaction) {
            print_r($transaction);
        }
    } else {
        echo "\nNo open transactions found.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}