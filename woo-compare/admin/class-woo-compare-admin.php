<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       nothing.com
 * @since      1.0.0
 *
 * @package    Woo_Compare
 * @subpackage Woo_Compare/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Woo_Compare
 * @subpackage Woo_Compare/admin
 * @author     Hanguyen <nguyenha1.08112@gmail.com>
 */
class Woo_Compare_Admin {

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

	private $options;
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/woo-compare-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/woo-compare-admin.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name."admin1", 'https://code.jquery.com/jquery-1.12.4.js', array( 'jquery' ), $this->version, false );
        wp_enqueue_script( $this->plugin_name."admin2", 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ), $this->version, false );

	}

	/// function add setting page

	public function woo_compare_setting_page(){

	}
	public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Comparisions plugin settings', 
            'manage_options', 
            'wooc-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'wooc_option' );
        ?>
        <div class="wrap">
            <h1>Setting Comparisions</h1>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'wooc_option_group' );
                do_settings_sections( 'wooc-setting-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }
    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'wooc_option_group', // Option group
            'wooc_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'wooc_setting_section', // ID
            'Setting Attributes', // Title
            array( $this, 'print_section_info' ), // Callback
            'wooc-setting-admin' // Page
        );         

        add_settings_field(
            'attributes', 
            'Attributes', 
            array( $this, 'attrs_callback' ), 
            'wooc-setting-admin', 
            'wooc_setting_section'
        );  

        add_settings_field(
            'customize', // ID
            'Enable/Unable in product single/shop page', // Title 
            array( $this, 'customize_callback' ), // Callback
            'wooc-setting-admin', // Page
            'wooc_setting_section' // Section           
        );
        add_settings_field(
            'title_button', // ID
            'Edit title button', // Title 
            array( $this, 'title_callback' ), // Callback
            'wooc-setting-admin', // Page
            'wooc_setting_section' // Section           
        );    
        add_settings_field(
            'sub-list', // ID
            'Choose display sub-list product', // Title 
            array( $this, 'sub_list_callback' ), // Callback
            'wooc-setting-admin', // Page
            'wooc_setting_section' // Section           
        );  
    }
 	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
// sanitize_text_field
        if( isset( $input['attributes'] ) )
            $new_input['attributes'] = ( $input['attributes'] );

        // return $new_input;
        return $input;
    }
    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Select attributes what you want to compare:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function attrs_callback()
    {
    	$attr = wc_get_attribute_taxonomies();
        $hidden = "wooc_option[order]";
        $hidden2 = "wooc_option[order-pub]";
        $check = get_option('wooc_option')[order];
        $check = explode(",", $check);
        // var_dump($check);
        
        echo '<input id="wooc-order" type="hidden" name="'.$hidden.'" value="">';
        echo '<input id="wooc-order-2" type="hidden" name="'.$hidden2.'" value="">';
        echo "<ul id='sortable'>";
        $order = 0;
        
        if($check[0]!=""){
            foreach ($check as $k => $v) {
                $v = explode(";", $v);
                if($v[0]=='price'){
                    if($v[1]=='1'){
                        
                        

                        echo '<li class="ui-state-default wooc-listyle">';
                        echo '<p>' .'Price'.'&nbsp;</p>';
                        echo '<div>';
                        echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                        printf( 
                            '<input class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'price',
                             ''
                        );
                        echo '</div>';
                        echo "</li>";
                        echo "\n";
                    }
                    else{
                        

                        echo '<li class="ui-state-default wooc-listyle">';
                        echo '<p>' .'Price'.'&nbsp;</p>';
                        echo '<div>';
                        echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                        printf( 
                            '<input class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'price',
                             'un'
                        );
                        echo '</div>';
                        echo "</li>";
                        echo "\n";
                    }
                }
                if($v[0]=='rating'){
                    if($v[1]=='1'){
                        

                        echo '<li class="ui-state-default wooc-listyle">';
                        echo '<p>' .'Customer rating'.'&nbsp;</p>';
                        echo '<div>';
                        echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                        printf( 
                            '<input class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'rating',
                             ''
                        );
                        echo '</div>';
                        echo "</li>";
                        echo "\n";
                    }
                    else{

                        echo '<li class="ui-state-default wooc-listyle">';
                        echo '<p>' .'Price'.'&nbsp;</p>';
                        echo '<div>';
                        echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                        printf( 
                            '<input class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'rating',
                             'un'
                        );
                        echo '</div>';
                        echo "</li>";
                        echo "\n";
                    }
                }
                foreach ($attr as $key => $value) {
                    $index = $value->attribute_name;
                    $id = $value->attribute_id;
                    if($index == $v[0]){
                        if($v[1]=='1'){
                            echo '<li class="ui-state-default wooc-listyle">';
                            echo '<p>' .$value->attribute_label.'&nbsp;</p>';
                            echo '<div>';
                            echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                            printf( 
                                '<input order="'.$order.'" class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,$index,
                                 ''
                            );
                            echo '</div>';
                            echo "</li>";
                            echo "\n";
                            $order += 1;
                        }
                        else {
                            echo '<li class="ui-state-default wooc-listyle">';
                            echo '<p>' .$value->attribute_label.'&nbsp;</p>';
                            echo '<div>';
                            echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                            printf( 
                                '<input order="'.$order.'" class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,$index,
                                 'un'
                            );
                            echo '</div>';
                            echo "</li>";
                            echo "\n";
                            $order += 1;
                        }
                    }     
                }
            }
        }
        //init setting option
        else{
            echo '<li class="ui-state-default wooc-listyle">';
            echo '<p>' .'Price'.'&nbsp;</p>';
            echo '<div>';
            echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
            printf( 
                '<input class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'price',
                 'un'
            );
            echo '</div>';
            echo "</li>";
            echo "\n";
            echo '<li class="ui-state-default wooc-listyle">';
            echo '<p>' .'Customer rating'.'&nbsp;</p>';
            echo '<div>';
            echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
            printf( 
                '<input order="'.$order.'" class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,'rating',
                 'un'
            );
            echo '</div>';
            echo "</li>";
            echo "\n";
            foreach ($attr as $key => $value) {
                $index = $value->attribute_name;
                $id = $value->attribute_id;
                echo '<li class="ui-state-default wooc-listyle">';
                echo '<p>' .$value->attribute_label.'&nbsp;</p>';
                echo '<div>';
                echo '<label style="font-size : 20px; color : #000; " for="'.$index.'"></label>';
                printf( 
                    '<input order="'.$order.'" class="wooc-input squaredThree" type="checkbox" id="%1s" name="%2s" %3schecked  value="1"/><br>',$index,$index,
                     'un'
                );
                echo '</div>';
                echo "</li>";
                echo "\n";
            }
        }
    	
        echo "</ul>";
        echo '<script>
                  $( function() {
                    var order = [];
                    var order_pub = [];
                    var input = document.getElementsByClassName("wooc-input");
                    for (var i = 0; i<input.length ; i++){
                        if( input[i].checked ){
                            order.push(input[i].name+";1");
                            order_pub.push(input[i].name);
                        }
                        else{
                            order.push(input[i].name+";0");
                        }
                    }
                    console.log(order);
                    $("#wooc-order").val(order);
                    $("#wooc-order-2").val(order_pub);
                    $(".wooc-input").change(function(){
                        var order = [];
                        var order_pub = [];
                        var input = document.getElementsByClassName("wooc-input");
                        for (var i = 0; i<input.length ; i++){
                            if( input[i].checked ){
                                order.push(input[i].name+";1");
                                order_pub.push(input[i].name);
                            }
                            else{
                                order.push(input[i].name+";0");
                            }
                        }
                        console.log(order);
                        $("#wooc-order").val(order);
                        $("#wooc-order-2").val(order_pub);
                    });
                    $( "#sortable" ).sortable({
                      placeholder: "ui-state-highlight",
                      stop: function(even,ui){
                        var input = document.getElementsByClassName("wooc-input");
                        // input[0].value = "hahah";
                        var order = [];
                        var order_pub = [];
                        for (var i = 0; i<input.length ; i++){
                            if( input[i].checked ){
                                order.push(input[i].name+";1");
                                order_pub.push(input[i].name);
                            }
                            else{
                                order.push(input[i].name+";0");
                            }
                        }
                        $("#wooc-order").val(order);
                        $("#wooc-order-2").val(order_pub);
                      }
                    });
                    $( "#sortable" ).disableSelection();
                  } );
            </script>';
    }

    public function customize_callback()
    {
    	$index = "singlepg";
        $index2 = "shoppage";
        // var_dump($this->options[$index]);
    	echo '<label style="font-size : 20px; color : #000; " for="'.$index.'">Single product page? &nbsp;</label>';
        printf(	
	            '<input type="checkbox" class="squaredThree" id="%1s" name="wooc_option[%2s]" %3schecked  value="1"/>',$index,$index,
	            isset( $this->options[$index] ) ? '' : 'un'
	    );
        echo "<br>";
        echo '<label style="font-size : 20px; color : #000; " for="'.$index2.'">Shop product page? &nbsp;</label>';
        printf( 
                '<input type="checkbox" class="squaredThree" id="%1s" name="wooc_option[%2s]" %3schecked  value="1"/>',$index2,$index2,
                isset( $this->options[$index2] ) ? '' : 'un'
        );
    }

    public function title_callback(){
        // var_dump($this->options['title-6']);
        $index = array(
            "title-1"   =>  "Add to compare",
            "title-2"   =>  "Remove",
            "title-3"   =>  "View Comparisions",
            "title-4"   =>  "Price",
            "title-5"   =>  "Customer rating",
            "title-6"   =>  "Products Comparison Table"
            );
        foreach ($index as $key => $value) {
            echo "<div class='wooc-title-button ".$value."'>";
            echo '<label style="font-size : 20px; color : #000; " for="'.$key.'"> '.$value.'&nbsp;</label>';
            printf( 
                    '<input type="text" class="squaredThree" id="%1s" name="wooc_option[%2s]"   value="%3s"/>',$key,$key,
                    (isset( $this->options[$key] )) ? $this->options[$key] :  $value
            );
            echo "</div>";
            echo "<br>";
        }
    }        

    public function sub_list_callback(){
        $index = "sub-list";
        echo '<label style="font-size : 20px; color : #000; " for="'.$index."1".'">Button?  </label>';
        printf( 
                '<input type="radio" id="%1s"  name="wooc_option[%2s]" %3schecked  value="1"/>',$index."1",$index,
                isset( $this->options[$index] )&&($this->options[$index] == 1) ? '' : 'un'
            );
        echo "</br>";
        echo '<label style="font-size : 20px; color : #000; " for="'.$index."2".'">Widget?  </label>';
        printf( 
                '<input type="radio" id="%1s" name="wooc_option[%2s]" %3schecked  value="2"/>',$index."2",$index,
                isset( $this->options[$index] )&&($this->options[$index] == 2) ? '' : 'un'
            );
    }

  
}
