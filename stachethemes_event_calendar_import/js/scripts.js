/*
 * Here your scripts
 */

jQuery(document).ready(function() {
	
	jQuery('.stec-layout-week-daycell-today').css('height', '500px');
	
});


jQuery(document).on('click', '.stec-layout-event-preview-left', function(e) {
	
	jQuery('.stec-layout-event-inner').remove();
	
	e.preventDefault();
	
	return false;
	
});