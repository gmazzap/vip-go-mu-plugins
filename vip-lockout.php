<?php
/**
 * Plugin Name: Lockouts and Warnings for VIP Go
 * Description: Displays a warning or lockout users from wp-admin
 * Author: Automattic
 * Author URI: http://automattic.com/
 */

class VIP_Lockout {

	/**
	 * VIP_Lockout constructor.
	 */
	public function __construct() {
	    add_action( 'admin_notices', [ $this, 'add_admin_notice' ], 1 );
		add_action( 'user_admin_notices', [ $this, 'add_admin_notice' ], 1 );

		add_filter( 'user_has_cap', [ $this, 'filter_user_has_cap' ], PHP_INT_MAX, 4 );
	}

	public function add_admin_notice() {
		if ( defined( 'VIP_LOCKOUT_STATE' ) ) {
			switch ( VIP_LOCKOUT_STATE ) {
				case 'warning':
					$user = wp_get_current_user();

                    $this->render_warning_notice( $user );
                    break;

				case 'locked':
				    $this->render_locked_notice();
					break;
			}
		}
	}

	protected function render_warning_notice( WP_User $user ) {
		if ( ! $user->has_cap( 'manage_options' ) ) {
			return;
		}

		?>
		<div id="lockout-warning" class="notice-warning wrap clearfix" style="align-items: center;background: #ffffff;border-left-width:4px;border-left-style:solid;border-radius: 6px;display: flex;margin-top: 30px;padding: 30px;" >
            <div class="dashicons dashicons-warning" style="display:flex;float:left;margin-right:2rem;font-size:38px;align-items:center;margin-left:-20px;color:#ffb900;"></div>
            <div class="vp-message" style="display: flex;align-items: center;" >
                <h3><?php _e( VIP_LOCKOUT_MESSAGE ); ?></h3>
            </div>
		</div>
		<?php
	}

	protected function render_locked_notice() {
		?>
        <div id="lockout-warning" class="notice-error wrap clearfix" style="align-items: center;background: #ffffff;border-left-width:4px;border-left-style:solid;border-radius: 6px;display: flex;margin-top: 30px;padding: 30px;" >
            <div class="dashicons dashicons-warning" style="display:flex;float:left;margin-right:2rem;font-size:38px;align-items:center;margin-left:-20px;color:#dc3232;"></div>
            <div class="vp-message" style="display: flex;align-items: center;" >
                <h3><?php _e( VIP_LOCKOUT_MESSAGE ); ?></h3>
            </div>
        </div>
		<?php
	}

	public function filter_user_has_cap( array $user_caps, array $caps, array $args, WP_User $user ) {
        if ( defined( 'VIP_LOCKOUT_STATE' ) && 'locked' === VIP_LOCKOUT_STATE ) {
            $subscriber = get_role( 'subscriber' );

            return array_intersect_key( $user_caps, (array) $subscriber->capabilities );
        }

        return $user_caps;
    }
}

new VIP_Lockout();