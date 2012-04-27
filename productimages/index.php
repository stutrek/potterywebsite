<?php
$uriArray = explode( '/', $_SERVER['REQUEST_URI'] );
$scriptArray = explode( '/', $_SERVER['SCRIPT_NAME'] );
array_splice( $uriArray, 0, count($scriptArray)-1 );

$imagepath = implode( '/', $uriArray );

if (!file_exists( $imagepath ) ) {
	$filename = basename($imagepath);
	require("../php/image.class.php");
	$image = new image();
	$image_r = $image->load( "5000/$filename" );
	$image_r = $image->resize( $image_r, $uriArray[0], $uriArray[0] );
	
	@mkdir( dirname($imagepath), 0777, true );
	
	$image->save( $image_r, $imagepath );
}

header('content-type: image/jpg');
readfile( $imagepath );

?>