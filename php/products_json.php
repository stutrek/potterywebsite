<?php
require('./db_interface.php');

$output = get_available_products();

header('content-type: application/json');

echo json_encode($output);
?>