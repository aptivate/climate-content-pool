<?php
function mock_get_the_time( $d = '', $post = null ) {
	global $_CONTENT_POOL_MOCK_TIME;
	global $_CONTENT_POOL_MOCK_POST_ID;

	$_CONTENT_POOL_MOCK_POST_ID = $post;

	return mysql2date( $d, $_CONTENT_POOL_MOCK_TIME );
}

$mock_function_args = array(
	'get_the_time' => '$d, $post',
);

include 'define-mock-functions.php';
