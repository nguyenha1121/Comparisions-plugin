jQuery(document).ready(function($){
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
	///



	var cartWrapper = $('.cd-cart-container');
	//product id - you don't need a counter in your real project but you can use your real product id
	var productId = 0;
	if( cartWrapper.length > 0 ) {
		//store jQuery objects
		var cartBody = cartWrapper.find('.body');
		// var cartBody = $('.wooc-widget-body');

		var cartList = cartBody.find('ul').eq(0);
		// var cartList = $('.wooc-widget-body').find('ul').eq(0);
		var cartTotal = cartWrapper.find('.checkout').find('span');
		var cartTrigger = cartWrapper.children('.cd-cart-trigger');
		var cartCount = cartTrigger.children('.count');
		var addToCartBtn = $('.wooc-a');
		var undo = cartWrapper.find('.undo');
		var undoTimeoutId;


		//init cookie
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
				updateCartCount(cartIsEmpty);
				//update total price
				// updateCartTotal(trigger.data('price'), true);
				ar.push(cs[i]);
			}
		}
		//show cart
		if( Number(cartCount.find('li').eq(0).text()) != 0) cartWrapper.removeClass('empty');


		//add product to cart
		addToCartBtn.on('click', function(event){
			event.preventDefault();
			if($(this).hasClass('added')){
				addToCart($(this));
			}
			else{
				var id = "#wooc-label-"+$(this).attr('data-id');
				removeProduct($(id));
				if( Number(cartCount.find('li').eq(0).text()) == 0) cartWrapper.addClass('empty');
			}
		});


		//open/close cart
		cartTrigger.on('click', function(event){
			event.preventDefault();
			toggleCart();
		});

		//close cart when clicking on the .cd-cart-container::before (bg layer)
		cartWrapper.on('click', function(event){
			if( $(event.target).is($(this)) ) toggleCart(true);
		});

		//delete an item from the cart
		cartList.on('click', '.delete-item', function(event){
			// event.preventDefault();
			// removeProduct($(event.target).parents('.product'));
			var id = "#wooc-label-"+$(this).attr('data-id');
			removeProduct($(id));
			var id = $(this).attr("data-id");
			var idRemove = "#wooc-la-"+id;
			$(idRemove).removeClass("added");
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
			$(cid).addClass("cd-add-to-carts");
			$(cid).removeClass('remove-item');
			$(iid).attr('checked',false);
		});

		//update item quantity
		cartList.on('change', 'select', function(event){
			quickUpdateCart();
		});

		//reinsert item deleted from the cart
		undo.on('click', 'a', function(event){
			clearInterval(undoTimeoutId);
			event.preventDefault();
			cartList.find('.deleted').addClass('undo-deleted').one('webkitAnimationEnd oanimationend msAnimationEnd animationend', function(){
				$(this).off('webkitAnimationEnd oanimationend msAnimationEnd animationend').removeClass('deleted undo-deleted').removeAttr('style');
				quickUpdateCart();
			});
			undo.removeClass('visible');
		});
	}

	function toggleCart(bool) {
		var cartIsOpen = ( typeof bool === 'undefined' ) ? cartWrapper.hasClass('cart-open') : bool;
		
		if( cartIsOpen ) {
			cartWrapper.removeClass('cart-open');
			//reset undo
			clearInterval(undoTimeoutId);
			undo.removeClass('visible');
			cartList.find('.deleted').remove();

			setTimeout(function(){
				cartBody.scrollTop(0);
				//check if cart empty to hide it
				if( Number(cartCount.find('li').eq(0).text()) == 0) cartWrapper.addClass('empty');
			}, 500);
		} else {
			cartWrapper.addClass('cart-open');
		}
	}

	function addToCart(trigger) {
		var cartIsEmpty = cartWrapper.hasClass('empty');
		//update cart product list
		addProduct(trigger.attr('data-id'));
		//update number of items 
		updateCartCount(cartIsEmpty);
		//update total price
		updateCartTotal(trigger.data('price'), true);
		//show cart
		cartWrapper.removeClass('empty');
	}

	function addProduct(id) {
		//this is just a product placeholder
		productId = productId + 1;
		$.ajax({
			type : "post",
			data : 	{'action' : 'woo_compare_ajax','data' : productId , 'id' : id },
			url  : ajax.url,
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
		clearInterval(undoTimeoutId);
		cartList.find('.deleted').remove();
		
		var topPosition = product.offset().top - cartBody.children('ul').offset().top ,
			productQuantity = 1,
			productTotPrice = Number(product.find('.price').text().replace('$', '')) * productQuantity;
		
		product.css('top', topPosition+'px').addClass('deleted');

		//update items count + total price
		updateCartTotal(productTotPrice, false);
		updateCartCount(true, -productQuantity);
		// undo.addClass('visible');

		//wait 8sec before completely remove the item
		undoTimeoutId = setTimeout(function(){
			undo.removeClass('visible');
			cartList.find('.deleted').remove();
		}, 8000);
	}

	function quickUpdateCart() {
		var quantity = 0;
		var price = 0;
		
		cartList.children('li:not(.deleted)').each(function(){
			var singleQuantity = Number($(this).find('select').val());
			quantity = quantity + singleQuantity;
			price = price + singleQuantity*Number($(this).find('.price').text().replace('$', ''));
		});

		cartTotal.text(price.toFixed(2));
		cartCount.find('li').eq(0).text(quantity);
		cartCount.find('li').eq(1).text(quantity+1);
	}

	function updateCartCount(emptyCart, quantity) {
		if( typeof quantity === 'undefined' ) {
			var actual = Number(cartCount.find('li').eq(0).text()) + 1;
			var next = actual + 1;
			
			if( emptyCart ) {
				cartCount.find('li').eq(0).text(actual);
				cartCount.find('li').eq(1).text(next);
			} else {
				cartCount.addClass('update-count');

				setTimeout(function() {
					cartCount.find('li').eq(0).text(actual);
				}, 150);

				setTimeout(function() {
					cartCount.removeClass('update-count');
				}, 200);

				setTimeout(function() {
					cartCount.find('li').eq(1).text(next);
				}, 230);
			}
		} else {
			var actual = Number(cartCount.find('li').eq(0).text()) + quantity;
			var next = actual + 1;
			
			cartCount.find('li').eq(0).text(actual);
			cartCount.find('li').eq(1).text(next);
		}
	}

	function updateCartTotal(price, bool) {
		bool ? cartTotal.text( (Number(cartTotal.text()) + Number(price)).toFixed(2) )  : cartTotal.text( (Number(cartTotal.text()) - Number(price)).toFixed(2) );
	}
});