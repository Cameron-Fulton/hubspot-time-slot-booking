<?php

namespace EventLandingPages\Admin;

defined( 'ABSPATH' ) || exit;

class AdminNotices {

    public function __construct() {
        add_action( 'admin_notices', [ $this, 'check_acf' ] );
        add_action( 'admin_notices', [ $this, 'check_auth_key' ] );
    }

    public function check_acf(): void {
        if ( class_exists( 'ACF' ) ) {
            return;
        }

        printf(
            '<div class="notice notice-error"><p>%s</p></div>',
            esc_html__(
                'Event Landing Pages requires Advanced Custom Fields (ACF) to be installed and active.',
                'event-landing-pages'
            )
        );
    }

    public function check_auth_key(): void {
        if ( ! \EventLandingPages\Security\Encryption::is_available() ) {
            printf(
                '<div class="notice notice-warning"><p>%s</p></div>',
                esc_html__(
                    'Event Landing Pages: AUTH_KEY is not configured in wp-config.php. API key encryption is disabled.',
                    'event-landing-pages'
                )
            );
        }
    }
}
