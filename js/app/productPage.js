define(function(require, exports, module) {
	
	var TEMPLATE_NAME = 'product_popup';
	var container$;
	var popup$;
	var screen$;
	var next$;
	var previous$;
	var currentIndex;
	var products;
	var showing = false;
	
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
		var renderedTemplate$ = $.tmpl(TEMPLATE_NAME, products[index]);
		renderedTemplate$.appendTo(container$)
		
		if (index === 0) {
			previous$.hide();
		} else {
			previous$.show();
			previous$.attr( 'href', '#!product/'+products[index-1].id+'/'+products[index-1].title);
		}
		
		if (index === products.length-1) {
			next$.hide();
		} else {
			next$.show();
			next$.attr( 'href', '#!product/'+products[index+1].id+'/'+products[index+1].title);
		}
		
		currentIndex = index;
		popup$.addClass('showing');
	}
	
	function thumbnailImageClick( event ) {
		var newSrc = event.currentTarget.getAttribute('data-src');
		container$.find('.productimage img').attr('src', newSrc);
	}
	
	function loadProductById( id ) {
		for( var i = 0; i < products.length; i+=1 ) {
			if (products[i].id == id) {
				exports.show( i );
			}
		}
	}
	function loadThumb( index ) {
		container$.find('.productimage img').attr('src', './productimages/700/'+products[currentIndex].images[index].filename);
	}
	function loadHash() {
		var argv = window.location.hash.split('/');
		if (argv[0] === '#!product') {
			loadProductById( argv[1] );
			if ( !isNaN(+argv[2]) ) {
				loadThumb(+argv[2]);
			}
		} else if (showing) {
			exports.hide();
		}
	}
	exports.init = function( newProducts ) {
		products = newProducts;
		templateString = document.getElementById('product_popup').innerHTML;
		container$ = $('#product_display_container .content');
		popup$ = $('#product_page');
		screen$ = popup$.find('.screen');
		next$ = popup$.find('.next');
		previous$ = popup$.find('.previous');
		screen$.on('click', exports.hide);
		$.template( TEMPLATE_NAME, templateString );
		
		$(window).on('hashchange', loadHash );
		loadHash();
	};
	
	exports.setProducts = function( newProducts ) {
		products = newProducts;
	};
	
	exports.show = function( index ) {
		showIndex( index );
		if (!showing) {
			$(document).on('keyup', keyListener);
		}
		showing = true;
	};
	
	exports.showNext = function() {
		window.location.hash = '!product/'+products[currentIndex+1].id+'/'+products[currentIndex+1].title;
	};
	exports.showPrevious = function() {
		window.location.hash = '!product/'+products[currentIndex-1].id+'/'+products[currentIndex-1].title;
	};
	
	exports.hide = function() {
		popup$.removeClass('showing');
		$(document).off('keyup', keyListener);
		window.location.hash = '';
		showing = false;
	};
});