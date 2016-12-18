jQuery(document).ready(function($){
	var cartWrapper = $('.cd-cart-container');
	//product id - you don't need a counter in your real project but you can use your real product id
	var productId = 0;
	///get/set cookie
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


	function addToCart(trigger) {
		var cartIsEmpty = cartWrapper.hasClass('empty');
		//update cart product list
		addProduct(trigger.attr('data-id'));
		//update number of items 
		
		//show cart
		
	}


	function addProduct(id) {
		//this is just a product placeholder
		productId = productId + 1;
		$.ajax({
			type : "post",
			data : 	{'action' : 'woo_compare_ajax','data' : productId , 'id' : id },
			url  : ajax2.url,
			success : function(response){
				// console.log(response);
				var productAdded = $(response);
				cartList.prepend(productAdded);
			}
		});
		// var productAdded = $('<li class="product"><div class="product-image"><a href="#0"><img src="img/product-preview.png" alt="placeholder"></a></div><div class="product-details"><h3><a href="#0">Product Name</a></h3><span class="price">$25.99</span><div class="actions"><a href="#0" class="delete-item">Delete</a><div class="quantity"><label for="cd-product-'+ productId +'">Qty</label><span class="select"><select id="cd-product-'+ productId +'" name="quantity"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option></select></span></div></div></div></li>');
		// cartList.prepend(productAdded);
	}

	function removeProduct(product) {
		// clearInterval(undoTimeoutId);
		product.remove();
		
		// var topPosition = product.offset().top - cartBody.children('ul').offset().top ;
		// product.css('top', topPosition+'px').addClass('deleted');
		// cartList.find('.deleted').remove();
		
	}
	///
	var productId = 0;
	var cartList = $('.wooc-widget-body').find('ul').eq(0);
	var cartBody = $('.wooc-widget-body');
	var addToCartBtn = $('.wooc-label');
	//init
	var ar = [];
		var cs = getCookie('wooc-cks');
		cs = cs.split(",");
		cs = cs.sort();
		for(var i = 0 ;i<cs.length;i++){
			if(cs[i]!=""){
				var cartIsEmpty = cartWrapper.hasClass('empty');
				//update cart product list
				addProduct(cs[i]);
				//update number of items 
				//update total price
				// updateCartTotal(trigger.data('price'), true);
				ar.push(cs[i]);
			}
		}


	addToCartBtn.on('click', function(event){
		// event.preventDefault();
		if(!$(this).hasClass('cd-add-to-cart')){
			addToCart($(this));
		}
		else{
			var id = "#wooc-label-"+$(this).attr('data-id');
			removeProduct($(id));
			
		}
	});

	cartList.on('click', '.delete-item', function(event){
			// event.preventDefault();
			removeProduct($(event.target).parents('.product'));
			var pr = $(event.target).parents('.product');
			var id = pr.attr('data-id');
			var i = ar.indexOf(id);
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
			var cid = "#wooc-la-"+id;
			var iid = "#wooc-checkbox-"+id;
			$(cid).text("Add to compare");
			$(cid).addClass("cd-add-to-cart");
			$(cid).removeClass('remove-item');
			$(iid).attr('checked',false);
		});



});