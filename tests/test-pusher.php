<?php
require_once dirname( __FILE__ ) . '/mock-get-the-time.php';
require_once dirname( __FILE__ ) . '/mock-get-permalink.php';
require_once dirname( __FILE__ ) . '/mock-option.php';
require_once dirname( __FILE__ ) . '/mock-remote-post.php';

class PusherTest extends ContentPoolTestBase {
	public function test_title_passed_to_api() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post( array(
			'post_title' => 'FEAaTURE: Thrëë Stëps tô Dëcàrbôñisiñg Dëvëlôpmëñt fôr à Zërô-Càrbôñ Futurë',
		) );

		$pusher->push( $post );

		$this->check_api_request_field(
			'title',
			$post->post_title );
	}

	public function test_content_passed_to_api() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post( array(
			'post_content' => "Æ ñëw World Bañk rëport lays out thrëë stëps for a smooth trañsitioñ to a zëro-carboñ futurë. Through data, ëxamplës añd policy advicë, it aims to hëlp couñtriës makës thë shift. It tëlls us that to prëvëñt tëmpëraturës from risiñg morë thañ 2 dëgrëës Cëlsius, thë world will ñëëd to trañsform it's ëñërgy usës añd ëlëctricity from clëañ ëñërgy sourcës will play añ importañt rolë.",
		) );

		$pusher->push( $post );

		$this->check_api_request_field(
			'text',
			$post->post_content );
	}

	public function test_language_by_default_is_english() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();

		$pusher->push( $post );

		$this->check_api_request_field(
			'locale',
			'en' );
	}

	public function test_language_filter_applied() {
		$pusher = new ContentPoolPusher();

		add_filter(
			'climate-content-pool-language',
			array( $this, 'return_spanish' ), 1, 2 );

		$post = $this->get_new_post();

		$pusher->push( $post );

		$this->check_api_request_field(
			'locale',
			'es' );
	}

	public function return_spanish() {
		return 'es';
	}

	public function test_internal_by_default_is_false() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();
		$pusher->push( $post );

		$this->check_api_request_field(
			'internal',
			false );
	}

	public function test_internal_flag_can_be_filtered() {
		$pusher = new ContentPoolPusher();

		add_filter(
			'climate-content-pool-internal-only',
			'__return_true' );

		$post = $this->get_new_post();
		$pusher->push( $post );

		$this->check_api_request_field(
			'internal',
			true );
	}

	public function test_url_passed_to_api() {
		$pusher = new ContentPoolPusher();

		$url = 'http://cdkn.org/2015/06/opinion-on-the-road-to-paris-spotlight-on-the-legal-issues/';

		global $_CONTENT_POOL_MOCK_PERMALINK;
		$_CONTENT_POOL_MOCK_PERMALINK = $url;

		$post = $this->get_new_post();
		$pusher->push( $post );

		$this->check_api_request_field(
			'documentUrl',
			$url );
	}

	public function test_content_filter_applied() {
		$pusher = new ContentPoolPusher();

		add_filter(
			'climate-content-pool-content',
			'strtoupper', 1, 1 );

		$post = $this->get_new_post( array(
			'post_content' => 'A new World Bank report lays out three steps for a smooth transition to a zero-carbon future.',
		) );

		$pusher->push( $post );

		$this->check_api_request_field(
			'text',
			'A NEW WORLD BANK REPORT LAYS OUT THREE STEPS FOR A SMOOTH TRANSITION TO A ZERO-CARBON FUTURE.' );
	}

	public function test_content_filter_error_handled() {
		$pusher = new ContentPoolPusher();

		add_filter(
			'climate-content-pool-content',
			array( $this, 'return_error' ), 1, 1 );

		$post = $this->get_new_post();

		$error = $pusher->push( $post );

		$messages = $error->get_error_messages();

		$this->assertThat(
			$messages,
			$this->equalTo(
				array(
					'An error occurred',
					'A very serious error',
				)
			)
		);
	}

	public function test_response_error_handled() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();

		$error = new WP_Error();
		$error->add( '1', 'An error occurred' );
		$error->add( '2', 'A very serious error' );

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = $error;

		$this->assertThat(
			$pusher->push( $post ),
			$this->equalTo( $error ) );
	}

	public function test_error_handled_for_non_200_status_code() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '<p>Invalid API Key</p>',
			'response' => array(
				'code' => 403,
			)
		);

		$expected_error = new WP_Error(
			'apierror',
			'Invalid API Key'
		);

		$this->assertThat(
			$pusher->push( $post ),
			$this->equalTo( $expected_error ) );
	}

	public function test_invalid_document_id_handled() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => '',
			'response' => array(
				'code' => 200,
			)
		);

		$expected_error = new WP_Error(
			'noid',
			'Failed to retrieve Content Pool document id'
		);

		$this->assertThat(
			$pusher->push( $post ),
			$this->equalTo( $expected_error ) );
	}

	public function test_successful_push() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();

		global $_CONTENT_POOL_MOCK_RESPONSE;
		$_CONTENT_POOL_MOCK_RESPONSE = array(
			'body' => 'abc123',
			'response' => array(
				'code' => 200,
			)
		);

		$this->assertThat(
			$pusher->push( $post ),
			$this->identicalTo( true ) );
	}

	public function return_error() {
		$error = new WP_Error();
		$error->add( '1', 'An error occurred' );
		$error->add( '2', 'A very serious error' );

		return $error;
	}

	public function test_api_url() {
		$pusher = new ContentPoolPusher();

		$post = $this->get_new_post();
		$pusher->push( $post );

		global $_CONTENT_POOL_MOCK_URL;

		$this->assertThat(
			$_CONTENT_POOL_MOCK_URL,
			$this->equalTo( 'http://api.reegle.info/service/push' ) );
	}

	public function test_api_token_passed_to_api() {
		$pusher = new ContentPoolPusher();

		global $_CONTENT_POOL_MOCK_OPTIONS;

		$token = 'abc123';
		$_CONTENT_POOL_MOCK_OPTIONS = array(
			'token' => $token,
		);

		$post = $this->get_new_post();
		$pusher->push( $post );

		$this->check_api_request_field(
			'token',
			$token );
	}

	public function test_date_passed_to_api() {
		$pusher = new ContentPoolPusher();

		global $_CONTENT_POOL_MOCK_TIME;
		$_CONTENT_POOL_MOCK_TIME = '2010-10-04 12:14:32';

		$post = $this->get_new_post( array(
			'ID' => 123456,
		) );
		$pusher->push( $post );

		$this->check_api_request_field(
			'date',
			'2010-10-04T12:14:32+0000' );

		global $_CONTENT_POOL_MOCK_POST_ID;
		$this->assertThat(
			$_CONTENT_POOL_MOCK_POST_ID,
			$this->equalTo( 123456 ));
	}

	private function check_api_request_field( $name, $expected_value ) {
		global $_CONTENT_POOL_MOCK_POST;

		$actual_value = $_CONTENT_POOL_MOCK_POST[ $name ];

		$this->assertThat( $expected_value, $this->equalTo( $actual_value ) );
	}

	private function get_new_post( $fields = array() ) {
		$post = new StdClass();

		$defaults = array(
			'post_title' => '',
			'post_content' => '',
			'ID' => '',
		);

		$fields = array_merge( $defaults, $fields );

		foreach ( $fields as $name => $value ) {
			$post->$name = $value;
		}

		return $post;
	}
}
