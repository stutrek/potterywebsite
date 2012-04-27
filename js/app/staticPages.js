define(function(require, exports, module) {
	
	var expandedStatic$;
	var hide$;
	
	function hide() {
		if (expandedStatic$) {
			expandedStatic$.hide();
		}
		hide$.hide();
	}
	function show( pageId ) {
		hide();
		expandedStatic$ = $('#static_'+pageId);
		if (expandedStatic$.length) {
			expandedStatic$.show();
			hide$.show();
		}
	}
	
	function loadHash() {
		if (window.location.hash.split('/').length === 1) {
			var id = window.location.hash.substr(2);
			show(id);
		} else {
			hide();
		}
	}
	exports.init = function() {
		hide$ = $('#static_hide');
		$(window).on('hashchange', loadHash);
		loadHash();
	};
});