define(function(require, exports, module) {
	
	var TEMPLATE_NAME = 'product_popup';
	var container$;
	var popup$;
	var screen$;
	var currentIndex;
	var products;
	
	var previousHash = '';

	function keyListener( event ) {
		switch (event.keyCode ) {
			case 27: // escape
				exports.hide();
				break;
			case 39: // right arrow
				exports.showNext();
				break;
			case 37: // left arrow
				exports.showPrevious();
				break;
		}
	}
	
	function showIndex( index ) {
		if (index < 0) {
			index = 0;
		}
		if (index >= products.length) {
			index = products.length-1;
		}
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
	
	function thumbnailImageClick( event ) {
		var newSrc = event.currentTarget.getAttribute('data-src');
		container$.find('.productimage img').attr('src', newSrc);
	}
	
	exports.init = function() {
		templateString = document.getElementById('product_popup').innerHTML;
		container$ = $('#product_display_container .content');
		popup$ = $('#product_page');
		screen$ = popup$.find('.screen');
		screen$.on('click', exports.hide);
		$.template( TEMPLATE_NAME, templateString );
		
		popup$.on('click', '.next', exports.showNext);
		popup$.on('click', '.previous', exports.showPrevious);
		
		container$.on('click', 'a[data-src]', thumbnailImageClick);
	};
	
	exports.show = function( newProducts, index ) {
		products = newProducts;
		showIndex( index );
		popup$.addClass('showing');
		$(document).on('keyup', keyListener);
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
		$(document).off('keyup', keyListener);
		window.location.hash = previousHash;
	};
});