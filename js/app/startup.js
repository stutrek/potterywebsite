define(function(require, exports, module) {
	require.async(['./productRepository', './productList', './productPage'], function(productRepository, productList, productPage) {
		
		var repositoryPromise = productRepository.init();
		productList.init();
		productPage.init();
		
		$(document).on('click', 'a', function( event ) {
			var productId = event.currentTarget.getAttribute('data-product-id')
			if (productId) {
				var product = productRepository.getById(+productId);
				productPage.show(product);
			}
		});
		
		repositoryPromise.done(productList.render);
		repositoryPromise.fail(productList.error);
	});
});