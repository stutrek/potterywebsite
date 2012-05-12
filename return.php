<?
require("php/db_interface.php");
header('content-type: text/plain');

$product = get_product($_GET['product']);
$product->available = 0;
update_product($product);
