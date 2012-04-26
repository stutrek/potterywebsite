<?
require_once( dirname( __FILE__ ).'/constants.php' );
require_once( 'includes/db.php' );
require_once( 'includes/functions.php' );

if( isset( $_REQUEST['admin_debug'] ) ) {
	$_SESSION['debug'] = true;
}

$_REQUEST = clear_sql( $_REQUEST );

?>