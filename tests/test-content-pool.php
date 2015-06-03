<?php

require_once 'ContentPoolTestBase.php';

class ContentPoolTest extends ContentPoolTestBase {
	public function test_creation() {
		$pool = new ClimateContentPool( new ContentPoolPusher() );

		$this->assertThat( $pool, $this->isInstanceOf( 'ClimateContentPool' ) );
	}

	public function test_meta_box_created_for_post() {
		global $wp_meta_boxes;

		do_action( 'add_meta_boxes', 'post' );

		$id = $wp_meta_boxes['post']['advanced']['high']['climate_content_pool']['id'];
		$title = $wp_meta_boxes['post']['advanced']['high']['climate_content_pool']['title'];


		$this->assertThat(
			$id, $this->equalTo( 'climate_content_pool' ) );

		$this->assertThat(
			$title, $this->equalTo( 'Climate Content Pool' ) );
	}

	public function test_meta_box_created_for_resource() {
		global $wp_meta_boxes;

		global $_CONTENT_POOL_MOCK_OPTIONS;

		$_CONTENT_POOL_MOCK_OPTIONS = array(
			'post_types' => 'post,resource',
		);

		do_action( 'add_meta_boxes', 'resource' );

		$id = $wp_meta_boxes['resource']['advanced']['high']['climate_content_pool']['id'];
		$title = $wp_meta_boxes['resource']['advanced']['high']['climate_content_pool']['title'];

		$this->assertThat(
			$id, $this->equalTo( 'climate_content_pool' ) );

		$this->assertThat(
			$title, $this->equalTo( 'Climate Content Pool' ) );
	}

	public function test_meta_box_not_created_when_filtered() {
		global $wp_meta_boxes;

		add_filter(
			'climate-content-pool-post-is-for-pushing',
			'__return_false' );

		do_action( 'add_meta_boxes', 'post' );

		$this->assertEmpty( $wp_meta_boxes );
	}

}
