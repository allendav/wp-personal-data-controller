<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Personal_Data_Controller {

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
	}

	// TODO - consider allowing other search params instead of email (e.g phone number)
	// TODO - break this up across smaller jobs to avoid timeouts
	public function get_personal_data( $email ) {

		$findings = array();

		$email = strtolower( trim( $email ) );
		$user = get_user_by( 'email', $email );
		$user_id = $user ? intval( $user->ID ) : false;

		// Posts, Post Meta
		$findings_posts = $this->get_personal_data_from_posts( $email, $user_id );
		if ( ! empty( $findings_posts ) ) {
			$findings[ 'posts' ] = $findings_posts;
		}

		// Comments, Comment Meta
		$findings_comments = $this->get_personal_data_from_comments( $email, $user_id );
		if ( ! empty( $findings_comments ) ) {
			$findings[ 'comments' ] = $findings_comments;
		}

		// Links
		$findings_links = $this->get_personal_data_from_links( $email, $user_id );
		if ( ! empty( $findings_links ) ) {
			$findings[ 'comments' ] = $findings_links;
		}

		// Users, User Meta
		$findings_user = $this->get_personal_data_from_user( $email, $user_id );
		if ( ! empty( $findings_user ) ) {
			$findings[ 'user' ] = $findings_user;
		}

		// Options
		$findings_options = $this->get_personal_data_from_options( $email, $user_id );
		if ( ! empty( $findings_options ) ) {
			$findings[ 'options' ] = $findings_options;
		}

		// Other
		$findings_other = $this->get_personal_data_from_other( $email, $user_id );
		if ( ! empty( $findings_other ) ) {
			$findings[ 'other' ] = $findings_other;
		}

		return $findings;
	}

	public function get_personal_data_from_posts( $email, $user_id ) {
		$findings = array();

		$posts_array = get_posts(
			array(
				'posts_per_page' => -1,
				'order_by' => 'ID',
				'order' => 'ASC'
			)
		);

		foreach ( (array) $posts_array as $post ) {
			if ( intval( $post->post_author ) === $user_id ) {
				$findings_post = apply_filters( 'privacy_get_post_author_personal_data', array(), $post, $email );
			} else {
				$findings_post = apply_filters( 'privacy_get_post_personal_data', array(), $post, $email );
			}
			if ( ! empty( $findings_post ) ) {
				$findings[ $post->ID ] = $findings_post;
			}
		}

		return $findings;
	}

	public function get_personal_data_from_comments( $email, $user_id ) {
		$findings = array();

		$comments_array = get_comments(
			array(
				// 'include_unapproved' TODO
				'order_by' => 'comment_ID',
				'order' => 'ASC'
			)
		);

		foreach ( (array) $comments_array as $comment ) {
			if ( intval( $comment->user_id ) === $user_id ) {
				$findings_comment = apply_filters( 'privacy_get_comment_author_personal_data', array(), $comment, $email );
			} else {
				$findings_comment = apply_filters( 'privacy_get_comment_personal_data', array(), $comment, $email );
			}
			if ( ! empty( $findings_comment ) ) {
				$findings[ $comment->comment_ID ] = $findings_comment;
			}
		}

		return $findings;
	}

	public function get_personal_data_from_links( $email, $user_id ) {
		// TODO
		return array();
	}

	public function get_personal_data_from_user( $email, $user_id ) {
		$userdata = get_userdata( $user_id );
		$usermeta = get_user_meta( $user_id );

		return apply_filters( 'privacy_get_user_personal_data', array(), $userdata, $usermeta, $email );
	}

	public function get_personal_data_from_options( $email, $user_id ) {
		// TODO
		return array();
	}

	public function get_personal_data_from_other( $email, $user_id ) {
		// TODO
		return array();
	}
}

WP_Personal_Data_Controller::getInstance();
