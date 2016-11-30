(function( $ ) {
	'use strict';
	
	$(document).ready(function(){
		
		$('.go-compare-page').click(function(event){
			event.preventDefault();
			var href = document.getElementById('woo-go-compare-page').href;
			var array = document.getElementsByClassName('woo_compare_checkbox');
			var result=[];
			for(var i = 0;i<array.length;i++){
				if(array[i].checked){
					var temp = array[i].value;
					result.push(temp);
				}
			}
			$.ajax({
				type	:	'post',
				data	:	{'action' : 'woo_compare_ajax','data' : result},
				url		: 	ajax.url	,
				success : function(results){
					if(results=='null'){
						alert('You need choose any product to compare');
					}
					else
					location.href = href;
				}  
			})
		});


 

	})
	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
