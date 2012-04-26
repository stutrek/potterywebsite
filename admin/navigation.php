<?
session_start();
$_SESSION['is_admin'] = true;
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" rev="stylesheet" href="admin.css" charset="utf-8">
	<script src="/js/jquery.js" type="text/javascript" language="javascript" charset="utf-8"></script>
	<title>Untitled</title>
</head>
<body style="background-color: #eee;">
	<div>
		<h2>Admin</h2>
		<a href="items.php" target="main">Items</a><br>
		<a href="static.php" target="main">Static Pages</a><br>
		<pre>
		</pre>
	</div>
</body>
</html>
