if (!Array.prototype.indexOf) {
	Array.prototype.indexOf = function( item ) {
		for( var i = 0; i < this.length; i += 1) {
			if (item === this[i]) {
				return i;
			}
		}
		return -1;
	};
}