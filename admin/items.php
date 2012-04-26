<?
require_once( 'includes/initialize.php' );
require_once( 'admin_functions.php');
if( isset( $_REQUEST['save'] ) ) {
	
	$item_a = $_REQUEST['item'];
	$item_a['on_sale'] = isset( $item_a['on_sale'] );
	$item_a['sold_out'] = isset( $item_a['sold_out'] );
	
	if( is_numeric( $_REQUEST['id'] ) ) {
		$id = $_REQUEST['id'];
	} else {
		$id = false;
	}
	
	$item_a['price'] = preg_replace( '/[^\d\.]/', '', $item_a['price'] );
		
	if( $id ) {
		update( 'products', $item_a, array( 'id' => $id ), 1 );
	} else {
		$new_id = insert( 'products', $item_a );
		$item_a['id'] = $new_id;
	}
	
	$attributes = $_REQUEST['attrs'];
	
	for( $i = 0; $i < count( $_REQUEST['attributes'] ); $i++ ) {

		$attributes[trim($_REQUEST['attributes'][$i])] = trim( $_REQUEST['values'][$i] );
		
	}
	if( isset( $new_id ) ) {
		for( $i = 0; $i < count( $_REQUEST['images'] ); $i++ ) {
			foreach( $_FILES['images'] as $key => $val ) {
				$file[$key] = $_FILES['images'][$key][$i];
			}
			$worked = add_image( $file['tmp_name'], $item_a, $_REQUEST['images'][$i] );
			if( !is_numeric( $worked ) ) {
				$error[] = $worked;
			} elseif( $i == 0 ) {
				update( 'products', array( 'image_id' => $worked, 'thumb_id' => $worked ), array( 'id' => $new_id ), 1 );
				$item_a['image_id'] = $worked;
			}
		}
	}
	
	
	
	header( 'location:items.php' );
	exit;
	
}

if( isset( $_REQUEST['delete'] ) ) {
	$images = select( 'productimages', array( 'item_id' => $_REQUEST['delete'] ) );
	foreach( $images as $image ) {
		unlink( ROOT_PATH.THUMB_PATH.$image['file_name'] );
		unlink( ROOT_PATH.MEDIUM_IMAGE_PATH.$image['file_name'] );
		unlink( ROOT_PATH.LARGE_IMAGE_PATH.$image['file_name'] );
	}
	if( $images ) {
		delete( 'productimages', array( 'item_id' => $_REQUEST['delete'] ), count( $images ) );
	}
	delete( 'products', array( 'id' => $_REQUEST['delete'] ), 1 );
	send_to( 'admin/items.php' );

}

if( isset( $_REQUEST['id'] ) ) {
	$item_a = select( 'products', array( 'id' => $_REQUEST['id'] ), '*', 1 );
}

if( !$item_a ) {
	$products = select( 'products, productimages', array( 'productimages.id' => 'sql:image_id' ), 'products.id, title, type, filename' );
	$products = link_array( $products, 'products' );
	$products_without_images = link_array( select( 'products', array( 'image_id' => '0' ), 'id, title, type' ), 'products' );
}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" rev="stylesheet" href="admin.css" charset="utf-8">
	<script src="../js/lib/jquery.js" type="text/javascript" language="javascript" charset="utf-8"></script>
	<title>Untitled</title>
	<script type="text/javascript">
		//<![CDATA[
		var image_inputs = 1;
		function add_image_input() {
			$('#image_inputs').append( '<table><tr><td class="input_label">image<'+'/td><td><input type="file" name="images['+image_inputs+']" size="20" /><'+'/td><'+'/tr><tr><td class="input_label">type<'+'/td><td><input type="text" name="images['+image_inputs+'][view]" size="20" /><'+'/td><'+'/tr><tr><td class="input_label">description<'+'/td><td><textarea name="images['+image_inputs+'][description]"  rows="3" cols="60"><'+'/textarea><'+'/td><'+'/tr><'+'/table>' );
			image_inputs++;
		}
		//]]>
	</script>
</head>
<body>
	<?
	if( isset( $_REQUEST['new'] ) or $item_a ) { ?>

		<h1>products</h1>
		<form action="?" method="post" enctype="multipart/form-data">
			<fieldset>
				<table>
					<tr><td class="input_label">URL</td><td><input type="text" name="item[url]" value="<?= $item_a['url'];?>"></td></tr>
					<tr><td class="input_label">Title</td><td><input type="text" name="item[title]" value="<?= $item_a['title'];?>"></td></tr>
					<tr><td class="input_label"></td>
						<td>
							<input type="checkbox" name="item[available]" id="available" <?= is_checked( $item_a['available'] ); ?>> <label for="on_sale"> Available</label><br/>
						</td>
					</tr>
					<tr><td class="input_label">Price</td><td>$<input type="text" name="item[price]" size="7" value="<?= $item_a['price'];?>"></td></tr>
					<tr><td class="input_label">type</td><td><input type="text" name="item[section]" value="<?= $item_a['section'];?>"></td></tr>
					<tr><td class="input_label">Description</td><td><textarea name="item[description]" rows="5" cols="50"><?= $item_a['description']; ?></textarea>
					<? if( !isset( $_REQUEST['new'] ) ) { ?>
					<tr><td class="input_label"><? if( $_REQUEST['id'] ) { echo '<input type="hidden" name="id" value="'.$_REQUEST['id'].'">'; } ?>
						<td>
							<input type="submit" name="save" value="Save">	
						</td>
					</tr>
					<? } ?>
					
				</table>
				<? if( isset( $_REQUEST['new'] ) ) { ?>
					<a href="javascript:add_image_input();">add another image form</a><br />
					<div id="image_inputs">
						<table>
							<tr><td class="input_label">image</td><td><input type="file" name="images[0]" size="20" /></td></tr>
							<tr><td class="input_label">view</td><td><input type="text" name="images[0][view]" size="20" /></td></tr>
							<tr><td class="input_label">description</td><td><textarea name="images[0][description]" rows="3" cols="60"></textarea></td></tr>
						</table>
					</div>
					<input type="submit" name="save" value="Save">
				<? } ?>
			</fieldset>
		</form>
		
		
	<? 
	} else { ?>
		 <a href="items.php?new">new item</a><br>
		 <?
		 if( $products_without_images ) {
			 echo '<h3>products without Images</h3>';
			 table( $products_without_images );
		}		
		echo '<h3>products</h3>';
		table( $products );
	}
	?>
</body>
</html>
