<?php
function mock_get_permalink( $post ) {
	global $_CONTENT_POOL_MOCK_PERMALINK;

	return $_CONTENT_POOL_MOCK_PERMALINK;
}

$mock_function_args = array(
	'get_permalink' => '$post',
);

include 'define-mock-functions.php';
