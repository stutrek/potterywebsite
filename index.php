<?php
//phpinfo();

$basedir = dirname($_SERVER['SCRIPT_NAME']);
if (strlen($basedir) > 1) {
	$basedir .= '/';
}

$uriArray = explode( '/', $_SERVER['REQUEST_URI'] );
$scriptArray = explode( '/', $_SERVER['SCRIPT_NAME'] );
array_splice( $uriArray, 0, count($scriptArray)-1 );

$templates = array();
foreach( glob('templates/*.html') as $template_filename ) {
	$templates[$template_filename] = file_get_contents( $template_filename );
}
$templates_string = '';
foreach($templates as $key => $value) {
	$templates_string .= "<script id='$key' type='text/x-jquery-tmpl'>$value</script>";
}

require 'php/db_interface.php';
require 'php/jqTmpl.class.php';

$t = new jqTmpl;
$t->load_document($templates_string);

$products = get_all_products();

if( $uriArray[0] === 'product' and is_numeric($uriArray[1]) ) {
	$product = get_product( $uriArray[1] );
}

?><!doctype html>
<html>
<head>
	<?php
	if ($product) {
		echo '<title>'.$product->title.'</title>';
	} else {
		echo '<title>Stuart Aaron Ceramics by Stu Kabakoff</title>';
	}
	?>
	<base href="<?php echo $basedir; ?>" />
	<link rel="stylesheet" href="./css/base.css">
	<link rel="stylesheet" href="./css/header.css">
	<link rel="stylesheet" href="./css/footer.css">
	<link rel="stylesheet" href="./css/staticpages.css">
	<link rel="stylesheet" href="./css/productlist.css">
	<link rel="stylesheet" href="./css/productpage.css">
</head>
<body>
	<div id="header">
		<div id="header_content">
			<img src="./images/stuartaaron.png" height="68" width="420" alt="Stuart Aaron Ceramics by Stuart Aaron Kabakoff">
			<div id="links">
				<a href="#!about">About</a>
			</div>
		</div>
	</div>
	<?php
	if( $product ) {
		if ( $product->images[$uriArray[2]] ) {
			$image_index = $uriArray[2];
		} else {
			$image_index = 0;
		}
		echo '<div class="static product_display_container" style="display:block">
				<div class="productimage">
					<img src="'.$basedir.'productimages/700/'.$product->images[$image_index]->filename.'" alt="" />
				</div>
				<div class="productinfo">
					<h2>'.$product->title.'</h2>
					<p>'.$product->description.'</p>';
					if( $product->available ) {
						echo '<div class="price">$'.$product->price."
						<form target='paypal' action='https://www.paypal.com/cgi-bin/webscr' method='post'>
							<input type='hidden' name='cmd' value='_xclick' />
							<input type='hidden' name='business' value='sakabako@gmail.com'/>
							<input type='hidden' name='item_name' value='$product->title'/>
							<input type='hidden' name='item_number' value='$product->id'/>
							<input type='hidden' name='amount' value='$product->price'/>
							<input type='hidden' name='shipping' value='0'/>
							<input type='hidden' name='shipping2' value='0'/>
							<input type='hidden' name='notify_url' value='http://stuartaaron.com/return.php?product=$product->id' />
							<input type='image' src='./images/buy.png' border='0' name='submit' alt='Buy with PayPal' />
						</form>
						</div>";
					}
					if( $product->hasMultipleImages ) {
						echo '<ul class="imageselector">';
						foreach( $product->images as $image ) {
							echo '<li><a href="./product/'.$image->url.'"><img src="./productimages/115/'.$image->filename.'" alt="" /></a></li>';
						}
						echo '</ul>';
					}
				echo '</div>
			</div>';
	}
	?>
	<div id="static_about" class="static">
		<p>Stuart Aaron Kabakoff is a studio potter at <a href="http://www.lamanopottery.com/" target="_blank">La Mano Pottery</a> in Chelsea, NYC. He throws and handbuilds functional pottery.</p>
		<p>In his toolkit he has an Egg-Bot spherical plotter that is used for engraving and sgraffito. <a href="http://www.youtube.com/watch?v=_f8DgePmaSg">See a video.</a></p>
	</div>
	<div id="static_hide" class="static">
		<a href="#">Hide</a>
	</div>
	<div id="content">
		<? echo $t->tmpl( $templates['templates/productlist.html'], $products ); ?>
	</div>
	<div id="footer">
	
	</div>
	<div id="product_page">
		<a class="screen" href="#"></a>
		<div class="product_display_container-border">
			<div class="product_display_container">
				<a href="#" class="previous"><span>previous</span></a>
				<div class="content"></div>
				<a href="#" class="next"><span>next</span></a>
				<a href="./" class="close">x</a>
			</div>
		</div>
	</div>
	<?php echo $templates_string; ?>
	<script src="./js/lib/prototypes.js"></script>
	<script src="./js/lib/jquery.js"></script>
	<script src="./js/lib/jquery.tmpl.js"></script>
	<script src="./js/lib/jquery.ba-hashchange.min.js"></script>
 	<script src="./js/lib/sea.js" data-main="<?php echo $basedir ?>js/app/startup"></script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-31225099-1']);
		_gaq.push(['_trackPageview']);
	</script>
</body>
</html>