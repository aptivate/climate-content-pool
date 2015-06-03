<?php
function mock_wp_remote_post( $url, $body ) {
	global $_CONTENT_POOL_MOCK_POST;
	global $_CONTENT_POOL_MOCK_URL;
	global $_CONTENT_POOL_MOCK_RESPONSE;

	$_CONTENT_POOL_MOCK_URL = $url;
	$_CONTENT_POOL_MOCK_POST = $body['body'];

	return $_CONTENT_POOL_MOCK_RESPONSE;
}

$mock_function_args = array(
	'wp_remote_post' => '$url, $body',
);

include 'define-mock-functions.php';
