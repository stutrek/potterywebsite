<!doctype html>
<html>
<head>
	<title>Stuart Aaron Pottery</title>
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
		<div class="loading">Loading...</div>
	</div>
	<div id="footer">
	
	</div>
	<div id="product_page">
		<div class="screen">
		</div>
		<div class="product_display_container-border">
			<div id="product_display_container">
			</div>
		</div>
	</div>
	<script id="product_list_item" type="text/template">
		<a class="product" href="#!product/${id}/${title}" data-product-id="${id}">
			<img src="./productimages/thumbs/${imageName}" alt="" />
		</a>
	</script>
	<script id="product_popup" type="text/template">
		<img src="./productimages/750/${imageName}" alt="" />
		<div class="productinfo">
			<div class="description">${description}</div>
			<div class="price">$${price}</div>
		</div>
	</script>
	<script src="./js/lib/jquery.js"></script>
	<script src="./js/lib/jquery.tmpl.js"></script>
	<script src="./js/lib/sea.js" data-main="./js/app/startup"></script>
</body>
</html>