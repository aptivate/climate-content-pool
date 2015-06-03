<?php
class ContentPoolPusher {
	public function push( $post ) {
		$title = $post->post_title;
		$content = $post->post_content;

		$content = apply_filters(
			'climate-content-pool-content',
			$content,
			$post );

		if ( is_wp_error( $content ) ) {
			return implode( '<br />', $content->get_error_messages() );
		}

		/* http://api.reegle.info/documentation - Content Pool Push tab*/
		$url = 'http://api.reegle.info/service/push';

		$options = get_option( 'climate_content_pool_general_settings' );

		$language = apply_filters( 'climate-content-pool-language', 'en', $post );

		$internal = apply_filters(
			'climate-content-pool-internal-only', false, $post );

		$fields = array(
			'title' => $title,
			'text' => $content,
			'locale' => $language,
			'documentUrl' => get_permalink( $post ),
			'token' => $options['token'],
			'date' => get_the_time( 'Y-m-d\TH:i:sO', $post->ID ),
			'internal' => $internal,
		);

		$response = wp_remote_post( $url, array( 'body' => $fields ) );

		if ( is_wp_error( $response ) ) {
			return implode( '<br />', $response->get_error_messages() );
		}

		if ( $response['response']['code'] != 200 ) {
			return strip_tags( wp_remote_retrieve_body( $response ) );
		}

		$document_id = wp_remote_retrieve_body( $response );
		if ( $document_id ) {
			delete_post_meta( $post->ID, 'content_pool_id' );
			add_post_meta( $post->ID, 'content_pool_id', $document_id );

			return false;
		}

		return 'Failed to retrieve Content Pool document id';
	}
}
