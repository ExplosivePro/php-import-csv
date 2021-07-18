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
foreach ($_REQUEST as $key => $value) {
    $query = "SELECT * FROM employers WHERE 1=1";
    $key = $key === 'EmployeeID' ? 'id': $key;
    $query = $query." AND $key='$value'";
}
$result = $db->search(array_keys($_REQUEST), array_values($_REQUEST));
array_to_csv_download($result);
