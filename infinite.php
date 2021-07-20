<?php 
use Phppot\DataSource;

require_once 'DataSource.php';
$db = new DataSource();

$result = $db->search_by_value($_POST["search"], $_POST["offset"]);

echo json_encode($result);