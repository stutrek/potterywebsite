<?php
//phpinfo();

$basedir = dirname($_SERVER['SCRIPT_NAME']).'/';
?><!doctype html>
<html>
<head>
	<title>Stuart Aaron Ceramics by Stu Kabakoff</title>
	<base href="<?php echo $basedir; ?>" />
	<link rel="stylesheet" href="./css/base.css">
	<link rel="stylesheet" href="./css/header.css">
	<link rel="stylesheet" href="./css/footer.css">
	<link rel="stylesheet" href="./css/productlist.css">
	<link rel="stylesheet" href="./css/productpage.css">
	<link rel="stylesheet" href="./css/staticpages.css">
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
	<div id="static_about" class="static">
		<p>Stuart Aaron Kabakoff is a studio potter at <a href="http://www.lamanopottery.com/" target="_blank">La Mano Pottery</a> in Chelsea, NYC. He throws and handbuilds functional pottery.</p>
		<p>In his toolkit he has an Egg-Bot spherical plotter that is used for engraving and sgraffito. <a href="http://www.youtube.com/watch?v=_f8DgePmaSg">See a video.</a></p>
	</div>
	<div id="static_hide" class="static">
		<a href="#">Hide</a>
	</div>
	<div id="content">
		<div class="loading">Loading Website...</div>
	</div>
	<div id="footer">
	
	</div>
	<div id="product_page">
		<a class="screen" href="#"></a>
		<div class="product_display_container-border">
			<div id="product_display_container">
				<a href="#" class="previous"><span>previous</span></a>
				<div class="content"></div>
				<a href="#" class="next"><span>next</span></a>
			</div>
		</div>
	</div>
	<script id="product_list_item" type="text/template">
		<a class="product" href="#!product/${id}/${title}" data-product-id="${id}">
			<img src="./productimages/150/${images[0].filename}" alt="" />
			{{if available == '0'}}
				<span class="unavailablemessage">taken</span>
			{{/if}}
		</a>
	</script>
	<script id="product_popup" type="text/template">
		<div class="productimage">
			<img src="./productimages/700/${images[0].filename}" alt="" />
		</div>
		<div class="productinfo">
			<h2>${title}</h2>
			<div class="description">${description}</div>
			{{if available == '1'}}
				<div class="price">$${parseInt(price, 10)}
					<form target='paypal' action='https://www.paypal.com/cgi-bin/webscr' method='post'>
						<input type='hidden' name='cmd' value='_xclick' />
						<input type='hidden' name='business' value='sakabako@gmail.com'/>
						<input type='hidden' name='item_name' value="${title}"/>
						<input type='hidden' name='item_number' value='${id}'/>
						<input type='hidden' name='amount' value='${price}'/>
						<input type='hidden' name='shipping' value='0'/>
						<input type='hidden' name='shipping2' value='0'/>
						<input type='hidden' name='notify_url' value="http://stuartaaron.com/return.php?product=${id}" />
						<input type="image" src="./images/buy.png" border="0" name="submit" alt="Buy with PayPal" />
					</form>
				</div>
			{{/if}}
			{{if images.length > 1}}
				<ul class="imageselector">
					{{each(i, image) images}}<li><a href="#!product/${product_id}/${i}/${title}"><img src="./productimages/115/${image.filename}" alt="" /></a></li>{{/each}}
				</ul>
			{{/if}}
		</div>
	</script>
	<script src="./js/lib/prototypes.js"></script>
	<script src="./js/lib/jquery.js"></script>
	<script src="./js/lib/jquery.tmpl.js"></script>
	<script src="./js/lib/jquery.ba-hashchange.min"></script>
	<script src="./js/lib/sea.js" data-main="<?php echo $basedir ?>js/app/startup"></script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-31225099-1']);
		_gaq.push(['_trackPageview']);
	</script>
</body>
</html>