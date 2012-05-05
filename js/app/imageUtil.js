define(function(require, exports, module) {
	
	var aspectRatio = 0.75;
	var pixelRatio = window.devicePixelRatio || 1;
	
	function round( number, roundThreshold ) {
		return Math.round(Math.round(number/roundThreshold)*roundThreshold);
	}
	
	exports.getUrl = function( image, width, height, roundThreshold ) {
		width *= pixelRatio;
		height *= pixelRatio;
		var sizes = exports.getSize( width, height, roundThreshold );
		return './productimages/'+sizes.width+'/'+sizes.height+'/'+image.filename;
	};
	
	exports.getSize = function( maxWidth, maxHeight, roundThreshold ) {
		if(+roundThreshold) {
			maxWidth = round( maxWidth, roundThreshold );
			maxHeight = round( maxHeight, roundThreshold*aspectRatio );
		}
		var sizes = {};
		if (maxWidth * aspectRatio > maxHeight) {
			sizes.width = Math.round( maxHeight / aspectRatio );
			sizes.height = maxHeight;
		} else {
			sizes.height = Math.round( maxWidth * aspectRatio );
			sizes.width = maxWidth;
		}
		return sizes;
	};
	
	exports.aspectRatio = aspectRatio;
});