<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WP_Personal_Data_Controller_Admin {

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
		add_filter( 'user_row_actions', array( $this, 'user_row_actions' ), 10, 2 );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}

	public function user_row_actions( $actions, $user_object ) {
		if ( current_user_can( 'edit_users' ) ) {
			$userdata = get_userdata( $user_object->ID );

			$export_url = add_query_arg(
				array(
					'page' => 'wppdc-export-user-page',
					'email' => urlencode( $userdata->user_email ),
				),
				'options.php'
			);
			$actions['export'] = "<a class='submitexport' href='" . esc_attr ( $export_url ) . "'>" .
				esc_html__( 'Export', 'wppdc' ) . "</a>";
		}

		return $actions;
	}

	public function admin_menu() {
		add_submenu_page(
			'options.php',
			__( 'Export Personal Data', 'wppdc' ),
			__( 'Export Personal Data', 'wppdc' ),
			'manage_options',
			'wppdc-export-user-page',
			array( $this, 'export_page' )
		);
	}

	public function export_page() {
		global $title;

		?>
			<h2>
				<?php echo esc_html( $title ); ?>
			</h2>
		<?php

		$email = isset( $_REQUEST['email'] ) ? $_REQUEST['email'] : '';
		if ( empty( $email ) ) {
			die( 'Error: Invalid email' );
		}

		$button_label = sprintf(
			__( 'Export Personal Data for %s', 'wppdc' ),
			$email
		);

		$nonce = isset( $_REQUEST['wppdc_nonce'] ) ? $_REQUEST['wppdc_nonce'] : '';
		if ( ! empty( $nonce ) ) {
			if ( ! wp_verify_nonce( $nonce, 'wppdc_nonce' ) ) {
				die( 'Error: Invalid nonce' );
			}

			$user = get_user_by( 'email', $email );
			$user_id = $user ? intval( $user->ID ) : false;

			if ( $user_id ) {
				$context = sprintf(
					__( 'The following personal data was found for %s (user ID: %d):', 'wppdc' ),
					$email, $user_id
				);
			} else {
				$context = sprintf(
					__( 'The following personal data was found for %s (not a registered user):', 'wppdc' ),
					$email
				);
			}

			?>
				<p>
					<?php echo esc_html( $context ); ?>
				</p>
			<?php

			// TODO - handle converting this PHP array into a better format like XML or something
			$personal_data = WP_Personal_Data_Controller::getInstance()->get_personal_data( $email );

			?>
				<pre><?php echo esc_html( print_r( $personal_data, true ) ); ?></pre>
			<?php

			$button_label = __( 'Refresh', 'wppdc' );
		}

		$nonced_url = add_query_arg(
			'wppdc_nonce',
			wp_create_nonce( 'wppdc_nonce' )
		);

		?>
			<p>
				<a href="<?php echo esc_attr( $nonced_url ); ?>" class="button button-primary">
					<?php echo esc_html( $button_label ); ?>
				</a>
			</p>
		<?php
	}

}

WP_Personal_Data_Controller_Admin::getInstance();
