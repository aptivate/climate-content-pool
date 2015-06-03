<?php
/*
Plugin Name: Climate Content Pool
Description: Pushes posts into the Reegle Content Pool
Version: 0.1
Author: Aptivate
*/

require_once dirname( __FILE__ ) . '/pusher.php';

class ClimateContentPool {

	private $pusher;

	function __construct( $pusher ) {
		$this->pusher = $pusher;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
	}

	function admin_menu() {
		add_options_page(
			'Climate Content Pool',
			'Climate Content Pool',
			'manage_options',
			'climate-content-pool',
			array( 'ClimateContentPool', 'add_options_page_callback' ));

	}

	function admin_init()
	{
		self::set_defaults();

		register_setting(
			'climate_content_pool_general_settings',
			'climate_content_pool_general_settings'
		);
	}

	function admin_notices() {
		global $pagenow;

		if ( $pagenow != 'post.php' ) {
			return;
		}

		global $post;

		if ( $post ) {
			$single = true;
			$message = get_post_meta( $post->ID, 'content_pool_message', $single );

			echo $message;
		}
	}

	function set_defaults() {

		$options = get_option( 'climate_content_pool_general_settings' );

		$options = wp_parse_args(
				$options,
				array(
					'token' => '',
					'post_types' => 'post',
				) );

		update_option( 'climate_content_pool_general_settings', $options );

	}

	function add_options_page_callback() {
		?>
		<div class="wrap">
		<h2>Climate Content Pool by Aptivate</h2>

		<div>

		<form method="post" action="options.php">

<?php
		settings_fields( 'climate_content_pool_general_settings' );
		$options = get_option( 'climate_content_pool_general_settings' );

		?>
		<h3>General Settings</h3>

		<table class="form-table">
		<tr valign="top">
		<th scope="row">Authentication token:</th>
		<td>
<?php
		printf(
			'<input type="text" id="climate-content-pool-token" name="climate_content_pool_general_settings[token]" value="%s" size="50" />',
			esc_attr( $options['token'] )
		);
		echo '<br /><span class="description">A valid authentication token that has been generated in the reegle API dashboard. <a href="http://api.reegle.info/register/" target="_blank">http://api.reegle.info/register</a></span>';
		?>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row">Post types:</th>
		<td>
<?php
		printf(
			'<input type="text" id="climate-content-pool-post-types" name="climate_content_pool_general_settings[post_types]" value="%s" />',
			esc_attr( $options['post_types'] )
		);
		echo '<br /><span class="description">Supported post types, separated by commas.</span>';
		?>
		</td>
		</tr>

		</table>


<?php
		submit_button();
		?>

		</form>

			  </div>

											</div>
<?php

	}


	/**
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	function trigger_content_pool_push( $post_id, $post, $update ) {

		$push_post = apply_filters( 'climate-content-pool-post-is-for-pushing', true, $post );

		$push_post = $push_post && isset($_POST['push_to_content_pool']);
		if ( $push_post && $post->post_status == 'publish' &&
				$this->is_post_type_supported( $post->post_type ) ) {
			$error = $this->pusher->push( $post );

			$message = $this->get_message( $error );

			delete_post_meta( $post->ID, 'content_pool_message' );
			add_post_meta( $post->ID, 'content_pool_message',
						   $message );
		}
	}

	private function get_message( $error ) {
		if ( $error !== false ) {
			return "<div class='error'>$error</div>";
		}

		return "<div class='updated'>Post successfully sent to Content Pool</div>";
	}

	public function add_meta_box( $post_type ) {
		global $post;

		$push_post = apply_filters( 'climate-content-pool-post-is-for-pushing', true, $post );

		if ( $push_post && $this->is_post_type_supported( $post_type ) ) {
			add_meta_box(
				'climate_content_pool',
				'Climate Content Pool',
				array( $this, 'render_meta_box_content' ),
				$post_type,
				'advanced',
				'high'
			);
		}
	}

	private function is_post_type_supported( $post_type ) {
		$options = get_option( 'climate_content_pool_general_settings' );
		$post_types = explode( ',', $options['post_types'] );
		$post_types = array_map( 'trim', $post_types );

		return in_array( $post_type, $post_types );
	}

	function render_meta_box_content() {
		echo '<label for="id_push_to_content_pool">Send to Reegle content pool: <input type="checkbox" id="id_push_to_content_pool" name="push_to_content_pool" checked="checked" /></label>';
	}
}

$content_pool = new ClimateContentPool( new ContentPoolPusher() );

add_action( 'save_post', array( $content_pool, 'trigger_content_pool_push' ), 10, 3 );

if ( is_admin() ) {
	add_action( 'admin_menu', array( $content_pool, 'admin_menu' ) );
	add_action( 'admin_init', array( $content_pool, 'admin_init' ) );
	add_action( 'admin_notices', array( $content_pool, 'admin_notices' ) );
}
