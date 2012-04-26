<?

if( !$_SESSION['is_admin'] ) {
	echo '<a href="index.php">Log back in</a>';
}

function link_array( $a, $type='' ) {
	foreach( $a as $a_key => $b ) {
		switch( $type ) {
			case 'products':
				$c['image'] = '<img src="../'.THUMB_PATH.$b['filename'].'">';
				unset( $b['file_name'] );
		}
		foreach( $b as $b_key => $val ) {
			switch( strtolower( $b_key ) ) {
				
				
			}
			$c[$b_key] = $val;
		}
		switch( $type ) {
			case 'products':
				$c['edit'] = '<a href="items.php?id='.$b['id'].'">edit</a>';
				$c['images'] = '<a href="images.php?product_id='.$b['id'].'">images</a>';
				$c['delete'] = '<a href="items.php?delete='.$b['id'].'" onclick="confirm(\'Are you sure you want to delete '.preg_replace( '/[\W ]/', '', $b['title'] ).'?\')">delete</a>';
		}
		$a[$a_key] = $c;
	}
	return $a;
}

// adds a checkbox to the beginning of an array 
// $name is the name of the HTML element
// $value_key is the key that will be used for the value of the HTML element 
function add_checkbox( $a, $name='id', $value_key='id' ) {
	foreach( $a as $key => $val ) {
		$a[$key] = array( 'chk' => '<input type="checkbox" name="'.$name.'[]" value="'.$val[$value_key].'" class="checkbox">' ) + $val;
	}
	return $a;
}

// email the users
// $recipients is 'all' or 'list'

function is_checked( $value, $compare=true ) {
	if( $value != $compare ) {
		return '';
	}
	return 'checked="checked"';
}


function add_image( $tmp_path, $item_a ) {
	require_once( 'includes/image.class.php' );
	$feedback = true;
	
	$image = new image();
	$image_r = $image->load( $tmp_path );
	
	if( $image_r ) {
		$image_r = $image->resize( $image_r, 5000, 5000 );
		
		$id = insert( 'productimages', array( 'product_id' => $item_a['id'] ) );
		$file_name = $item_a['id'].'_'.$id.'.jpg';
		update( 'productimages', array('filename' => $file_name ), array('id' => $id), 1 );
		$destination_path = ROOT_PATH.HUGE_IMAGE_PATH.$file_name;
		$medium_path = ROOT_PATH.LARGE_IMAGE_PATH.$file_name;
		$thumb_path = ROOT_PATH.THUMB_PATH.$file_name;
		$large_thumb_path = ROOT_PATH.LARGE_THUMB_PATH.$file_name;
		
		if( $image->save( $image_r, $destination_path ) ) {
			$image_r = $image->resize( $image_r, 700, 700 );
			if( $image->save( $image_r, $medium_path ) ) {
				$image_r = $image->resize( $image_r, 150, 150 );
				if( $image->save( $image_r, $large_thumb_path ) ) {
				$image_r = $image->resize( $image_r, 100, 100 );
					if( $image->save( $image_r, $thumb_path ) ) {
						update( 'productimages', array( 'filename' => $file_name ), array( 'id' => $id ), 1 );
						$feedback = 'Saved image '.$i.'. ('.$description.')';
					} else {
						delete( 'productimages', array( 'id' => $id ), 1 );
						unlink( $large_thumb_path );
						unlink( $destination_path );
						unlink( $medium_path );
						$feedback = 'There was a problem saving the thumb of image '.$i.'. ('.$description.').';
					}
				} else {
					delete( 'productimages', array( 'id' => $id ), 1 );
					unlink( $destination_path );
					unlink( $medium_path );
					$feedback = 'There was a problem saving the large thumb of image '.$i.'. ('.$description.').';
				}
			} else {
				unlink( $destination_path );
				delete( 'productimages', array( 'id' => $id ), 1 );
				$feedback = 'There was a problem saving the medium sized version of image '.$i.'. ('.$description.').';
			}
		} else {
			delete( 'productimages', array( 'id' => $id ), 1 );
			$feedback = 'There was a problem saving image '.$i.'. ('.$description.').';
		}
		
	} else {
		$feedback = 'Image number '.$i.', ('.$description.') failed. Make sure it is a jpeg, gif or png.';
	}
	return $id;
}

?>