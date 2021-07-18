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

if (!empty(array_keys( $_REQUEST))) {
    if (array_key_exists("search", $_REQUEST)) {
        $search = $_REQUEST["search"];
        $result = $db->search_by_value($search);
    } else {
        $result = $db->search(array_keys( $_REQUEST), array_values( $_REQUEST));
    }

} else {
    $result = $db->search_by_value("");
}
array_to_csv_download($result);
