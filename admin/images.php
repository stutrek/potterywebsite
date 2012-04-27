<?
ini_set( 'upload_max_filesize', 50000000000 );

require_once( 'includes/initialize.php' );
require_once( 'admin_functions.php');

if( isset( $_REQUEST['save'] ) ) {
	
	$id = $_REQUEST['product_id'];
	
	send_to( 'products.php' );
	
}

$product_a = select( 'products', array( 'id' => $_REQUEST['product_id'] ), '*', 1 );
if( isset( $_REQUEST['update_images'] ) ) {
	update( 'products', array( 'image_id' => $_REQUEST['main'] ), array( 'id' => $product_a['id'] ), 1 );
	$product_a['image_id'] = $_REQUEST['main'];
}

if( isset( $_REQUEST['add_image'] ) ) {
	for( $i = 0; $i < count( $_FILES['images']['name'] ); $i++ ) {
		if ($_FILES['images']['tmp_name'][$i]) {
			$worked = add_image(  $_FILES['images']['tmp_name'][$i], $product_a );
			if( !is_numeric( $worked ) ) {
				$error[] = $worked;
			} else {
				if( $product_a['image_id'] == '0' ) {
					update( 'products', array( 'image_id' => $worked ), array( 'id' => $product_a['id'] ), 1 );
					$product_a['image_id'] = $worked;
				}
			}
		}
	}
}

if( isset( $_REQUEST['delete'] ) ) {
	$image = select( 'productimages', array( 'id' => $_REQUEST['delete'] ), '*', 1 );
	$product_a = select( 'products', array( 'id' => $image['product_id'] ), '*', 1 );
	dump($image);
	
	$dirs = scandir("../productimages");
	foreach( $dirs as $dir ) {
		if (file_exists("../productimages/".$dir."/".$image['filename'])) {
			unlink("../productimages/".$dir."/".$image['filename']);
		}
	}
	
	delete( 'productimages', array( 'id' => $image['id'] ), 1 );
	
	if( $product_a['image_id'] == $image['id'] ) {
		$new_image_id = select( 'productimages', array( 'product_id' => $product_a['id'] ), 'id', 1 );
		if( $new_image_id ) {
			update( 'products', array( 'image_id' => $new_image_id ), array( 'id' => $product_a['id'] ), 1 );
			$product_a['product'] = $new_image_id;
		} else {
			update( 'products', array( 'image_id' => '0' ), array( 'id' => $product_a['id'] ), 1 );
		}
	}
	send_to( 'admin/images.php?product_id='.$image['product_id'] );
}

$images = select( 'productimages', array( 'product_id' => $_REQUEST['product_id'] ) );

?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" rev="stylesheet" href="admin.css" charset="utf-8">
	<script src="../js/lib/jquery.js" type="text/javascript" language="javascript" charset="utf-8"></script>

	<title>Treat Couture product Editor</title>
	<script type="text/javascript">
		//<![CDATA[
		var image_inputs = 1;
		function add_image_input() {
			$('#image_inputs').append( '<table><tr><td class="input_label">image<'+'/td><td><input type="file" name="images['+image_inputs+']" /><'+'/td><'+'/tr><'+'/table>' );
			image_inputs++;
		}
		//]]>
	</script>
</head>
<body>
	<?
	if( count( $error ) > 0 ) {
		echo '<div class="error">'.implode( '<br />', $error ).'</div>';
	}
	?>
	<form action="" method="post">
		<fieldset>
			<legend>Edit Images</legend>
						
			<?
			foreach( $images as $key => $image ) {
				if( $product_a['image_id'] == $image['id'] ) {
					$main_selected = 'checked="checked"';
				} else {
					$main_selected = '';
				}
				if( $product_a['thumb_id'] == $image['id'] ) {
					$thumb_selected = 'checked="checked"';
				} else {
					$thumb_selected = '';
				}
				echo '<table>
						<tr>
							<td rowspan="2">
								<input type="radio" name="main" value="'.$image['id'].'" '.$main_selected.' /> main<br>
							</td>
							<td rowspan="2"><img src="../'.THUMB_PATH.$image['filename'].'" alt=""></td>
							<td><a href="?delete='.$image['id'].'" onclick="return confirm( \'Are you sure you want to delete the '.$image['view'].' image?\' )">delete</a></td>
						</tr>
						
					</table>';
			}
			?>
			<input type="submit" name="update_images" value="Update Images" />
		</fieldset>
	</form>
	<form action="" method="post" enctype="multipart/form-data">
		<fieldset>
			<legend>Add an Image:</legend>
			<a href="javascript:add_image_input();">add another image form</a><br />
			<div id="image_inputs">
				<table>
					<tr><td class="input_label">image</td><td><input type="file" name="images[0]" /></td></tr>
				</table>
			</div>
			<input type="submit" name="add_image" value="Add Image" />
		</fieldset>
	</form>
</body>
</html>
