define(function(require, exports, module) {

	var productRepository = require('./productRepository');
	
	var templateString;
	var container$;
	var TEMPLATE_NAME = "productListTemplate";	
	
	function fillList(products) {
		if (products) {
		} else {
			fetchError();
		}
	}
	
	exports.error = function() {
		container$.html('ERROR!');
	}
	
	exports.init = function() {
		templateString = document.getElementById('product_list_item').innerHTML;
		container$ = $('#content');
		$.template( TEMPLATE_NAME, templateString );
	};
	
	exports.render = function(products) {
		container$.empty();
		$.tmpl(TEMPLATE_NAME, products).appendTo(container$);
	};
});