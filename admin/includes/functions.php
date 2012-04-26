<?

function get_args() {
	$url = explode( '?', substr( $_SERVER['REQUEST_URI'], strlen( URL_PREPEND ) ) );
	$args = explode( '/', $url[0] );
	foreach( $args as $key => $val ) {
		if( trim( $val ) == '' ) {
			unset( $args[$key] );
		} else {
			$args[$key] = preg_replace( '/^(sql:)+/', '', trim( urldecode( $val ) ) );
		}
	}
	return array_values( $args );
}

function in_args( $var ) {
	global $args;
	$arg_count = count( $args );
	for( $i=0; $i < $arg_count; $i++ ) {
		if( $args[$i] == $var ) {
			return true;
		}
	}
	return false;
}

function clear_sql( $array ) {
	foreach( $array as $key => $val ) {
		if( is_array( $val ) ) {
			$array[$key] = clear_sql( $val );
		} else {
			$array[$key] = preg_replace( '/^(sql:)+/', '', trim( $val ) );
		}
	}
	return $array;
}

function replace_extension( $path, $extension ) {
	if( strstr( $path, '.' ) !== false ) {
		$path_a = explode( '.', $path );
		unset( $path_a[count($path_a)-1] );
		$path = implode( '.', $path_a ).'.'.$extension;
	} else {
		$path = $path.'.'.$extension;
	}
	return $path;
}

function mime_to_extension( $mime ) {
	switch( strtolower( $mime ) ) {
		case 'video/3gpp':
			return '3gp';
		case 'video/3gpp2':
			return '3g2';
		case 'video/x-mpeg':
		case 'video/x-mpeg2a':
		case 'video/mpeg2a':
		case 'video/mpeg':
		case 'video/mpg':
			return 'mpg';
		case 'video/wmv':
			return 'wmv';
		case 'video/quicktime':
			return 'mov';
		case 'video/x-msvideo':
		case 'video/msvideo':
		case 'video/avi':
			return 'avi';
		case 'video/mp4':
		case 'video/mpeg4':
		case 'video/mp4v-es':
		case 'video/h263-2000':
		case 'video/h263-1998':
		case 'video/h264':
		case 'video/h263':
			return 'mp4';
		case 'video/mpv':
			return 'mpv';
		case 'image/jpg':
		case 'image/jpeg':
		case 'image/pjpeg':
		case 'image/pjpg':
			return 'jpg';
		case 'image/gif':
			return 'gif';
		case  'image/png':
			return 'png';
		case 'audio/mp3':
			return 'mp3';
		case 'audio/midi':
		case 'audio/mid':
			return 'mid';
		case 'application/java-archive':
		case 'game':
			return 'jar';
	}
}

function extension_to_mime( $extension ) {
	$dot = strpos( $extension, '.' );
	if( $dot !== false ) {
		$extension_a = explode( '.', $extension );
		$extension = $extension_a[count($extension_a)-1];
	}

	switch( strtolower( $extension ) ) {
		case '3gp':
			return 'video/3gpp';
		case '3g2':
			return 'video/3gpp2';
		case 'mpg':
			return 'video/mpeg';
		case 'wmv':
			return 'video/wmv';
		case 'mov':
			return 'video/quicktime';
		case 'avi':
			return 'video/avi';
		case 'mp4':
			return 'video/mp4';
		case 'mpv':
			return 'video/mpv';
		case 'jpg':
			return 'image/jpeg';
		case 'gif':
			return 'image/gif';
		case 'png':
			return 'image/png';
		case 'mp3':
			return 'audio/mp3';
		case 'spmidi':
		case 'mid':
			return 'audio/midi';
		case 'game':
			return 'application/java-Archive';
		default:
			return '';
			
	}
}

function file_extension( $file_name ) {
	$dot = strpos( $file_name, '.' );
	if( $dot !== false ) {
		$file_name_a = explode( '.', $file_name );
		$extension = $file_name_a[count($file_name_a)-1];
	}
	return $extension;
}

function sql_time_to_timestamp( $sql_time ) {
	$time_a = sql_time_to_array( $sql_time );
	$timestamp = mktime( $time_a['hours'], $time_a['minutes'], $time_a['seconds'], $time_a['month'], $time_a['day'], $time_a['year'] );
	return $timestamp;
}

function sql_time_to_array( $sql_time ) {
	$datetime = explode( ' ', $sql_time );
	$time = explode( ':', $datetime[1] );
	$date = explode( '-', $datetime[0] );
	
	$return = array(
			'year' => $date[0],
			'month' => $date[1],
			'day' => $date[2],
			'hours' => $time[0],
			'minutes' => $time[1],
			'seconds' => $time[2]
			);
	return $return;
}

function sql_time_to_readable( $sql_time ) {
	$timestamp = sql_time_to_timestamp( $sql_time );
	$time = explode( ' ', $sql_time );
	if( $time[1] == '00:00:00' or $time[1] == '' ) {
		return date( 'l, F j, Y', $timestamp );
	} else {
		return date( 'l, F j, Y g:i a', $timestamp );
	}
}

function timestamp_to_sql_time( $timestamp=null ) {
	if( $timestamp == null ) {
		$timestamp = time();
	}
	return date( 'Y-m-d H:i:s', $timestamp ); 
}
function timestamp_to_readable( $timestamp=null ) {
	if( $timestamp == null ) {
		$timestamp = time();
	}
	return date( 'l, F j, Y g:i a', $timestamp );
}
function sql_time( $timestamp=null ) {
	return timestamp_to_sql_time( $timestamp );
}
function array_to_sql_time( $time_a ) {
	return sprintf( '%04d-%02d-%02d %02d:%02d:%02d', $time_a['year'], $time_a['month'], $time_a['day'], $time_a['hours'], $time_a['minutes'], $time_a['seconds'] );
}
function array_to_timestamp( $time_a ) {
	return mktime( $time_a['hours'], $time_a['minutes'], $time_a['seconds'], $time_a['month'], $time_a['day'], $time_a['year'] );
}
if (!function_exists('file_put_contents')) {

	function file_put_contents( $file, $contents='', $method='a+' ) {
		$file_handle = fopen($file, $method);
		fwrite($file_handle, $contents);
		fclose($file_handle);
		return true;
	}
}
function remove_directory($dir) {
	if( $handle = opendir( $dir ) ) {
		while (false !== ($item = readdir($handle))) {
			if ($item != "." && $item != "..") {
				if (is_dir("$dir/$item")) {
					remove_directory("$dir/$item");
				} else {
					unlink("$dir/$item");
				}
			}
		}
		closedir($handle);
		rmdir($dir);
	}
}
if( !function_exists( 'scandir' ) ) {
	function scandir( $dir, $sort=0 ) {
		$dh  = opendir($dir);
		while (false !== ($filename = readdir($dh))) {
			$files[] = $filename;
		}
		
		if( $sort ) {
			rsort($files);
		} else {
			sort($files);
		}
		
		return $files;
	}
}

