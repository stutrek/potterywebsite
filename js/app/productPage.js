define(function(require, exports, module) {
	
	var TEMPLATE_NAME = 'product_popup';
	var container$;
	var popup$;
	var screen$;
	var currentIndex;
	var products;
	
	var previousHash = '';

	function escapeListener( event ) {
		if (event.keyCode === 27) {
			exports.hide();
		}
	}
	
	function showIndex( index ) {
		container$.empty();
		$.tmpl(TEMPLATE_NAME, products[index]).appendTo(container$);
		
		if (index === 0) {
			popup$.find('.previous').hide();
		} else {
			popup$.find('.previous').show();
		}
		
		if (index === products.length-1) {
			popup$.find('.next').hide();
		} else {
			popup$.find('.next').show();
		}
		
		currentIndex = index;
	}
	
	exports.init = function() {
		templateString = document.getElementById('product_popup').innerHTML;
		container$ = $('#product_display_container');
		popup$ = $('#product_page');
		screen$ = popup$.find('.screen');
		screen$.on('click', exports.hide);
		$.template( TEMPLATE_NAME, templateString );
		
		popup$.on('click', '.next', exports.showNext);
		popup$.on('click', '.previous', exports.showPrevious);
	};
	
	exports.show = function( newProducts, index ) {
		products = newProducts;
		showIndex( index );
		popup$.addClass('showing');
		$(document).on('keyup', escapeListener);
		previousHash = window.location.hash;
	};
	
	exports.showNext = function() {
		showIndex( currentIndex+1 );
	};
	exports.showPrevious = function() {
		showIndex( currentIndex-1 );
	};
	
	exports.hide = function() {
		popup$.removeClass('showing');
		$(document).off('keyup', escapeListener);
		window.location.hash = previousHash;
	};
});