(function( $ ) {
	'use strict';
	function setCookie(cname, cvalue, exdays) {
	    var d = new Date();
	    d.setTime(d.getTime() + (exdays*24*60*60*1000));
	    var expires = "expires="+d.toUTCString();
	    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
	}

	function getCookie(cname) {
	    var name = cname + "=";
	    var ca = document.cookie.split(';');
	    for(var i = 0; i < ca.length; i++) {
	        var c = ca[i];
	        while (c.charAt(0) == ' ') {
	            c = c.substring(1);
	        }
	        if (c.indexOf(name) == 0) {
	            return c.substring(name.length, c.length);
	        }
	    }
	    return "";
	}

	$(document).ready(function(){
		
		$('.go-compare-page').click(function(event){
			// event.preventDefault();
			// var href = document.getElementById('woo-go-compare-page').href;
			// var array = document.getElementsByClassName('woo_compare_checkbox');
			// var result=[];
			// for(var i = 0;i<array.length;i++){
			// 	if(array[i].checked){
			// 		var temp = array[i].value;
			// 		result.push(temp);
			// 	}
			// }
			// $.ajax({
			// 	type	:	'post',
			// 	data	:	{'action' : 'woo_compare_ajax','data' : result},
			// 	url		: 	ajax.url	,
			// 	success : function(results){
			// 		if(results=='null'){
			// 			alert('You need choose any product to compare');
			// 		}
			// 		else
			// 		location.href = href;
			// 	}  
			// })
		});
		var ar = [];
		$('.woo_compare_checkbox').change(function(){
			
			if($(this).is(":checked")){
				ar.push($(this).val());
				setCookie('wooc-cks',ar,1);
			}
			else {
				var deleteElement = $(this).val();
				var i = ar.indexOf(deleteElement);
				if (i != -1) {
				    ar.splice(i,1);
				}
				setCookie('wooc-cks',ar,1);
				var cs = document.cookie;
				// console.log(cs);
			}
		})

 

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
