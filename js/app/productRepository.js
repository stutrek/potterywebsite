define(function(require, exports, module) {
	var products;
	var productsById;
	
	function setProducts(newProducts) {
		products = newProducts;
		productsById = {};
	}
	
	function fetchError() {
		throw new Error('Error fetching data from the server!');
	}
	
	exports.loadAll = function( success, failure ) {
		return $.ajax({
			url: "./php/products_json.php",
			dataType: 'json',
			success: setProducts,
			error: fetchError
		});
	};
	
	exports.getAll = function() {
		return products;
	};
	
	exports.getById = function( id ) {
		if (productsById[id]) {
			return productsById[id];
		}
		for( var i = 0; products[i]; i += 1) {
			if (products[i].id === id) {
				productsById[id] = products[i];
				return products[i];
			}
		}
		throw new Error('Unable to find product id '+id);
	};
});