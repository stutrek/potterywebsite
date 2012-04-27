define(function(require, exports, module) {
	var products = [];
	
	require.async(['./productRepository', './productList', './productPage'], function(productRepository, productList, productPage) {
		
		function recieveProducts( newProducts ) {
			products = newProducts;
			productList.render(products);
		}
		
		$('#content .loading').html('Loading pottery...');
		
		var repositoryPromise = productRepository.loadAll();
		productList.init();
		productPage.init();
		
		$(document).on('click', 'a', function( event ) {
			var productId = event.currentTarget.getAttribute('data-product-id')
			if (productId) {
				var product = productRepository.getById(productId);
				productPage.show(products, products.indexOf(product));
			}
		});
		
		repositoryPromise.done(recieveProducts);
		repositoryPromise.fail(productList.error);
		
		repositoryPromise.then(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		});
	});
});