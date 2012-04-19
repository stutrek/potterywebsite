<?php
require('./db.php');

$result = mysql_query("SELECT * FROM products WHERE available=1 ORDER BY awesomeness DESC, date_added DESC");

if (!$result) {
	echo mysql_error();
}

$output = array();

while($row = mysql_fetch_assoc($result)) {
	$image_result = mysql_query("SELECT * FROM productimages WHERE product_id=$row[id] ORDER BY `order`");
	$row['images'] = array();
	while( $image = mysql_fetch_assoc($image_result) ) {
		$row['images'][] = $image;
	}
	$output[] = $row;
}


header('content-type: application/json');

echo json_encode($output);
?>