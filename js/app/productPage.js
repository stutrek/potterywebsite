define(function(require, exports, module) {
	
	var TEMPLATE_NAME = 'product_popup';
	var container$;
	var popup$;
	var screen$;
	

	function escapeListener( event ) {
		if (event.keyCode === 27) {
			exports.hide();
		}
	}
		
	exports.init = function() {
		templateString = document.getElementById('product_popup').innerHTML;
		container$ = $('#product_display_container');
		popup$ = $('#product_page');
		screen$ = popup$.find('.screen');
		screen$.on('click', exports.hide);
		$.template( TEMPLATE_NAME, templateString );
	};
	
	exports.show = function( product ) {
		container$.empty();
		$.tmpl(TEMPLATE_NAME, product).appendTo(container$);
		popup$.addClass('showing');
		$(document).on('keyup', escapeListener);
	};
	
	exports.hide = function() {
		popup$.removeClass('showing');
		$(document).off('keyup', escapeListener);
	};
});