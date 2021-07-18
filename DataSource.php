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
                foreach ($keys as $id => $key) {
                    $record[$key] = $values[$id];
                }
                array_push($result, $record);
            }

            $values = fgetcsv($file);
        }
        return $result;
    }

}