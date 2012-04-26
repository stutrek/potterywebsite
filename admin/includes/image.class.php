<?

class image {
	
	var $transparent = false;
		
	var $background = array( 'red' => 255, 'green' => 255, 'blue' => 255 );

			
	function load( $source ) {
		$type = exif_imagetype( $source );
		$mime = image_type_to_mime_type( $type );
		switch( $mime ) {
			case 'image/gif':
				if (!function_exists('imagegif')) {
					echo "imagegif does not exist";
					return (false);
				} else {
					 return @imagecreatefromgif( $source );
				}
				break;
			case 'image/jpeg':
				if (!function_exists('imagejpeg')) {
					echo "imagejpeg does not exist";
					return (false);
				} else {
					 return @imagecreatefromjpeg( $source);
				}
				break;
			case 'image/png':
				if (!function_exists('imagepng')) {
					echo "imagepng does not exist.";
					return (false);
				} else { 
					$image = @imagecreatefrompng( $source );	
					imagealphablending( $image, false) ;
					imagesavealpha( $image, true );
					return $image;
				}
				break;
			default:
				echo 'invalid image type: '.$type."\n";
				return false;
		}
	}
	
	function resize( $image, $max_width, $max_height, $transparent=null ) {
		
		if( $transparent == null ) {
			$transparent = $this->transparent;
		}
		
		$width = $original_width = ImageSX($image);
		$height = $original_height = ImageSY($image);
	
		if( $max_height != null and $height > $max_height ) {
			$ratio = $max_height / $height;
			$height = $max_height;
			$width = $width * $ratio;
		}
		if( $max_width != null and $width > $max_width ) {
			$ratio = $max_width / $width;
			$width = $max_width;
			$height = $height * $ratio;
		}
		
		$width  = intval($width);
		$height = intval($height);
		
		$new_image = $this->new_image( $width, $height, $transparent );
		
		imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $width, $height, $original_width, $original_height );
		
		return $new_image;	
	}
	
	function stretch( $image, $width, $height, $transparent=null ) {
		
		if( $transparent == null ) {
			$transparent = $this->transparent;
		}
		
		$original_width = imagesx( $image  );
		$original_height = imagesy( $image );
		
		$new_image = $this->new_image( $width, $height, $transparent );
		
		imagecopyresampled( $new_image, $image, 0, 0, 0, 0, $width, $height, $original_width, $original_height );
		
		return $new_image;	
	}
	
	function new_image( $width, $height, $transparent=null ) {
		
		if( $transparent == null ) {
			$transparent = $this->transparent;
		}
		
		$new_image = imagecreatetruecolor( $width, $height );
		
		if( $transparent ) {		
			//echo 'transparent, '.$width.', '.$height.'<br />';
			$bg_color = imagecolorallocatealpha( $new_image, $this->background['red'], $this->background['green'], $this->background['blue'], 127 );
			imagefill( $new_image, 0, 0, $bg_color );	

			imagealphablending($new_image, false);
			imagesavealpha($new_image, true);
			
		} else { 
			//echo 'not transparent, '.$width.', '.$height.'<br />';
			$bg_color = imagecolorallocate( $new_image, $this->background['red'], $this->background['green'], $this->background['blue'] );
			imagefill( $new_image, 0, 0, $bg_color );		
		}
		
		return $new_image;
	}
	
	function overlay( $image, $overlay, $max_ratio=0.5, $h_placement=5, $v_placement=5 ) {
		if( $overlay == null ) {
			return $image;
		}
		$overlay_width = ImageSX( $overlay );
		$overlay_height = ImageSY( $overlay );
		
		$width = imageSX( $image );
		$height = imageSY( $image );
		
		if( $overlay_height > ( $height * $max_ratio ) or $overlay_width > ( $width * $max_ratio )) {
			$overlay = $this->resize( $overlay, ($overlay_height * $max_ratio), ($overlay_width * $max_ratio), true );
			$overlay_width = ImageSX( $overlay );
			$overlay_height = ImageSY( $overlay );
		}
		imagealphablending($image, true);
		imagesavealpha($image, false);
		imagecopy( $image, $overlay, $h_placement, ($height-$overlay_height-$v_placement), 0, 0, $overlay_width, $overlay_height );
		
		return $image;
	}
	
	function output( $image, $format='jpeg' ) {
		header( 'content-type: image/'.$format );
		$function = 'image'.$format;
		$function( $image );
	}
	
	function save( $image, $path, $format='jpeg' ) {
		$function = 'image'.$format;
		return( $function( $image, $path, 80 ) );
	}
}

?>