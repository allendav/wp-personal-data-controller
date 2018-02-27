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

	public function get_personal_data( $email ) {
		$personal_data = array(
			'first_name' => 'Allen',
			'last_name' => 'Snook'
		);

		return $personal_data;
	}

}

WP_Personal_Data_Controller::getInstance();
