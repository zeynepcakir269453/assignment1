<?php
function cacheContents(array $callLogs): array
{
    $cache = [];
    $memory = [];

    foreach ($callLogs as $log) {
        $timestamp = $log[0];
        $item_id = $log[1];

        if (!isset($memory[$item_id])) {
            $memory[$item_id] = ['priority' => 0, 'access_count' => 0];
        }

        $memory[$item_id]['priority'] = max(0, $memory[$item_id]['priority'] - 1);

        $memory[$item_id]['access_count'] += 1;
        if ($memory[$item_id]['access_count'] > 1) {           
            $memory[$item_id]['priority'] += 2 * ($memory[$item_id]['access_count'] - 1);

        }
        if ($memory[$item_id]['priority'] <= 3 && in_array($item_id, $cache)) {
            $index = array_search($item_id, $cache);
            unset($cache[$index]);
        }


        if ($memory[$item_id]['priority'] > 5) {
            $cache[$item_id] = $item_id;
            $memory[$item_id]['priority'] = 0;
        }

        //echo "Timestamp: $timestamp, Item ID: $item_id, Memory: " . print_r($memory, true) . ", Cache: " . print_r($cache, true) . "\n";
    }

    sort($cache);
    return $cache ? $cache : [-1];
}

$callLogs_rows = intval(trim(fgets(STDIN)));
$callLogs_columns = 2;

$callLogs = [];
for ($i = 0; $i < $callLogs_rows; $i++) {
    $callLogs_temp = rtrim(fgets(STDIN));
    $callLogs[] = array_map('intval', explode(' ', $callLogs_temp));
}

$result = cacheContents($callLogs);
echo implode("\n", $result) . "\n";