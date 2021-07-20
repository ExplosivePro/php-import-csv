<?php

use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();

function array_to_csv_download($array, $filename = "export.csv", $delimiter=",") {
    $f = fopen('php://memory', 'w');
    $key_printed = false;

    foreach ($array as $line) {
        if (!$key_printed) {
            fputcsv($f, array_keys($line), $delimiter);
            $key_printed = true;
        }
        fputcsv($f, $line, $delimiter); 
    }
    fseek($f, 0);
    header('Content-Type: application/csv');
    header('Content-Disposition: attachment; filename="'.$filename.'";');
    fpassthru($f);
}

$search = array_key_exists("search", $_REQUEST) ? $_REQUEST["search"]: "";
$numeric_keys = array_key_exists("numerics", $_REQUEST) ? $_REQUEST["numerics"]: array();
$result = $db->search_by_value($search, -1);
$summary = $db->get_summary($result, $numeric_keys);

if (!empty($numeric_keys)) {
    array_push($result, $summary);
}
array_to_csv_download($result);
