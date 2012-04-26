<?
session_start();

if( !$_SESSION['is_admin'] ) {
	if( isset( $_REQUEST['password'] ) and trim( $_REQUEST['password'] ) == trim( file_get_contents('password.txt') ) ) {
		$_SESSION['is_admin'] = true;
		header( 'location: index.php' );
		exit();
	}
	?>
	<html>
	<body>
		<form action="" method="post">
			Password: <input type="password" name="password"> <input type="submit" name="log in">
		</form>
	</body>
	<?
	exit();
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Frameset//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-frameset.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Nate's Admin</title>
</head>
<frameset cols="224,*">
	<frame name="left" scrolling="auto" src="navigation.php">
	<frame name="main" id="main" src="purchases.php">
</frameset>
</html>

