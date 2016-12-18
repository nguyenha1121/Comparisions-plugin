	<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       nothing.com
 * @since      1.0.0
 *
 * @package    Woo_Compare
 * @subpackage Woo_Compare/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Compare
 * @subpackage Woo_Compare/public
 * @author     Hanguyen <nguyenha1.08112@gmail.com>
 */
class Woo_Compare_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		//ajax
		add_action('wp_ajax_woo_compare_ajax', array(&$this,'woo_compare_ajax'));
		add_action('wp_ajax_nopriv_woo_compare_ajax', array(&$this,'woo_compare_ajax'));
	
		//single page
		if(get_option( 'wooc_option' )['singlepg']=="1"){
			add_action('woocommerce_before_add_to_cart_form',array(&$this,'woo_compare_add_checkbox'));
		}
		//hop page
		if(get_option( 'wooc_option' )['shoppage']=="1"){
			add_action('woocommerce_after_shop_loop_item',array(&$this,'woo_compare_add_checkbox'));
		}	

		add_action('woocommerce_archive_description',array(&$this,'woo_compare_submit'));
		add_action('woocommerce_product_meta_start',array(&$this,'woo_compare_submit'));
		add_action('init',array(&$this,'woo_compare_auto_add_page'));
		add_shortcode('woo_compare_compare_content',array(&$this,'woo_compare_compare_page_sc'));
		add_action('widgets_init',array(&$this,'woo_compare_widget'));

		
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Compare_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Compare_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wooc-style.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name."1", plugin_dir_url( __FILE__ ) . 'css/wooc-reset.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name."2", plugin_dir_url( __FILE__ ) . 'css/woo-compare-public.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name."3", plugin_dir_url( __FILE__ ) . 'css/addtocartstyle.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Woo_Compare_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Woo_Compare_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/modernizr.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name."1", plugin_dir_url( __FILE__ ) . 'js/woo-compare-public.js', array( 'jquery' ), $this->version, false );
		wp_enqueue_script( $this->plugin_name."2", plugin_dir_url( __FILE__ ) . 'js/main.js', array( 'jquery' ), $this->version, false );

		$option= get_option("wooc_option");
		if($option['sub-list']!=2){
			wp_register_script('ez_script', plugin_dir_url( __FILE__ ) . 'js/addtocartmain.js', array( 'jquery' ), $this->version, false);
	   		wp_localize_script('ez_script','ajax',array('url'=> admin_url('admin-ajax.php')));
	    	wp_enqueue_script('ez_script');
		}
		else{
			wp_register_script('wooc-script', plugin_dir_url( __FILE__ ) . 'js/wooc-widget.js', array( 'jquery' ), $this->version, false);
		    wp_localize_script('wooc-script','ajax2',array('url'=> admin_url('admin-ajax.php')));
		    wp_enqueue_script('wooc-script');
		}

	   

	}

	/**
	 * Init widget
	 */
	function woo_compare_widget(){
		$option= get_option("wooc_option");
		if($option['sub-list']==2){
			register_widget('Wooc_Widget');
		}
		// register_widget('Wooc_Widget');
	}
	/*
	* Get price of product
	*/
	function wc_get_product_price( $product_id ) {
	    // $_product = wc_get_product( $product_id );
	    $price = get_post_meta( $product_id, '_regular_price', true);
	    return $price;
	}
	    
	/**
	 * Process AJAX
	 */

	public function woo_compare_ajax(){

		$sec_id = $_POST['data'];
		$id = intval($_POST['id']);
		$product = get_post($id);
		$price = get_post_meta( $id, '_regular_price', true);
		if(get_post_thumbnail_id( $id )==''){
			$url[0] = 'http://honganhtesol.com/Home/wp-content/uploads/2016/09/default.jpg';
		}
		else $url = wp_get_attachment_image_src( get_post_thumbnail_id( $id ), 'thumbnail' );
		$title = $product->post_title;
		
		if(!isset(get_option(wooc_option)['title-2'])||(get_option(wooc_option)['title-2']=="")) {
			$e = "Remove";
		}
		else $e = get_option(wooc_option)['title-2'];
		echo '<li style="" class="product" data-id="'.$id.'" id="wooc-label-'.$id.'"><div class="product-image"><a href="#0"><img src="'.$url[0].'" alt="placeholder"></a></div><div class="product-details"><div><h3><a href="#0">'.$title.'</a></h3><span class="price">'.$price.'</span></div><div class="actions" style="position: relative;"><a style="" href="#0" class="delete-item">'.$e.'</a></div></li>';
		die();
	}

	/**
	 * Auto create page comapre
	 */
	public function woo_compare_auto_add_page(){
		$check = get_option('woo_compare_page_id');
		if($check) {
			$this->woo_compare_check_compare_page($check);
			return "";
		}
		if(!isset(get_option(wooc_option)['title-6'])||(get_option(wooc_option)['title-6']=="")) {
				$e = "Products Comparison Table";
		}
		else $e = get_option(wooc_option)['title-6'];
		$page = array(
			'post_type'	=> 'page',
			'post_content'	=> '[woo_compare_compare_content]',
			'post_status'	=> 'publish',
			'post_title'	=> $e
		);
		$id = wp_insert_post($page);
		add_option('woo_compare_page_id',$id);
	}
	/**
	 * Check compare page
	 */

	public function woo_compare_check_compare_page($id){
		$page = get_post($id);
		if(!isset(get_option(wooc_option)['title-6'])||(get_option(wooc_option)['title-6']=="")) {
			$e = "Products Comparison Table";
		}
		else $e = get_option(wooc_option)['title-6'];
		if($page == null || $page->post_status=='trash' || $page->post_tittle != $e){
			$post = array(
				'ID'		=> $id,
				'post_type'	=> 'page',
				'post_content'	=> '[woo_compare_compare_content]',
				'post_status'	=> 'publish',
				'post_title'	=> $e
			);
			$i = wp_insert_post($post);
		}

	}
	/**
	 * Add 
	 */

	public function woo_compare_add_checkbox(){
		global $product;
		// var_dump($product->price);
		// var_dump($_COOKIE['wooc-cks']);
		$scoo = explode(",", $_COOKIE['wooc-cks']);
		$check = false;
		foreach ($scoo as $key => $value) {
			if($value == $product->id){
				$check = true;
			}
		}
		// var_dump(!isset(get_option(wooc_option)['title-2']));
		?>

		<label class="button wooc-label cd-add-to-cart" id="wooc-la-<?php echo $product->id;?>" data-price="<?php echo $product->price; ?>" data-data="<?php 
			if(!isset(get_option(wooc_option)['title-2'])||(get_option(wooc_option)['title-2']=="")) {
				$e = "Remove";
			}
			else $e = get_option(wooc_option)['title-2'];
			echo $e;
		?>" data-data2="<?php 
			if(!isset(get_option(wooc_option)['title-1'])||(get_option(wooc_option)['title-1']=="")) {
				$e = "Add to compare";
			}
			else $e = get_option(wooc_option)['title-1'];
			echo $e;
		?>" data-id="<?php echo $product->id;?>" style="-webkit-appearance: push-button; -moz-appearance: button; cursor: pointer;" for="<?php echo "wooc-checkbox-".$product->id; ?>">Add to compare</label>
		<input id="<?php echo "wooc-checkbox-".$product->id; ?>"  style="display:none;" class="woo_compare_checkbox" type="checkbox"<?php if($check){ echo "checked";} ?> name="add_compare" value="<?php
		// id product
			echo $product->id;
		?>">
		<?php
	}

	public function woo_compare_submit(){
		$page = get_post(get_option('woo_compare_page_id'));

		?>
		<!-- <a id="woo-go-compare-page" class="go-compare-page" href="<?php echo $page->guid ;?>">View Comparisions</a> -->

		<!-- <main>
			<input href="#0" type="button" class="cd-add-to-cart" data-price="25.99">
		</main> -->

		<div class="cd-cart-container empty">
			<a href="#0" class="cd-cart-trigger">
				List Products
				<ul class="count"> <!-- cart items count -->
					<li>0</li>
					<li>0</li>
				</ul> <!-- .count -->
			</a>

			<div class="cd-cart">
				<div class="wrapper">
					<header>
						<h2>Cart</h2>
						<span class="undo">Item removed. <a href="#0">Undo</a></span>
					</header>
					
					<div class="body">
						<ul>
							<!-- products added to the cart will be inserted here using JavaScript -->
						</ul>
					</div>

					<footer>
						<a href="<?php echo $page->guid ;?>" class="checkout btn"><em><?php
							if(!isset(get_option(wooc_option)['title-3'])||(get_option(wooc_option)['title-3']=="")) {
								echo "View Comparisions";
							}
							else echo get_option(wooc_option)['title-3'];?></em>
						</a>
					</footer>
				</div>
			</div> <!-- .cd-cart -->
		</div> <!-- cd-cart-container -->
		<script>
			if( !window.jQuery ) document.write('<script src="<?php echo  plugin_dir_url( __FILE__ ) ."js/jquery-3.0.0.min.js"; ?>"><\/script>');
		</script>
		<?php
	}

	public function woo_compare_compare_page_sc($args,$content){
		$_pr = new WC_Product_Factory();
		$ids = $_COOKIE['wooc-cks'];
		// var_dump($ids);
		if(!isset($_COOKIE['wooc-cks'])||$ids=="") {
			// when not select another product
			echo "Not item selcected!";
			return ;
		}
		$s = explode(",", $ids);
		// var_dump($_COOKIE);
		// list item selected
		$list = wc_get_attribute_taxonomies();
		$actv = get_option('wooc_option')['order-pub'];
		$actv = explode(",", $actv);
		// var_dump($actv);
		?>
		<!-- <section class="cd-intro">
			<h1><?php _e('Products Comparison Table','woo-compare'); ?></h1>
		</section> -->

		<section class="cd-products-comparison-table">
			<header>
				<h2><?php _e('Compare Models','woo-compare'); ?></h2>

				<div class="actions">
					<a href="#0" class="reset">Reset</a>
					<a href="#0" class="filter">Filter</a>
				</div>
			</header>
			<div class="cd-products-table">
				<div class="features">
					<div class="top-info">Models</div>
					<ul class="cd-features-list">
						
						<?php 
							
							// var_dump($list);
							
							$list_actv = array();
							$list_ac = array();

							if(($actv!='')){
								foreach ($actv as $key => $value) {
									if($value == "price"){
										
										if(!isset(get_option(wooc_option)['title-4'])||(get_option(wooc_option)['title-4']=="")) {
											$e = "Price";
										}
										else $e = get_option(wooc_option)['title-4'];
										array_push($list_actv,$e);
										array_push($list_ac,'price');
									}
									if($value == "rating"){
										if(!isset(get_option(wooc_option)['title-5'])||(get_option(wooc_option)['title-5']=="")) {
											$e = "Customer rating";
										}
										else $e = get_option(wooc_option)['title-5'];
										array_push($list_actv,$e);
										array_push($list_ac,'rating');
									}
									foreach ($list as $k => $v) {
										// var_dump($v->attribute_name==$value);

										if($value == $v->attribute_name){
											array_push($list_actv,$v->attribute_label);
											array_push($list_ac,'pa_'.$v->attribute_name);
										}
									}
								}
								foreach ($list_actv as $key => $value) {
									echo '<li>'.$value.'</li>';
								}
							}
							
							
							
							// var_dump($list_ac);
						?>
					</ul>
				</div> <!-- .features -->
				<div class="cd-products-wrapper">
					<ul class="cd-products-columns">		
					<?php
					foreach ($s as $k => $value) {
						$post = $_pr->get_product($value);
						// var_dump($post);
						// var_dump($post->get_average_rating( ));
						?>
						<li class="product">
							<div class="top-info">
								<div class="check"></div>
								<?php 
								$img_url = wp_get_attachment_image_src( get_post_thumbnail_id( $value ), 'thumbnail' );
								if ($img_url[0]==""){
									$img_url[0]="http://honganhtesol.com/Home/wp-content/uploads/2016/09/default.jpg";
								}
								?>
								<div class="wooc-img">
									<img width="150px" height="100px" class="wooc-header-product-thumbnail" src="<?php  echo $img_url[0]; ?>" data-id="<?php echo $value; ?>">
								</div>
								<h4><a class="wooc-header-product" href="<?php echo (get_permalink($value)); ?>"><?php echo $post->post->post_title; ?></a></h4>
							</div> <!-- .top-info -->
							<ul class="cd-features-list">
								<!--   -->
								<?php
								$arttris = $post->get_attributes();
								// foreach attributes
								// var_dump(wc_get_product_terms( $value, 'pa_mau-sac', array( 'fields' => 'names' ) ));
								foreach ($list_ac as $k => $v) {
									// echo $values[name];
									if($v == 'price'){?>
										<li><?php echo ($this->wc_get_product_price($value)); ?></li>
									<?php }
									if($v == 'rating'){?>
										<li class="wooc-ratting" style="min-height: 76px;">
											<?php 	$rating_count = $post->get_rating_count();
													$review_count = $post->get_review_count();
													$average      = $post->get_average_rating(); ?>
												<div class="woocommerce-product-rating" itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
												<div class="star-rating" style="margin:0 auto;" title="<?php printf( __( 'Rated %s out of 5', 'woocommerce' ), $average ); ?>">
													<span style="width:<?php echo ( ( $average / 5 ) * 100 ); ?>%">
														<strong itemprop="ratingValue" class="rating"><?php echo esc_html( $average ); ?></strong> <?php printf( __( 'out of %s5%s', 'woocommerce' ), '<span itemprop="bestRating">', '</span>' ); ?>
														<?php printf( _n( 'based on %s customer rating', 'based on %s customer ratings', $rating_count, 'woocommerce' ), '<span itemprop="ratingCount" class="rating">' . $rating_count . '</span>' ); ?>
													</span>
												</div>
												<?php if ( comments_open() ) : ?><a href="#reviews" class="woocommerce-review-link" rel="nofollow">(<?php printf( _n( '%s customer review', '%s customer reviews', $review_count, 'woocommerce' ), '<span itemprop="reviewCount" class="count">' . $review_count . '</span>' ); ?>)</a><?php endif ?>
												</div>
											</li>
										<?php
										}
									
									if($v != 'rating' && $v != 'price'){
										echo "<li>";
										$ck = false;
										foreach ($arttris as $keys => $values) {
											if($values[name]==$v){
												$vl = wc_get_product_terms( $value, $v , array( 'fields' => 'names' ) );
												// var_dump($vl);
													foreach ($vl as $ke => $va) {
														
														echo $va."&nbsp;";
														$ck = true;
													}	
												}
												
											}
										if(!$ck) echo "None";
										echo '</li>';
									}
								}
								?>
							</ul>
						</li> <!-- .product -->
						<?php	
					}
					?>
					</ul> <!-- .cd-products-columns -->
				</div> <!-- .cd-products-wrapper -->
				<ul class="cd-table-navigation">
					<li><a href="#0" class="prev inactive">Prev</a></li>
					<li><a href="#0" class="next">Next</a></li>
				</ul>
			</div> <!-- .cd-products-table -->
		</section> <!-- .cd-products-comparison-table -->
	<?php

		
		
	}	
}

class Wooc_Widget extends WP_Widget {
	/**
	 * Constructor
	 */
	function __construct(){
		parent::__construct(
			'wooc_widget',
			'Comparisions Widget',
			array(
				'description'	=>	'Create comparisions widget.'
				)
		);
	}
	/**
	 * Form widget
	 */
	function form($instance){
		$default = array(
				'title'	=> 'Comparisions Table'
			);
		$instance = wp_parse_args((array)$instance , $default );
		$title = esc_attr($instance['title']);
		echo '<p>Name widget:</p>';
		echo '<input type="text" class = "widefat" name="'.$this->get_field_name('title').'" value = "'.$title.'"/>';
	}
	/**
	 * Save form
	 */
	function update($new_instance,$old_instance){
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
	/**
	 * Show widget
	 */
	function widget(  $args,$instance ){
		extract($args);
		// var_dump($args);
		$title = $instance['title'];
		echo $before_widget;
		echo $before_title.$title.$after_title;
		// content here!
		$page = get_post(get_option('woo_compare_page_id'));
		?>
		<div class="wooc-widget-body wooc-widget-css">
			<ul>
				<!-- Product here -->
			</ul>
			<a class="button" href="<?php echo $page->guid ;?>" class="checkout btn"><em><?php 
				if(!isset(get_option(wooc_option)['title-3'])||(get_option(wooc_option)['title-3']=="")) {
				$e = "View Comparisions";
			}
			else $e = get_option(wooc_option)['title-3'];
			echo $e; ?></em></a>
		</div>

		<?php
		echo $after_widget;
	}
}
