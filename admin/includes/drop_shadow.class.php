<?

require_once (dirname(__FILE__).'/image.class.php');

/*

Drop Shadow Class by Stuart Kabakoff, 2006-10-10

This class takes an image resource and puts a drop shadow on it for you.
It was designed to be simple to use, thus it lacks some options that you
probably won't need. It does not resize images.

There are three options for this class:
location of light source, corner size, and background color.

The constructor:
drop_shadow( $horizontal='center', $vertical='top', $corner_size=8px, $opacity=50%, $background_color=white )

The first two arguments control the light source, they are control where 
the light is coming from, not the side the shadow is on.
(this means that a value of right will put the shadow on the left).
$horizontal has three options, left, right or center.
$vertical also has three options, top, bottom and center.
(in addition to center, middle is accepted)

The corner size controls the radius of the corners

The opacity controls the darkness of the shadow, 100 is black and 0 is no shadow.

The background color is an array( 'red', 'green', 'blue' ).
You can label them or just put them in that order.
The default is white.

You may send no image, or null as the image if you intend
to use the object later but you don't want to use it now.
You can also do that if you'd like to be a pro and edit the
h_offset and v_offset manually. The class can handle it.
(however, it won't fill in the background with grey)

--------------------------------------------------------------

Usage:
//$image = a GD image resource

$drop = new drop_shadow( 'center', 'top', 8, 50 );
$image_with_shadow = $drop->shadow( $image ) ;

//After the object has been created you can use this line to 
//give another image a shadow with the same settings.
$image2_with_shadow = $drop->shadow( $image2 );


*/

class drop_shadow extends image {
	
	var $o_image;
	var $o_width;
	var $o_height;
	
	var $image;
	var $width;
	var $height;
	
	var $v_offset; 
	var $h_offset;
	var $corner_size;
	var $opacity; //the opacity of the shadow, 100 is black, 0 is white.
	
	var $horizontal_align = 'center';
	var $vertical_align = 'top';
	
	var $opaque_shadow_source = array(
			'right_top' => 'images/dropshadow/tr.png',
			'right_bottom' => 'images/dropshadow/br.png',
			'left_top' => 'images/dropshadow/tl.png',
			'left_bottom' => 'images/dropshadow/bl.png',
			'left' => 'images/dropshadow/left.png',
			'right' => 'images/dropshadow/right.png',
			'bottom' => 'images/dropshadow/bottom.png',
			'top' => 'images/dropshadow/top.png'
			);
			
	var $trans_shadow_source = array(
			'right_top' => 'images/dropshadow/trans_tr.png',
			'right_bottom' => 'images/dropshadow/trans_br.png',
			'left_top' => 'images/dropshadow/trans_tl.png',
			'left_bottom' => 'images/dropshadow/trans_bl.png',
			'left' => 'images/dropshadow/trans_left.png',
			'right' => 'images/dropshadow/trans_right.png',
			'bottom' => 'images/dropshadow/trans_bottom.png',
			'top' => 'images/dropshadow/trans_top.png'
			);
	
	var $shadow_source = array();
	
	var $shadow = array(
			'right_top' => null,
			'right_bottom' => null,
			'left_top' => null,
			'left_bottom' => null,
			'left' => null,
			'right' => null,
			'bottom' => null,
			'top' => null
			);
	
	function drop_shadow( $horizontal='center', $vertical='top', $corner_size=8, $opacity=50, $background=null ) {
		
		if( !is_numeric( $corner_size ) ) {
			$corner_size = 8;
		}
		if( ceil( $corner_size ) != $corner_size ) {
			$corner_size = ceil( $corner_size );
		}

		$this->horizontal_align = $horizontal;
		$this->vertical_align = $vertical;
		$this->set_corner_size( $corner_size );
		
		
		if( is_array( $background) ) {
			foreach( $background as $key => $val ) {
				switch( $key ) {
					case 'red':
					case 0:
						$this->background['red'] = $val;
						break;
					case 'green':
					case 1:
						$this->background['green'] = $val;
						break;
					case 'blue':
					case 2:
						$this->background['blue'] = $val;
						break;
				}
			}
			$this->shadow_source = $this->opaque_shadow_source;
		} elseif( $background =='trans' or $background == 'transparent' ) {
			$this->transparent = true;
			$this->shadow_source = $this->trans_shadow_source;
		} else {
			$this->shadow_source = $this->opaque_shadow_source;
		}
			
		if( !is_numeric( $opacity ) ) {
			$this->opacity = 50;
		} else if( $opacity > 100 ) {
			$this->opacity = 100;
		} else if( $opactiy < 0 ) {
			$this->opacity = 0;
		} else {
			$this->opacity = $opacity;
		}
		
	}
	
	function set_corner_size( $corner_size ) {
		switch( $this->horizontal_align ) {
			case 'left':
				$this->h_offset = $corner_size;
				break;
			case 'right':
				$this->h_offset = 0;
				break;
			case 'middle':
			case 'center':
			default:
				$this->h_offset = $corner_size / 2;
		}
		switch( $this->vertical_align ) {
			case 'top':
				$this->v_offset = $corner_size;
				break;
			case 'bottom':
				$this->v_offset = 0;
				break;
			case 'middle':
			case 'center':
				$this->v_offset = $corner_size / 2;
				break;
			default: //top
				$this->v_offset = $corner_size;
		}
		$this->corner_size = $corner_size;
		$this->get_new_size();
	}
	
	function shadow( $image=null, $corner_size=null ) {
		if( $corner_size !== null ) {
			$this->set_corner_size( $corner_size );
		}
		if( $image !== null ) {
			$this->load_image( $image );
		}
		return $this->make_shadow();
	}
	
	function load_image( $image ) {
		if( $image == null ) {
			return;
		}
		$this->o_image = $image;
		$this->o_width = imagesx( $image );
		$this->o_height = imagesy( $image );
		$this->get_new_size();
	}
	
	function make_shadow() {
		if( $this->corner_size == 0 ) {
			return $this->o_image;
		}
		$this->image = $this->new_image( $this->width, $this->height );
		
		$this->load_shadows();
		$this->place_shadows();
		
		return $this->image;
	}
	
	function has_left_side() {
		return $this->h_offset < $this->corner_size;
	}
	function has_right_side() {
		return $this->h_offset > 0;
	}
	function has_bottom() {
		return $this->v_offset > 0;
	}
	function has_top() {
		return $this->v_offset < $this->corner_size;
	}
	
	function over_zero( $number ) {
		if( $number > 0 ) { 
			return $number;
		} else {
			return 0;
		}
	}
	
	function get_new_size() {
		$this->width = $this->o_width + abs( $this->h_offset );
		if( $this->corner_size > abs( $this->h_offset ) ) {
			$this->width += $this->corner_size - abs( $this->h_offset );
		}
		
		$this->height = $this->o_height + abs( $this->v_offset );
		if( $this->corner_size > abs( $this->v_offset ) ) {
			$this->height += $this->corner_size - abs( $this->v_offset );
		}
	}
	
	function place_shadows() {
			
		$shadow_width = $this->o_width + $this->corner_size - ( $this->corner_size * 2 );
		$shadow_height = $this->o_height + $this->corner_size - ( $this->corner_size * 2 );
		
		$width_a = array( 
				'top' => $shadow_width,
				'bottom' => $shadow_width,
				);
		$height_a = array( 
				'left' => $shadow_height,
				'right' => $shadow_height
				);
		
		$v_offset['left_top'] = $this->over_zero( $this->v_offset - $this->corner_size );
		$h_offset['left_top'] = $this->over_zero( $this->h_offset - $this->corner_size );
		
		$v_offset['right_top'] = $v_offset['left_top'];
		$h_offset['right_top'] = $h_offset['left_top'] + $shadow_width + $this->corner_size;
		
		$v_offset['left_bottom'] = $v_offset['left_top'] + $shadow_height + $this->corner_size;
		$h_offset['left_bottom'] = $h_offset['left_top'];
		
		$v_offset['right_bottom'] = $v_offset['left_bottom'];
		$h_offset['right_bottom'] = $h_offset['right_top'];
		
		$v_offset['left'] = $v_offset['left_top'] + $this->corner_size;
		$h_offset['left'] = $h_offset['left_top'];
		
		$v_offset['right'] = $v_offset['right_top'] + $this->corner_size;
		$h_offset['right'] = $h_offset['right_top'];
		
		$v_offset['top'] = $v_offset['left_top'];
		$h_offset['top'] = $h_offset['left_top'] + $this->corner_size;
		
		$v_offset['bottom'] = $v_offset['left_bottom'];
		$h_offset['bottom'] = $h_offset['left_bottom'] + $this->corner_size;
		
		foreach( $this->shadow as $key => $image ) {
			if( $image != null ) {
				
				if( isset( $height_a[$key] ) ) {
					$height = $height_a[$key];
				} else {
					$height = $this->corner_size;
				}
				
				if( isset( $width_a[$key] ) ) {
					$width = $width_a[$key];
				} else {
					$width = $this->corner_size;
				}
				$image = $this->stretch( $image, $width, $height );
				$this->place_image( $image, $width, $height, $h_offset[$key], $v_offset[$key] );
			}
		}
		
		if( $this->has_left_side() ) {
			$image_h_offset = abs( $this->corner_size - $this->h_offset );
		} else  {
			$image_h_offset = 0;
		}
		if( $this->has_top() ) {
			$image_v_offset = abs( $this->corner_size - $this->v_offset );
		} else {
			$image_v_offset = 0;
		}
		
		$this->place_image( $this->o_image, $this->o_width, $this->o_height, $image_h_offset, $image_v_offset, 100 );
		
	}
	
	function place_image( $image, $width, $height, $h_offset, $v_offset, $opacity=null ) {
		
		if( $this->transparent ) {
			imagecopy( $this->image, $image, $h_offset, $v_offset, 0, 0, $width, $height );
		} else {
			if( $opacity == null ) {
				$opacity = $this->opacity;
			}
			imagecopymerge( $this->image, $image, $h_offset, $v_offset, 0, 0, $width, $height, $opacity );
		}
	}
	
	function load_shadows() {
		if( $this->has_left_side() ) {
			$this->load_shadow( 'left' );
			$this->load_shadow( 'left_top' );
			$this->load_shadow( 'left_bottom' );
		}
		if( $this->has_right_side() ) {
			$this->load_shadow( 'right' );
			$this->load_shadow( 'right_top' );
			$this->load_shadow( 'right_bottom' );
		}
		if( $this->has_top() ) {
			$this->load_shadow( 'top' );
			$this->load_shadow( 'left_top' );
			$this->load_shadow( 'right_top' );
		}
		if( $this->has_bottom() ) {
			$this->load_shadow( 'bottom' );
			$this->load_shadow( 'left_bottom' );
			$this->load_shadow( 'right_bottom' );
		}
	}
	
	function load_shadow( $side ) {
		if( $this->shadow[$side] != null ) {
			return;
		}
		$this->shadow[$side] = $this->load( ROOT_PATH.$this->shadow_source[$side] );
		
	}
}

?>