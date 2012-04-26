<?php
require('db_interface.php');

$output = get_all_products();

header('content-type: application/json');

echo json_encode($output);
?>