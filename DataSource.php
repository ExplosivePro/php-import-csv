<?php
/**
 * Copyright (C) 2019 Phppot
 *
 * Distributed under MIT license with an exception that,
 * you don’t have to include the full MIT License in your code.
 * In essense, you can use it on commercial software, modify and distribute free.
 * Though not mandatory, you are requested to attribute this URL in your code or website.
 */
namespace Phppot;

/**
 * Generic datasource class for handling DB operations.
 * Uses MySqli and PreparedStatements.
 *
 * @version 2.5 - recordCount function added
 */
class DataSource
{
    public function search($key_arr, $val_arr) {
        $file = fopen("upload/uploaded_file.csv", "r");
        
        $keys = fgetcsv($file);

        $values = fgetcsv($file);
        $result = array();

        while ($values) {
            $flag = true;
            foreach ($key_arr as $i => $key) {
                $key_id = array_search($key, $keys);
                
                $flag = $flag && $values[$key_id] == $val_arr[$i];
            }
            if ($flag) {
                $record = array();
                array_push($result, $values);
            }

            $values = fgetcsv($file);
        }
        return $result;
    }

    public function search_by_value($val, $offset = -1 ) {
        $file = fopen("upload/uploaded_file.csv", "r");
        
        $keys = fgetcsv($file);
        $values = fgetcsv($file);
        
        $offset = $offset * 30;
  
        $result = array();
        $counter = 0;
        $buf = 0;

        while ($values && ($counter < 30 || $offset < 0)) {
    
            $flag = false;
            foreach ($values as $value) {  
                $flag = $flag || !strcasecmp($value, $val) || !$val;
            }
            if ($flag) {
                if ($buf < $offset) {
                    $values = fgetcsv($file);
                    $buf++;
                } else {
                    $record = array();
                    foreach ($keys as $id => $key) {
                        $record[$key] = $values[$id];
                    }
                    array_push($result, $record);
                    $counter++;
                }
            }
            $values = fgetcsv($file);
            
        }        
        return $result;
    }

    public function get_columns() {
        $file = fopen("upload/uploaded_file.csv", "r");
        
        $keys = fgetcsv($file);

        return $keys;
    }
    public function get_summary($data, $numeric_keys = array()) {
        $file = fopen("upload/uploaded_file.csv", "r");
        $columns = fgetcsv($file);
        
        $summary = array();
        
        foreach ($columns as $key => $value) {
            $summary[$value] = '';            
        }

        foreach ($numeric_keys as $key => $value) {
            $summary[$value] = 0;            
        }
        
        $counter = 0;
        foreach ($data as $key => $row) {
            foreach ($columns as $field) {
                if (is_numeric($row[$field]) && is_numeric($summary[$field])) {
                    $summary[$field] += $row[$field];
                } else if(!$counter && !is_numeric($summary[$field])) {
                    $summary[$field] = "summary";
                }
                $counter++;
            }
        }

        return $summary;
    }
}