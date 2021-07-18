<?php
use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();
$conn = $db->getConnection();
ini_set('auto_detect_line_endings',TRUE);
$query = "";

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
$fileName = "";

if (isset($_POST["import"])) {
    
    $fileName = $_FILES["file"]["tmp_name"];
    if ( isset($_FILES["file"])) {

        //if there was an error uploading the file
        if ($_FILES["file"]["error"] > 0) {
            echo "Return Code: " . $_FILES["file"]["error"] . "<br />";

        }
        else {
            $storagename = "uploaded_file.csv";
            if(file_exists("upload/$storagename")) {
                chmod("upload/$storagename",0755); //Change the file permissions if allowed
                unlink("upload/$storagename"); //remove the file
            }
            move_uploaded_file($_FILES["file"]["tmp_name"], "upload/$storagename");
        }
    } else {
            echo "No file selected <br />";
    }
}

if (isset($_POST["search_key"])) {
    $fileName = $_FILES["file"]["tmp_name"];

    if ($_FILES["file"]["size"] > 0) {
        $file = fopen($fileName, "r");
        
        $keys = fgetcsv($file);
        $values = fgetcsv($file);

        $query = "SELECT * FROM employers WHERE 1=1";

        for ($i = 0; $i < count($keys) ; $i ++) {
            $keys[$i] = $keys[$i] === 'EmployeeID' ? 'id': $keys[$i];
            $query = $query." AND $keys[$i]='$values[$i]'";
        }
    }
}
?>
<!DOCTYPE html>
<html>

    <head>
        
        <link rel="stylesheet" type="text/css" href="static/main.css">
    </head>

    <body>
        <h1>Search based on the Column names in the Csv</h1>        
        <div class="tbl-header">
            <table cellpadding="0" cellspacing="0" border="0">
                <thead>
                    <tr class="row100 head">
                        <th class="cell100 column1">Employee ID</th>
                        <th class="cell100 column2">User Name</th>
                        <th class="cell100 column3">Department</th>
                        <th class="cell100 column4">Mail</th>
                        <th class="cell100 column5">DptCode</th>
                        <th class="cell100 column6">License</th>
                    </tr>
                </thead>
            </table>
        </div>
        <div class="tbl-content">

            <table cellpadding="0" cellspacing="0" border="0">
                <?php
                    if($query != "") {
                        // $result = $db->select($query);
                        $result = $db->search($keys, $values);

                        if (! empty($result)) {
                ?>
                <tbody>
                    <?php foreach ($result as $row) { ?>
                    <tr>
                        <td><?php  echo $row['id']; ?></td>
                        <td><?php  echo $row['UserName']; ?></td>
                        <td><?php  echo $row['Department']; ?></td>
                        <td><?php  echo $row['Mail']; ?></td>
                        <td><?php  echo $row['DepartmentCode']; ?></td>
                        <td><?php  echo $row['PrimaryLicense']; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
                <?php 
                    } else {
                ?>
                <tbody>
                    <tr>
                        <td colspan="6" style="text-align: center">No Data</td>
                    </tr>
                </tbody>
            <?php 
                } 
            }
            ?>
            </table>
        </div>
        <div>
            <form class="form-horizontal" action="" method="post"
                name="searchCsv" id="searchCsv"
                enctype="multipart/form-data">
                <div class="input-row pull-right">
                    <label class="button">
                        <input type="file" name="file" id="searchFile" accept=".csv" onchange="handleChange(event)">
                        <span id="fileSelect">
                            Select Csv
                        </span>
                    </label>
                    <button type="submit" id="submit" name="search_key" class="button">
                        <?php 
                            if(count($keys) != 0) {
                                echo "Reset";
                            } else {
                                echo "Confirm";
                            }
                            
                        ?>
                    </button>
                    <br />
                </div>
            </form>
            <form class="form-horizontal" action="" method="post" enctype="multipart/form-data" style="padding-right: 50px">
                <div class="input-row pull-right">

                    <label class="button">
                        <input type="file" name="file" id="file" />
                        <span id="fileSelect">
                            Upload Csv
                        </span>

                    </label>
                    <input class="button" type="submit" name="import" />
                </div>
            </form>
            <?php if(count($keys) != 0) {?>
            <a target="_blank" class="button pull-right" href="download.php<?php echo arr_to_query($keys, $values);?>">Export</a>
            <?php } ?>

        </div>
        <script src="static/jquery-3.2.1.min.js"></script>
        <script src="static/main.js"></script>
    </body>
</html>