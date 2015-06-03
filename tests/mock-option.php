<?php
global $_CONTENT_POOL_MOCK_OPTIONS;

$_CONTENT_POOL_MOCK_OPTIONS = array();

function mock_get_option( $option, $default = false ) {
	switch ( $option ) {
		case 'reeep_content_pool_general_settings':
			$defaults = array(
				'post_types' => 'post',
				'token' => '',
			);

			global $_CONTENT_POOL_MOCK_OPTIONS;

			$options = array_merge( $defaults, $_CONTENT_POOL_MOCK_OPTIONS );

			return $options;

		case 'timezone_string':
			return '';

		case 'default_ping_status':
		case 'default_comment_status':
			return 'closed';

		default:
			return "Mock option: $option";
	}
}

$mock_function_args = array(
	'get_option' => '$option,$default = false',
);

include 'define-mock-functions.php';
