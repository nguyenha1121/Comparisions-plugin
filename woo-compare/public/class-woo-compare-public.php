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

		add_action('woocommerce_after_shop_loop_item',array(&$this,'woo_compare_add_checkbox'));
		add_action('woocommerce_before_add_to_cart_form',array(&$this,'woo_compare_add_checkbox'));
		add_action('woocommerce_archive_description',array(&$this,'woo_compare_submit'));
		add_action('woocommerce_product_meta_start',array(&$this,'woo_compare_submit'));
		add_action('init',array(&$this,'woo_compare_auto_add_page'));
		add_shortcode('woo_compare_compare_content',array(&$this,'woo_compare_compare_page_sc'));
		// add_action('woocommerce_single_product_summary',array(&$this,'isa_woocommerce_all_pa'));

		
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
		wp_enqueue_script( $this->plugin_name."ss", plugin_dir_url( __FILE__ ) . 'js/main.js', array( 'jquery' ), $this->version, false );
		wp_register_script('ez_script', plugin_dir_url( __FILE__ ) . 'js/woo-compare-public.js', array( 'jquery' ), $this->version, false);
	    wp_localize_script('ez_script','ajax',array('url'=> admin_url('admin-ajax.php')));
	    wp_enqueue_script('ez_script');

	}

	////////////////////////////////////--add checkbox in product--\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\




	function wc_get_product_price( $product_id ) {
	    $_product = wc_get_product( $product_id );
	    $price = $_product->get_price();
	    return $price;
	}
	    
	

	public function woo_compare_ajax(){

		$result = $_POST['data'];
		$name = "ids";
		// $old = array();
		// $old = $_COOKIE[$name];	
		setcookie( $name, json_encode($result), time() + (86400), "/" );	
		if($result == null){
			echo 'null';
		}
		die();
	}


	public function woo_compare_auto_add_page(){
		$check = get_option('woo_compare_page_id');
		if($check) {
			$this->woo_compare_check_compare_page($check);
			return "";
		}
		$page = array(
			'post_type'	=> 'page',
			'post_content'	=> '[woo_compare_compare_content]',
			'post_status'	=> 'publish',
			'post_title'	=> 'Products Comparison Table'
		);
		$id = wp_insert_post($page);
		add_option('woo_compare_page_id',$id);
	}


	public function woo_compare_check_compare_page($id){
		$page = get_post($id);
		// var_dump($_COOKIE);
		// var_dump($page);
		if($page == null || $page->post_status=='trash'){
			$post = array(
				'ID'		=> $id,
				'post_type'	=> 'page',
				'post_content'	=> '[woo_compare_compare_content]',
				'post_status'	=> 'publish',
				'post_title'	=> 'Products Comparison Table'
			);
			$i = wp_insert_post($post);
		}

	}




	/////////ex function


	////////////




	public function woo_compare_add_checkbox(){
		global $product;
		// var_dump($product->list_attributes());
		?>
		<input class="woo_compare_checkbox" type="checkbox" name="add_compare" value="<?php
		// id product
			echo $product->id;
		?>">
		<?php
	}

	public function woo_compare_submit(){
		$page = get_post(get_option('woo_compare_page_id'));
		?>
		<a id="woo-go-compare-page" class="go-compare-page" href="<?php echo $page->guid ;?>">View Comparisions</a>
		<?php
	}

	public function woo_compare_compare_page_sc($args,$content){
		$_pr = new WC_Product_Factory();
		$ids = $_COOKIE['wooc-cks'];
		if(!isset($_COOKIE['wooc-cks'])) {
			// when not select another product
			echo "Not item selcected!";
			return ;
		}
		$s = explode(",", $ids);
		// var_dump($_COOKIE);
		// list item selected
		$list = wc_get_attribute_taxonomies();
		$actv = get_option('wooc_option');
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
						<li>Price</li>
						<li>Customer Rating</li>
						<?php 
							
							// var_dump($list);
							
							$list_actv = array();
							$list_ac = array();

							if(($actv!='')){
								foreach ($list as $k => $v) {
									foreach ($actv as $key => $value) {
										if($key == $v->attribute_name){
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
								<li><?php echo ($this->wc_get_product_price($value)); ?></li>
								<li>
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
								<!--   -->
								<?php
								$arttris = $post->get_attributes();
								// foreach attributes
								// var_dump(wc_get_product_terms( $value, 'pa_mau-sac', array( 'fields' => 'names' ) ));
								foreach ($list_ac as $k => $v) {
									// echo $values[name];
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
	