<?php
/*
Plugin Name: TNG Accept Cookies Plugin
Plugin URI: http://jontang.se/tng-accept-cookies-plugin
Description: A plugin adding a cookie accept (requeried by Swedish law).
Version: 0.3.2
Author: Jon Täng
Author URI: http://jontang.se
License: GPL2
*/
if ( ! defined( 'WPINC' ) ) {
	die;
}

class TNG_Accept_Cookies {
	
	public	$plugin_version,
			$plugin_prefix,
			$plugin_url,
			$plugin_dir,
			$plugin_name,
			$plugin_basename,
			$domain;
	
	/**
	 * Init plugin
	 *
	 * @since	0.1
	 */	
	function __construct() {
		// Plugin Version
		$this->plugin_version = '0.3.2';
		// Plugin Prefix
		$this->plugin_prefix = 'tng_accept_cookies';
		// The Plugin URL Path
		$this->plugin_url = WP_PLUGIN_URL . '/' . str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );
		// The Plugin DIR Path
		$this->plugin_dir = WP_PLUGIN_DIR. '/' . str_replace( basename( __FILE__), "", plugin_basename(__FILE__) );
		// The Plugin Name. Dirived from the plugin folder name.
		$this->plugin_name = basename(dirname(__FILE__));
		// The Plugin Basename
		$this->plugin_basename = plugin_basename(__FILE__);
		// Variable for the Plugin Prefix
		$this->domain = $this->plugin_prefix;
		
		load_plugin_textdomain( $this->domain, false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' ); 
		
		if ( ! isset($_COOKIE['accepts_cookies']) ) {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_scripts' ) );
			add_action( 'wp_head', array( $this, 'add_inline_style' ), 99 );
			add_action( 'wp_footer', array( $this, 'using_cookies' ) );
			add_action( 'wp_footer', array( $this, 'add_inline_script' ), 99 );
		}
		
		if ( is_admin() ) {
			include_once( $this->plugin_dir . 'inc/accept-cookies-settings.php' );
		}
	}
	
	/**
	 * Add scripts
	 *
	 * @since	0.1
	 */
	function add_scripts() {
		if ( ! wp_style_is( 'bootstrap' ) ) {
			wp_enqueue_style( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', null );
			wp_enqueue_script( 'bootstrap', '//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js', null, null );
		}
		if ( ! wp_style_is( 'fontawesome' ) ) 
			wp_enqueue_style( 'fontawesome', '//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css', null );
			
		wp_enqueue_script( 'jquery-cookie', $this->plugin_url . 'js/jquery.cookie.js', array('jquery'), null );
	}
	
	/**
	 * Using Cookies
	 *
	 * @since	0.1
	 */
	function using_cookies() {
		?>
		<div id="using-cookies">
			<div class="container">
				<div class="row">
					<div class="col-xs-9">
						<?php 
						$option = get_option('tng_accept_cookies');
						if ( isset( $option['cookie_page'] ) && $option['cookie_page'] != 0 )
							$url = get_permalink( $option['cookie_page'] );
						elseif ( isset( $option['cookie_url'] ) && $option['cookie_url'] != '' )
							$url = esc_url( $option['cookie_url'] );
						else
							$url = esc_url( 'http://www.cookielaw.org/' );
							
						// http://www.pts.se/sv/Bransch/Regler/Lagar/Lag-om-elektronisk-kommunikation/Cookies-kakor/Fragor-och-svar-om-kakor-for-anvandare/
						$cookie_info = sprintf( 
								' <a href="%1$s" target="_blank">%2$s</a>.', $url, __('More about cookies', 'tng_accept_cookies') 
							);
						
						$cookie_page = get_page_by_path( 'cookies' );
						if ( $cookie_page ) 
							$cookie_info = sprintf( 
								' %1$s <a href="%3$s">%2$s</a>.', 
								__('Read more about', 'tng_accept_cookies'), 
								__('cookies on this site', 'tng_accept_cookies'), 
								get_permalink( $cookie_page->ID )
							);
							
						printf( '%1$s%2$s', __( 'This website uses cookies to enhance your visit.', 'tng_accept_cookies'), $cookie_info ); ?>
					</div>
					<div class="col-xs-3 text-right">
					<button class="btn btn-success btn-xs" type="button" id="accept-cookies" title="<?php _e( 'I accept cookies', 'tng_accept_cookies' ); ?>">
                    	<i class="fa fa-thumbs-up"></i></button>
                    <button class="btn btn-danger btn-xs" type="button" id="decline-cookies" title="<?php _e( "I dont't accept cookies", 'tng_accept_cookies' ); ?>">
                    	<i class="fa fa-thumbs-down"></i></button>
					</div>
				</div>
			</div>
		</div>
		<?php
		//endif;
	}
	
	/**
	 * Run script
	 *
	 * @since	0.1
	 */
	function add_inline_script() {
	?>
	<script type="text/javascript">
		jQuery(function($) {
			if ( $.cookie( 'accepts_cookies') ) {
				$.cookie( 'accepts_cookies', 1, { expires: 30, path: '/' } );
			}
			
			if ($('#accept-cookies').length) {
				// set accept cookies
				$('#accept-cookies').click( function() {
					$.cookie( 'accepts_cookies', 1, { expires: 30, path: '/' } );
					
					$('#using-cookies').slideUp( "slow", function() { 
						$(this).hide(); 
					});
				});
			}
			
			$('#decline-cookies').click( function() {
				$('#using-cookies').slideUp( "slow", function() { 
					$(this).hide(); 
				});
			});
		});
	</script>
	<?php
	}
	
	function add_inline_style() {
	?>
	<!-- This site is using TNG Accept Cookies plugin v0.3.1 - http://jontang.se/tng-accept-cookies-plugin/ -->
	<style>
	#using-cookies {position:fixed;top:0;left:0;right:0;z-index:9999;background:#000;background:rgba(0,0,0,.65);color:#eee;}
	#using-cookies .row {padding:10px 0;font-size: .8em;}
	</style>
	<!-- / TNG Accept Cookies plugin. -->
	<?php
	}
}
add_action( 'plugins_loaded', create_function('', 'return new TNG_Accept_Cookies();') );