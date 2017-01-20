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

		var ar = [];
		var temp = [];
		var cs = getCookie('wooc-cks');
		cs = cs.split(",");
		cs = cs.sort();
		for(var i = 0 ;i<cs.length;i++){
			if(cs[i]!=""){
				ar.push(cs[i]);
			}
		}
		var label = document.getElementsByClassName("wooc-a");
		// console.log(label[0].nextSibling);
		var c;
		// var className = " " + selector + " ";
		for (c = 0;c<label.length; c++){
			if ( (" " + label[c].className + " ").replace(/[\n\t]/g, " ").indexOf("added") > -1 ){
				// alert("ss");
				var s = label[c].getAttribute("data-data");
				label[c].innerHTML = s;
				label[c].className = label[c].className.replace( /(?:^|\s)cd-add-to-carts(?!\S)/g , '' );
			} 
			else {
				var s = label[c].getAttribute("data-data2");
				label[c].innerHTML = s;
				label[c].className += "cd-add-to-carts";
				label[c].className = label[c].className.replace( /(?:^|\s)added(?!\S)/g , '' );
			}
		}

		// if($(this).hasClass('added')){
		// 	$(this).removeClass("added");
		// 	$(this).text($(this).attr("data-data"));
		// 	$(this).addClass("remove-item");
		// 	$(this).removeClass('cd-add-to-carts');
		// 	var deleteElement = $(this).attr("data-id");
		// 	var i = ar.indexOf(deleteElement);
		// 	if (i != -1) {
		// 	    ar.splice(i,1);
		// 	}
		// 	ar.sort();
		// 	for(var i = 1 ;i<ar.length;i++){
		// 		if(ar[i-1]==ar[i]){
		// 			ar.splice(i-1,1);
		// 		}
		// 	}
		// 	setCookie('wooc-cks',ar,1);
		// 	var cs = document.cookie;
		// 	console.log(cs);
			
		// }
		// else{
		// 	$(this).addClass("added");
		// 	var z = $(this).attr("data-data2");
		// 	$(this).text(z);
		// 	$(this).addClass("cd-add-to-carts");
		// 	$(this).removeClass('remove-item');
		// 	var id = $(this).attr("data-id");
		// 	ar.push(id);
		// 	ar.sort();
		// 	for(var i = 1 ;i<ar.length;i++){
		// 		if(ar[i-1]==ar[i]){
		// 			ar.splice(i-1,1);
		// 		}
		// 	}
		// 	// console.log(ar);
		// 	setCookie('wooc-cks',ar,1);
		// }
		$('.wooc-a').click(function(event){
			event.preventDefault();
			if($(this).hasClass('added')){
				$(this).removeClass("added");
				var txt = $(this).attr("data-data2");
				console.log(txt);
				$(this).text(txt);
 				$(this).addClass("remove-item");
 				$(this).removeClass('cd-add-to-carts');
 				var deleteElement = $(this).attr("data-id");
				var i = ar.indexOf(deleteElement);
				if (i != -1) {
				    ar.splice(i,1);
				}
				ar.sort();
				for(var i = 1 ;i<ar.length;i++){
					if(ar[i-1]==ar[i]){
						ar.splice(i-1,1);
					}
				}
				setCookie('wooc-cks',ar,1);
				var cs = document.cookie;
				console.log(cs);
				
			}
			else{
				$(this).addClass("added");
				var txt = $(this).attr("data-data");
				$(this).text(txt);
 				$(this).addClass("cd-add-to-carts");
 				$(this).removeClass('remove-item');
 				var v = $(this).attr("data-id");
 				ar.push(v);
				ar.sort();
				for(var i = 1 ;i<ar.length;i++){
					if(ar[i-1]==ar[i]){
						ar.splice(i-1,1);
					}
				}
				console.log(ar);
				setCookie('wooc-cks',ar,1);
			}
		});

		//
		
 		// for( c = 0; c < label.length; c++){
 		// 	var ck = label[c].nextElementSibling;
 		// 	if(ck.checked){
 		// 		var s = label[c].getAttribute("data-data");
 		// 		label[c].innerHTML = s;
 		// 		label[c].className = label[c].className.replace( /(?:^|\s)cd-add-to-carts(?!\S)/g , '' );
 		// 		label[c].className += " remove-item";
 		// 	}
 		// 	else {
 		// 		var s = label[c].getAttribute("data-data2");
 		// 		label[c].innerHTML = s;
 		// 		//label[c].innerHTML = "Add to compare";
 		// 		label[c].className = label[c].className.replace( /(?:^|\s)remove-item(?!\S)/g , '' );
 		// 		label[c].className += " cd-add-to-carts";
 		// 	}

 		// }

 		// $('.wooc-a').click(function(){
 		// 	// alert ("sss");
 		// 	if($(this).hasClass("added")){
 		// 		$(this).text($(this).attr("data-data2"));
 		// 		$(this).addClass("cd-add-to-carts");
 		// 		$(this).removeClass('remove-item');
 				
 		// 	}
 		// 	else {
 		// 		$(this).text($(this).attr("data-data"));
 		// 		$(this).addClass("remove-item");
 		// 		$(this).removeClass('cd-add-to-carts');
 		// 	}
 		// })
		
		
		
		// console.log(ar);
		// $('.wooc-a').click(function(){
			
		// 	if($(this).hasClass("added")){
		// 		ar.push($(this).attr("data-id"));
		// 		ar.sort();
		// 		for(var i = 1 ;i<ar.length;i++){
		// 			if(ar[i-1]==ar[i]){
		// 				ar.splice(i-1,1);
		// 			}
		// 		}
		// 		console.log(ar);
		// 		setCookie('wooc-cks',ar,1);
		// 	}
		// 	else {
		// 		var deleteElement = $(this).attr("data-id");
		// 		var i = ar.indexOf(deleteElement);
		// 		if (i != -1) {
		// 		    ar.splice(i,1);
		// 		}
		// 		ar.sort();
		// 		for(var i = 1 ;i<ar.length;i++){
		// 			if(ar[i-1]==ar[i]){
		// 				ar.splice(i-1,1);
		// 			}
		// 		}
		// 		setCookie('wooc-cks',ar,1);
		// 		var cs = document.cookie;
		// 		console.log(cs);
		// 	}
		// })



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
