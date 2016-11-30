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

	}

	/// function add setting page

	public function woo_compare_setting_page(){

	}
	public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'My Settings', 
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
        $this->options = get_option( 'wooc_option_name' );
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
            'wooc_option_name', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Setting Attributes', // Title
            array( $this, 'print_section_info' ), // Callback
            'wooc-setting-admin' // Page
        );        

        add_settings_field(
            'attributes', 
            'Attributes', 
            array( $this, 'title_callback' ), 
            'wooc-setting-admin', 
            'setting_section_id'
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

        return $new_input;
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
    public function title_callback()
    {
        printf(	
            '<input type="checkbox" id="attributes" name="wooc_option_name[attributes]" value="%s" />',
            isset( $this->options['attributes'] ) ? esc_attr( $this->options['attributes']) : ''
        );
        printf(
            '<input type="checkbox" id="attributes" name="wooc_option_name[attributes]" value="%s" />',
            isset( $this->options['attributes'] ) ? esc_attr( $this->options['attributes']) : ''
        );
        printf(
            '<input type="checkbox" id="attributes" name="wooc_option_name[attributes]" value="ddddd" />',
            isset( $this->options['attributes'] ) ? esc_attr( $this->options['attributes']) : ''
        );
    }

    function create_section_for_multi_select($value) { 
		create_opening_tag($value);
		echo '<ul class="mnt-checklist" id="'.$value['id'].'" >'."\n";
		foreach ($value['options'] as $option_value => $option_list) {
			$checked = " ";
			if (get_option($value['id']."_".$option_value)) {
				$checked = " checked='checked' ";
			}
			echo "<li>\n";
			echo '<input type="checkbox" name="'.$value['id']."_".$option_value.'" value="true" '.$checked.' class="depth-'.($option_list['depth']+1).'" />'.$option_list['title']."\n";
			echo "</li>\n";
		}
		echo "</ul>\n";
		create_closing_tag($value);
	 }
}
