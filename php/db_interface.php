<?php
require("db_setup.php");

$product_fields = array( 'title', 'description', 'type', 'price', 'available', 'awesomeness' );

function create_safe_product( $product ) {
	global $product_fields;
	$safe_product = array();
	foreach( $product_fields as $field ) {
		if (!isset($product[$field])) {
			die('product is missing field '.$field);
		}
		$safe_product[$field] = mysql_real_escape_string($product[$field]);
	}
	return $safe_product;
}

function create_clause( $fields ) {
	$query_string = '';
	foreach($fields as $key => $val) {
		$query_string .= " `$key`='$val.',";
	}
	return substr( $query_string, 0, strlen($query_string)-1 );
}

function get_all_products_from_result( $result ) {
	if (!$result) {
		echo mysql_error();
	}
	
	$return = array();
	
	while($row = mysql_fetch_assoc($result)) {
		$image_result = mysql_query("SELECT * FROM productimages WHERE product_id=$row[id] ORDER BY (ID = $row[image_id])");
		$row['images'] = array();
		while( $image = mysql_fetch_assoc($image_result) ) {
			$row['images'][] = $image;
		}
		if( $row['images'] ) {
			$return[] = $row;
		}
	}
	
	return $return;

}

function get_available_products() {
	$result = mysql_query("SELECT * FROM products WHERE available=1 ORDER BY awesomeness DESC, date_added DESC");
	return get_all_products_from_result( $result );
}

function get_all_products() {
	$result = mysql_query("SELECT * FROM products ORDER BY date_added DESC");
	return get_all_products_from_result( $result );
}

function get_product($id) {
	$result = mysql_query("SELECT * FROM products WHERE id=$id LIMIT 1");
	$products = get_all_products_from_result( $result );
	return $products[0];
}
function add_product( $product ) {
	$db_product = create_safe_product($product);
	$db_product['date_added'] = 'now()';
	
	$query_string = "INSERT INTO `products` SET";
	$query_string .= create_clause( $db_product );
	
	mysql_query($query_string) or die("Error inserting product. Query was: $query_string");
	
	return mysql_insert_id();
}

function update_product( $product ) {
	if( !is_numeric($product['id']) ) {
		die('Tried to update a product with an invalid id!');
	}
	
	$db_product = create_safe_product($product);
	
	$query_string = 'UPDATE `products` SET';
	$query_string .= create_clause( $db_product );
	
	$query_string .= " WHERE `id`=$product[id] LIMIT 1";
	echo $query_string;
	mysql_query($query_string);
}

function delete_product( $id ) {
	if( !is_numeric($id) ) {
		die('Tried to delete a product with an invalid id!');
	}
	mysql_query("DELETE FROM `products` WHERE id=$id LIMIT 1");
}

?>