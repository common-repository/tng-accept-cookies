<?php
class TNG_Accept_Cookies_Settings {
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
	 * @since	0.3
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
	 * @since	0.3
     */
    public function add_plugin_page() {
        
        $option_page = add_options_page(
            __('TNG Accept Cookies Settings', 'tng_accept_cookies'), 
            __('Accept Cookies', 'tng_accept_cookies'), 
            'manage_options', 
            'tng-accept-cookies', 
            array( $this, 'create_admin_page' )
        );
		
		if ( ! $option_page )
			return;

		add_action( "load-$option_page", array( $this, 'settings_help' ) );
		
    }
	
	function settings_help() {
	$help = sprintf( '<h4>%s</h4><p>%s</p>', 
		__('Help', 'tng_accept_cookies'),
		sprintf( '%s <a href="%s">TNG Accept Cookies</a>', __('Find out more at', 'tng_accept_cookies'), 'http://wordpress.org/plugins/tng-accept-cookies/')
	);
	$sidebar = sprintf( '<h4>%s</h4><p><a href="http://jontang.se">Jon Täng</a></p>',
		__('The author', 'tng_accept_cookies')
	);

	$screen = get_current_screen();
	$screen->add_help_tab( array(
		'title' => __( 'Help', 'tng_accept_cookies' ),
		'id' => 'tng-settings-help',
		'content' => $help,
		)
	);
	$screen->set_help_sidebar( $sidebar );
}

    /**
     * Options page callback
	 * @since	0.3
     */
    public function create_admin_page() {
        $this->options = get_option( 'tng_accept_cookies' );
        ?>
        <div class="wrap">
            <h2><?php _e('TNG Accept Cookies Settings', 'tng_accept_cookies'); ?></h2>           
            <form method="post" action="options.php">
            <?php
				//echo '<pre>'; print_r( $this->options ); echo '</pre>';
                // This prints out all hidden setting fields
                settings_fields( 'accept_cookies_group' );   
                do_settings_sections( 'tng-accept-cookies' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
	 * @since	0.3
     */
    public function page_init() {        
        register_setting(
            'accept_cookies_group', // Option group
            'tng_accept_cookies', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'cookie_section', // ID
            __('Cookie Information', 'tng_accept_cookies'), // Title
            array( $this, 'print_cookie_info' ), // Callback
            'tng-accept-cookies' // Page
        );  

        add_settings_field(
            'cookie_page', // ID
            __('Cookie Page', 'tng_accept_cookies'), // Title 
            array( $this, 'print_cookie_page' ), // Callback
            'tng-accept-cookies', // Page
            'cookie_section' // Section           
        );      

        add_settings_field(
            'cookie_url', 
            __('Cookie URL', 'tng_accept_cookies'), 
            array( $this, 'print_cookie_url' ), 
            'tng-accept-cookies', 
            'cookie_section'
        );      
    }

    /**
     * Sanitize each setting field as needed
     * @since	0.3
	 *
     * @param	array	$input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $new_input = array();
        if( isset( $input['cookie_page'] ) )
            $new_input['cookie_page'] = absint( $input['cookie_page'] );

        if( isset( $input['cookie_url'] ) )
            $new_input['cookie_url'] = sanitize_text_field( $input['cookie_url'] );

        return $new_input;
    }

    /** 
     * Print the Section text
	 * @since	0.3
     */
    public function print_cookie_info() {
        ?>
		<div style="position:absolute;right:20px;width:300px;display:block;border:1px solid #ccc;border-radius:5px;float:right;padding:15px;background:#fefefe;">
			<h4><?php _e('Follow me', 'tng_accept_cookies'); ?></h4>
			<p>
				<!--a href="http://jontang.se/" target="_blank">Jon Täng</a><br-->
				<a href="https://www.facebook.com/jontangse" target="_blank">Facebook</a><br>
				<a href="https://google.com/+jontangse" target="_blank">Google+</a><br>
				<a href="https://twitter.com/jontangse/" target="_blank">Twitter</a><br>
			</p>
			
			<p><?php printf('%s <a href="http://wordpress.org/support/view/plugin-reviews/tng-accept-cookies" target="_blank">%s</a>.', __("Don't forget to leave a", 'tng_accept_cookies'), __('Review', 'tng_accept_cookies') ); ?></p>
		</div>
		<?php
    }

    /** 
     * Get the settings option array and print one of its values
	 * @since	0.3
     */
    public function print_cookie_page() {
		$pages = get_pages();
		
		echo '<select name="tng_accept_cookies[cookie_page]">';
		
		printf( '<option value="0" %2$s>%1$s</option>', __('Choose'), selected( $this->options['cookie_page'], 0, false ) );
		
		foreach( $pages as $page )
			printf( '<option value="%1$s" %3$s>%2$s</option>', $page->ID, $page->post_title, selected( $this->options['cookie_page'], $page->ID, false ) );
        
		echo '</select>';
		
		printf( '<p class="description">%s</p>', __('Choose a page for informing your visitor about cookies used on your site.', 'tng_accept_cookies') );
		
    }

    /** 
     * Get the settings option array and print one of its values
	 * @since	0.3
     */
    public function print_cookie_url() {
		$value = (isset($this->options['cookie_url'])) ? esc_attr($this->options['cookie_url']) : '';
        printf( '<input type="text" id="cookie_url" name="tng_accept_cookies[cookie_url]" value="%s" />', $value );
		
		printf( '<p class="description">%s</p>', __('Enter a URL for external information about cookies. Local page overrides URL.', 'tng_accept_cookies') );
    }
}
new TNG_Accept_Cookies_Settings();