<?
require("php/db_interface.php");
header('content-type: text/plain');

$product = get_product($_GET['id']);

$product['available'] = 0;

print_r($product);

update_product($product);
