define(function(require, exports, module) {
	
	var imageUtil = require('./imageUtil');
	
	var TEMPLATE_NAME = 'templates/productpage.html';
	var container$;
	var popup$;
	var screen$;
	var productImage$;
	var productDisplayContainer;
	var currentIndex;
	var products;
	var showing = false;
	var imageSizes = {
		width: 700,
		height: 700 * imageUtil.aspectRatio
	};
	
	var previousHash = '';

	function keyListener( event ) {
		switch (event.keyCode ) {
			case 27: // escape
				window.location.hash = '';
				break;
			case 39: // right arrow
				exports.showNext();
				break;
			case 37: // left arrow
				exports.showPrevious();
				break;
		}
	}
	function makeHash( i ) {
		return '#!product/'+products[i].id+'/'+products[i].title
	}
	
	function showIndex( index ) {
		if (index < 0) {
			index = 0;
		}
		if (index >= products.length) {
			index = products.length-1;
		}
		if (index === currentIndex) {
			return;
		}
		
		container$.empty();
		var renderedTemplate$ = $.tmpl(TEMPLATE_NAME, products[index], {"imageSizes": imageSizes, "next": products[index+1], "previous": products[index-1]});
		renderedTemplate$.appendTo(container$)
		
		currentIndex = index;
		
		productImage$ = container$.find('.productimage img');
		setImageSize();
		debugger;
		productDisplayContainer.parentNode.style.top = $(document).scrollTop()+'px';
		screen$[0].style.height = document.body.scrollHeight+'px'
		popup$.addClass('showing');
	}
	
	function thumbnailImageClick( event ) {
		var newSrc = event.currentTarget.getAttribute('data-src');
		productImage$.attr('src', newSrc);
	}
	
	function loadProductById( id ) {
		for( var i = 0; i < products.length; i+=1 ) {
			if (products[i].id == id) {
				exports.show( i );
			}
		}
	}
	function loadThumb( index ) {
		container$.find('.productimage img').attr('src', imageUtil.getUrl(products[currentIndex].images[index], imageSizes.width, imageSizes.height, 100));
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
	
	function setImageSize() {
		imageSizes = imageUtil.getSize(window.innerWidth-280, window.innerHeight-30, 200);
		
		if (productImage$) {
			productImage$.css(imageUtil.getSize(window.innerWidth-280, window.innerHeight-30));
			setTimeout(function(){ // it offsetHeight is zero until the next redraw.
				productDisplayContainer.style.marginTop = ((window.innerHeight-productDisplayContainer.offsetHeight) / 2)+'px';
			}, 0);
		}
	}
	
	exports.init = function( newProducts ) {
		products = newProducts;
		container$ = $('#product_page .content');
		popup$ = $('#product_page');
		screen$ = popup$.find('.screen');
		productDisplayContainer = popup$.find('.product_display_container')[0];
		
		screen$.on('click', exports.hide);
		$(window).on('resize', setImageSize);
		
		loadHash();
		setImageSize();
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
	
	exports.loadHash = loadHash;
	
	exports.showNext = function() {
		window.location.hash =  makeHash(currentIndex+1);
	};
	exports.showPrevious = function() {
		window.location.hash =  makeHash(currentIndex-1);
	};
	
	exports.hide = function() {
		popup$.removeClass('showing');
		$(document).off('keyup', keyListener);
		showing = false;
		currentIndex = -1;
	};
});