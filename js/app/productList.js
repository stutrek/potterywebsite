define(function(require, exports, module) {

	var productRepository = require('./productRepository');
	
	var templateString;
	var container$;
	var TEMPLATE_NAME = "templates/productlist.html";	
	
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
		container$ = $('#content');
	};
	
	exports.render = function(products) {
		container$.empty();
		$.tmpl(TEMPLATE_NAME, products).appendTo(container$);
	};
});