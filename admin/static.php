<?
require_once( '../includes/initialize.php' );
require_once( 'admin_functions.php' );


if( isset( $_REQUEST['save'] ) ) {
	
	$page_id = select( 'static_pages',  array( 'id' => $_REQUEST['id'] ), 'id', 1 );
	$_REQUEST['page']['url'] = preg_replace( '/[^\w-]/', '', $_REQUEST['page']['url'] );
	$_REQUEST['page']['in_sidebar'] = isset( $_REQUEST['in_sidebar'] );
	$_REQUEST['page']['in_footer'] = isset( $_REQUEST['in_footer'] );
	$_REQUEST['page']['is_banner'] = isset( $_REQUEST['is_banner'] );
	if( $page_id ) {
		update( 'static_pages', $_REQUEST['page'], array( 'id' => $_REQUEST['id'] ), 1 );
	} else {
		insert( 'static_pages', $_REQUEST['page'] );
	}
	send_to( '/'.$_REQUEST['page']['url'].'?'.session_name().'='.session_id() );
}
if( isset( $_REQUEST['delete'] ) ) {
	delete( 'static_pages', array( 'id' => $_REQUEST['delete'] ), 1 );
}
if( isset( $_REQUEST['id'] ) and !isset( $_REQUEST['save'] ) ) {
	$page = select( 'static_pages', array( 'id' => $_REQUEST['id'] ), '*', 1 );
} elseif( !isset( $_REQUEST['new'] ) ) {
	$pages = select( 'static_pages', array( 'is_banner' => '0', array( 'in_sidebar' => 1, 'in_footer' => 1 ) ), 'id, title' );
	$banners = select( 'static_pages', array( 'is_banner' => '1' ), 'id, title' );
	$hidden = select( 'static_pages', array( 'is_banner' => '0', 'in_sidebar' => '0', 'in_footer' => '0' ), 'id, title' );
}


?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
        "http://www.w3.org/TR/html4/strict.dtd">
<html lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8">
	<link rel="stylesheet" rev="stylesheet" href="admin.css" charset="utf-8">
	<script src="/js/jquery.js" type="text/javascript" language="javascript" charset="utf-8"></script>
	<title>Untitled</title>
	<style type="text/css">
		a:link, a:visited {
			text-decoration: none;
		}
	</style>
</head>
<body>
	<?
	if( isset( $pages ) ) {
		echo '<a href="?new">new</a><br>';
		echo '<h1>Pages</h1>';
		echo '<table>';
		foreach( $pages as $page ) {
			echo '<tr><td><a href="?id='.$page['id'].'">'.$page['id'].'. '.$page['title'].'</a></td><td><a href="static.php?delete='.$page['id'].'" onclick="return confirm( \'Are you sure you want to delete '.htmlentities($page['title']).'?\')">delete</a></td></tr>';
		}
		echo '</table>';
		echo '<h1>Front Page Banners</h1>';
		echo '<table>';
		foreach( $banners as $page ) {
			echo '<tr><td><a href="?id='.$page['id'].'">'.$page['id'].'. '.$page['title'].'</a></td><td><a href="static.php?delete='.$page['id'].'" onclick="return confirm( \'Are you sure you want to delete '.htmlentities($page['title']).'?\')">delete</a></td></tr>';
		}
		echo '</table>';
		echo '<h1>Hidden</h1>';
		echo '<table>';
		foreach( $hidden as $page ) {
			echo '<tr><td><a href="?id='.$page['id'].'">'.$page['id'].'. '.$page['title'].'</a></td><td><a href="static.php?delete='.$page['id'].'" onclick="return confirm( \'Are you sure you want to delete '.htmlentities($page['title']).'?\')">delete</a></td></tr>';
		}
		echo '</table>';
		
	} else { ?>
		<h1>Editing <?= $page['title']; ?></h1>
		<form action="" method="post">
			<fieldset>
				<table>
					<tr><td class="input_label">Title:</td><td><input type="text" name="page[title]" size="30" value="<?= $page['title'] ?>" /></td></tr>
					<tr><td class="input_label">URL:</td><td><input type="text" name="page[url]" size="30" value="<?= $page['url'] ?>" /> (alphanumeric, - and _)</td></tr>
					<tr><td class="input_label">Redirect URL:</td><td><input type="text" name="page[redirect_url]" size="60" value="<?= $page['redirect_url'] ?>" /> (this will send the visitor to another page)</td></tr>
					<tr><td class="input_label">main text:</td>
						<td>
							<textarea rows="30" cols="70" name="page[main]"><?= $page['main']; ?></textarea><br>
							<small>You can use <a href="http://wiki.splitbrain.org/wiki:syntax">DokuWiki formatting</a></small>
						</td>
					</tr>
					<tr><td class="input_label">CSS: <small>(optional)</small></td>
						<td>
							<textarea rows="1" cols="10" name="page[css]" onclick="this.rows=10;this.cols=40"><?=  $page['css']; ?></textarea><br>
						</td>
					</tr>
					<tr><td class="input_label"></td>
						<td>
							<input type="checkbox" name="in_sidebar" <?= is_checked( $page['in_sidebar'] ); ?> id="display"><label for="display"> Display in the sidebar<label><br/>
							<input type="checkbox" name="in_footer" <?= is_checked( $page['in_footer'] ); ?> id="in_footer"><label for="in_footer"> Display in the footer<label><br/>
							<input type="checkbox" name="is_banner" <?= is_checked( $page['is_banner'] ); ?> id="is_banner"><label for="is_banner"> is a front page banner<label><br/>
						</td>
					</tr>
					<tr><td></td><td><input type="hidden" name="id" value="<?= $page['id']; ?>"><input type="submit" name="save" value="Save" /></td></tr>
				</table>
			</fieldset>
		</form>
	<? } ?>
</body>
</html>
