<!doctype html>
<html>
<head>
	<title>Stuart Aaron Ceramics by Stu Kabakoff</title>
	<link rel="stylesheet" href="./css/base.css">
	<link rel="stylesheet" href="./css/header.css">
	<link rel="stylesheet" href="./css/footer.css">
	<link rel="stylesheet" href="./css/productlist.css">
	<link rel="stylesheet" href="./css/productpage.css">
</head>
<body>
	<div id="header">
		<div id="header_content">
			<img src="./images/stuartaaron.png" height="100" width="200" alt="Stuart Aaron Pottery">
			<div id="links">
				<a href="./about">About</a>
				<a href="./contact">Contact</a>
				<a href="./commissions">Commissions</a>
			</div>
		</div>
	</div>
	<div id="content">
		<div class="loading">Loading Website...</div>
	</div>
	<div id="footer">
	
	</div>
	<div id="product_page">
		<div class="screen">
		</div>
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
				<div class="price">$${price}
				</div>
				<form target='paypal' action='https://www.paypal.com/cgi-bin/webscr' method='post'>
					<input type='hidden' name='cmd' value='_xclick' />
					<input type='hidden' name='business' value='sakabako@gmail.com'/>
					<input type='hidden' name='item_name' value="${title}"/>
					<input type='hidden' name='item_number' value='${id}'/>
					<input type='hidden' name='amount' value='${price}'/>
					<input type='hidden' name='shipping' value='0'/>
					<input type='hidden' name='shipping2' value='0'/>
					<input type='hidden' name='notify_url' value="http://stuartaaron.com/return.php?product=${id}" />
					<input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!" />
				</form>
			{{/if}}
			{{if images.length > 1}}
				<ul class="imageselector">
					{{each(i, image) images}}<li><a href="#!product/${product_id}/${i}/${title}"><img src="./productimages/100/${image.filename}" alt="" /></a></li>{{/each}}
				</ul>
			{{/if}}
		</div>
	</script>
	<script src="./js/lib/prototypes.js"></script>
	<script src="./js/lib/jquery.js"></script>
	<script src="./js/lib/jquery.tmpl.js"></script>
	<script src="./js/lib/jquery.ba-hashchange.min"></script>
	<script src="./js/lib/sea.js" data-main="./js/app/startup"></script>
	<script type="text/javascript">
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-31225099-1']);
		_gaq.push(['_trackPageview']);
	</script>
</body>
</html>