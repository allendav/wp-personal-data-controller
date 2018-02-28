<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// This class would ideally be delivered to WordPress core as part of WPPDC

class WP_Personal_Data_Controller_WordPress_Core {

	private static $instance;

	public static function getInstance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

	private function __clone() {
	}

	private function __wakeup() {
	}

	protected function __construct() {
		add_filter( 'privacy_get_post_author_personal_data', array( $this, 'privacy_get_post_author_personal_data' ), 10, 3 );
		add_filter( 'privacy_get_comment_author_personal_data', array( $this, 'privacy_get_comment_author_personal_data' ), 10, 3 );
		add_filter( 'privacy_get_comment_personal_data', array( $this, 'privacy_get_comment_personal_data' ), 10, 3 );
		add_filter( 'privacy_get_user_personal_data', array( $this, 'privacy_get_user_personal_data' ), 10, 4 );

		// TODO get_personal_data_from_links
		// TODO get_personal_data_from_options
	}

	// Surface personal data contained in a post the data subject made
	public function privacy_get_post_author_personal_data( $findings, $post, $email ) {
		$findings[] = array(
			'table' => 'posts',
			'key'   => 'post_author',
			'value' => $post->post_author,
		);

		return $findings;
	}

	// Surface personal data contained in a comment the logged-in data subject made
	public function privacy_get_comment_author_personal_data( $findings, $comment, $email ) {
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author',
			'value' => $comment->comment_author,
		);
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author_email',
			'value' => $comment->comment_author_email,
		);
		if ( ! empty( $comment->comment_author_url ) ) {
			$findings[] = array(
				'table' => 'comments',
				'key'   => 'comment_author_url',
				'value' => $comment->comment_author_url,
			);
		}
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author_IP',
			'value' => $comment->comment_author_IP,
		);
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_agent',
			'value' => $comment->comment_agent,
		);
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'user_id',
			'value' => $comment->user_id,
		);

		return $findings;
	}

	// Surface personal data contained in a no-priv comment if the data subject's email is present
	public function privacy_get_comment_personal_data( $findings, $comment, $email ) {
		$comment_author_email = strtolower( trim( $comment->comment_author_email ) );
		if ( $email !== $comment_author_email ) {
			return $findings;
		}

		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author',
			'value' => $comment->comment_author,
		);
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author_email',
			'value' => $comment->comment_author_email,
		);
		if ( ! empty( $comment->comment_author_url ) ) {
			$findings[] = array(
				'table' => 'comments',
				'key'   => 'comment_author_url',
				'value' => $comment->comment_author_url,
			);
		}
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_author_IP',
			'value' => $comment->comment_author_IP,
		);
		$findings[] = array(
			'table' => 'comments',
			'key'   => 'comment_agent',
			'value' => $comment->comment_agent,
		);

		return $findings;
	}

	public function privacy_get_user_personal_data( $findings, $userdata, $usermeta, $email ) {
		$findings[] = array(
			'table' => 'users',
			'key'   => 'ID',
			'value' => $userdata->ID,
		);
		$findings[] = array(
			'table' => 'users',
			'key'   => 'user_login',
			'value' => $userdata->user_login,
		);
		$findings[] = array(
			'table' => 'users',
			'key'   => 'display_name',
			'value' => $userdata->display_name,
		);
		$findings[] = array(
			'table' => 'users',
			'key'   => 'user_email',
			'value' => $userdata->user_email,
		);
		if ( ! empty( $userdata->user_url ) ) {
			$findings[] = array(
				'table' => 'users',
				'key'   => 'user_url',
				'value' => $userdata->user_url,
			);
		}
		if ( ! empty( $usermeta['first_name'][0] ) ) {
			$findings[] = array(
				'table' => 'usermeta',
				'key'   => 'first_name',
				'value' => $usermeta['first_name'][0],
			);
		}
		if ( ! empty( $usermeta['last_name'][0] ) ) {
			$findings[] = array(
				'table' => 'usermeta',
				'key'   => 'last_name',
				'value' => $usermeta['last_name'][0],
			);
		}
		$findings[] = array(
			'table' => 'usermeta',
			'key'   => 'nickname',
			'value' => $usermeta['nickname'][0],
		);
		if ( ! empty( $usermeta['description'][0] ) ) {
			$findings[] = array(
				'table' => 'usermeta',
				'key'   => 'description',
				'value' => $usermeta['description'][0],
			);
		}

		return $findings;
	}
}

WP_Personal_Data_Controller_WordPress_Core::getInstance();
