define(function(require, exports, module) {
	var products = [];
	window.scrollTo(0, 1);
	require.async(['./productRepository', './productList', './productPage', './staticPages', './imageUtil'], function(productRepository, productList, productPage, staticPages, imageUtil) {
		
		function recieveProducts( newProducts ) {
			products = newProducts;
			//productList.render( products );
			productPage.init( products );
		}
		
		var repositoryPromise = productRepository.loadAll();
		productList.init();
		staticPages.init();
		
		$(window).on('hashchange', productPage.loadHash );
		
		repositoryPromise.done(recieveProducts);
		//repositoryPromise.fail(productList.error);
		
		$(document).on('click', 'a', function(event) {
			if (event.currentTarget.getAttribute('href').substr(0,2) === './') {
				
				//make sure the URL bar is hidden on mobiles
				if (window.scrollY <= 1) {
						setTimeout(function(){ window.scrollTo(0, 1); }, 0);
				}
				window.location.hash = '!'+event.currentTarget.getAttribute('href').substr(2);
				event.preventDefault();
			}
		});
		
		repositoryPromise.then(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		});
		
		$('script[type="text/x-jquery-tmpl"]').each(function(index, script) {
			$.template( script.id, script.innerHTML );
		});

	});
});