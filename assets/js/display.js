// javascript

jQuery(document).ready(function(){

	jQuery('.pf-scroller-container .pf-scroller-item.jslinked').click(function(){
		window.href = jQuery(this).find('a').attr('href');
	});


});
