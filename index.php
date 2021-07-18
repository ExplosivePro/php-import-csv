<?php
use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();
ini_set('auto_detect_line_endings',TRUE);

function arr_to_query($key_arr, $val_arr) {
    $q = "";
    $delim = "?";
    foreach ($key_arr as $id => $key) {
        $q = $q."$delim$key=$val_arr[$id]";
        $delim = "&";
    }
    return $q;
}

$keys = array();
$values = array();
$result = array();

$fileName = "";

$db_initialized = false;
if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    if ( isset($_FILES["file"])) {

        //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            // echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        }
        else {
            $storagename = "uploaded_file.csv";
            if(file_exists("upload/$storagename")) {
                chmod("upload/$storagename",0755); //Change the file permissions if allowed
                unlink("upload/$storagename"); //remove the file
            }
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/$storagename");
            $db_initialized = true;
        }
    }
}
$search = "";
if (isset($_POST["search_str"])) {
    $db_initialized = true;
}

if($db_initialized) {
    $file = fopen("upload/uploaded_file.csv", "r");
    $keys = fgetcsv($file);
}
?>
<!DOCTYPE html>
<html>

    <head>
        <link rel="stylesheet" type="text/css" href="static/main.css">
        <link rel="stylesheet" type="text/css" href="static/fSelect.css">
    </head>

    <body>
        <h1>Search based on the Column names in the Csv</h1>
        <div class="text-right m-b-10">
            <form class="form-search" method="post">
                <input type="text" name="search_str" />
                
                <select class="columns" multiple="multiple" name="columns[]">
                    <?php foreach($keys as $k => $column) {
                    ?>
                        <option value="<?php echo $column;?>"><?php echo $column;?></option>
                    <?php }?>
                </select>

                <button type="submit" class="search-button">Search</button>
            </form>
        </div>
        <div class="tbl-content">
            <table cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr class="row100 head">
                    <?php
                    $columns = array();
                    if($db_initialized) {
                        $columns =$db->get_columns();
                        if (!empty($columns)) {
                            foreach($columns as $i => $column) {
                                echo "<th class='cell100 column$i'>$column</th>";
                            }
                        }
                    }
                    ?>
                    </tr>
                </thead>   
                <tbody>
                <?php
                    if($db_initialized) {
                        $search = array_key_exists('search_str', $_POST) ? $_POST["search_str"]: "";

                        $values = array();
                        if(array_key_exists('columns', $_POST)) {
                            foreach($_POST["columns"] as $column) {
                                array_push($values, $search);
                            }
                            $result = $db->search($_POST["columns"],$values);
                        } else {
                            $result = $db->search_by_value($search);
                        }
                        if (! empty($result)) {
                ?>
                    <?php foreach ($result as $row) { ?>
                    <tr>
                        <?php foreach ($row as $value) { ?>
                            <td><?php  echo $value; ?></td>
                        <?php } ?>
                    </tr>
                    <?php } ?>
                    <?php 
                    } else {
                        ?>
                    <tr>
                        <td colspan="<?php echo count($columns)?>">No Data</td>
                    </tr>
                <?php 
                } 
            }
            ?>
                </tbody>
            </table>
        </div>
        <div>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" style="padding-right: 50px">
                <div class="input-row pull-right">

                    <label class="button">
                        <input type="file" name="file" id="file" />
                        <span id="fileSelect">
                            Choose File
                        </span>

                    </label>
                    <input class="button" type="submit" name="import" />
                </div>
            </form>
            <?php if(count($result) != 0) {
                $query = "?search=".$search;
                
                if(array_key_exists('columns', $_POST)) {
                    $delim = "?";
                    $query = "";
                    foreach($_POST["columns"] as $column) {
                        $query = $query."$delim$column=$search";
                        $delim = "&";
                    }
                }
                ?>
                
                <a target="_blank" class="button pull-right" href="download.php<?php echo $query; ?>">Export</a>
            <?php } ?>

        </div>
        <script src="static/jquery-3.2.1.min.js"></script>
        <script src="static/fselect.js"></script>
        <script src="static/main.js"></script>
    </body>
</html>