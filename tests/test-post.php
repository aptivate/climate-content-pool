<?php
require_once 'mock-remote-post.php';
require_once 'mock-option.php';

class PostTest extends ContentPoolTestBase {
	private $post;

	public function setUp() {
		parent::setUp();

		$_POST['push_to_content_pool'] = 'true';

		$author = $this->factory->user->create( array( 'role' => 'author', 'user_login' => 'author' ) );

		$this->post = array(
			'post_author' => $author,
			'post_status' => 'publish',
			'post_title' => 'FEATURE: Three Steps to Decarbonising Development for a Zero-Carbon Future',
			'post_content' => "A new World Bank report lays out three steps for a smooth transition to a zero-carbon future. Through data, examples and policy advice, it aims to help countries makes the shift. It tells us that to prevent temperatures from rising more than 2 degrees Celsius, the world will need to transform it's energy uses and electricity from clean energy sources will play an important role.",
			'post_type' => 'post',
		);

	}

	public function test_metadata_for_successful_push() {
		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '555e89c85f0b172afd3cd02116248150',
			'response' => array(
				'code' => 200,
			)
		);

		$post_id = wp_insert_post( $this->post );
		$this->assertTrue( $post_id !== 0 );

		$document_id = get_post_meta( $post_id, 'content_pool_id', true );
		$this->assertThat( $document_id, $this->equalTo( '555e89c85f0b172afd3cd02116248150' ) );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );
		$div = $this->get_html_element_from_output( $message_html, '/div' );

		$this->assertThat(
			(string)$div,
			$this->equalTo( 'Post successfully sent to Content Pool' ) );
	}

	public function test_metadata_for_unsuccessful_push() {
		$error = new WP_Error();
		$error->add( '1', 'An error occurred' );
		$error->add( '2', 'A very serious error' );

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = $error;

		$post_id = wp_insert_post( $this->post );
		$this->assertTrue( $post_id !== 0 );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );

		$div = $this->get_html_element_from_output( $message_html, '/div' );

		$this->assertThat(
			(string)($div),
			$this->equalTo( 'An error occurredA very serious error' ) );
	}

	public function test_no_push_when_status_not_publish()
	{
		$this->post['post_status'] = 'draft';
		$post_id = wp_insert_post( $this->post );

		$this->assertTrue( $post_id !== 0 );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );

		$this->assertThat( $message_html, $this->identicalTo( '' ) );
	}

	public function test_no_push_when_post_type_not_post()
	{
		$this->post['post_type'] = 'resource';
		$post_id = wp_insert_post( $this->post );

		$this->assertTrue( $post_id !== 0 );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );

		$this->assertThat( $message_html, $this->identicalTo( '' ) );
	}

	public function test_no_push_when_post_parameter_not_set()
	{
		unset($_POST['push_to_content_pool']);

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '555e89c85f0b172afd3cd02116248150',
			'response' => array(
				'code' => 200,
			)
		);

		$this->post['post_type'] = 'post';
		$post_id = wp_insert_post( $this->post );

		$this->assertTrue( $post_id !== 0 );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );

		$this->assertThat( $message_html, $this->identicalTo( '' ) );
	}

	public function test_no_push_when_filter_returns_false()
	{
		add_filter(
			'climate-content-pool-post-is-for-pushing',
			'__return_false' );

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '555e89c85f0b172afd3cd02116248150',
			'response' => array(
				'code' => 200,
			)
		);

		$this->post['post_type'] = 'post';
		$post_id = wp_insert_post( $this->post );

		$this->assertTrue( $post_id !== 0 );

		$message_html = get_post_meta( $post_id, 'content_pool_message', true );

		$this->assertThat( $message_html, $this->identicalTo( '' ) );
	}

	public function test_other_post_type_pushed_when_in_options() {
		global $_CONTENT_POOL_MOCK_OPTIONS;
		$_CONTENT_POOL_MOCK_OPTIONS = array(
			// deliberate spaces to test trimming
			'post_types' => '  post  ,   resource  ',
		);

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '555e89c85f0b172afd3cd02116248150',
			'response' => array(
				'code' => 200,
			)
		);

		$this->post['post_type'] = 'resource';
		$post_id = wp_insert_post( $this->post );
		$this->assertTrue( $post_id !== 0 );

		$document_id = get_post_meta( $post_id, 'content_pool_id', true );
		$this->assertThat( $document_id, $this->equalTo( '555e89c85f0b172afd3cd02116248150' ) );
	}

	public function test_parameters_passed_to_api() {
		// Full parameters tested at unit level

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '555e89c85f0b172afd3cd02116248150',
			'response' => array(
				'code' => 200,
			)
		);

		$post_id = wp_insert_post( $this->post );
		$this->assertTrue( $post_id !== 0 );

		global $_CONTENT_POOL_MOCK_POST;

		$this->assertThat(
			$_CONTENT_POOL_MOCK_POST['title'],
			$this->equalTo( $this->post['post_title'] ) );
	}

}
