<?php

namespace EventLandingPages\Frontend;

use EventLandingPages\PostType\EventPostType;

defined( 'ABSPATH' ) || exit;

class AssetEnqueuer {

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue' ] );
    }

    public function enqueue(): void {
        if ( ! is_singular( EventPostType::SLUG ) ) {
            return;
        }

        $post_id = get_the_ID();
        if ( ! $post_id ) {
            return;
        }

        // Google Fonts.
        wp_enqueue_style(
            'elp-google-fonts',
            'https://fonts.googleapis.com/css2?family=Oswald:wght@400;500;600;700&family=Source+Sans+3:wght@300;400;500;600&display=swap',
            [],
            null
        );

        // Main stylesheet.
        wp_enqueue_style(
            'elp-frontend',
            ELP_PLUGIN_URL . 'assets/css/event-frontend.css',
            [ 'elp-google-fonts' ],
            ELP_VERSION
        );

        $booking_method = get_field( 'elp_booking_method', $post_id ) ?: 'timeslots';

        if ( 'timeslots' === $booking_method ) {
            $this->enqueue_timeslots( $post_id );
        } else {
            $this->enqueue_hubspot_form( $post_id );
        }
    }

    private function enqueue_timeslots( int $post_id ): void {
        wp_enqueue_script(
            'elp-timeslots',
            ELP_PLUGIN_URL . 'assets/js/event-timeslots.js',
            [],
            ELP_VERSION,
            true
        );

        $timezone = get_field( 'elp_default_timezone', 'option' ) ?: 'America/Denver';

        wp_localize_script( 'elp-timeslots', 'elpEventConfig', [
            'restUrl'             => esc_url_raw( rest_url( 'elp/v1' ) ),
            'nonce'               => wp_create_nonce( 'wp_rest' ),
            'slug'                => get_field( 'elp_hubspot_slug', $post_id ) ?: '',
            'timezone'            => $timezone,
            'targetDate'          => get_field( 'elp_target_date', $post_id ) ?: '',
            'ctaLabel'            => get_field( 'elp_cta_label', $post_id ) ?: 'Reserve My Spot',
            'confirmationMessage' => get_field( 'elp_confirmation_message', $post_id ) ?: 'Check your email for confirmation details.',
        ] );
    }

    private function enqueue_hubspot_form( int $post_id ): void {
        wp_enqueue_script(
            'elp-hubspot-form',
            ELP_PLUGIN_URL . 'assets/js/event-hubspot-form.js',
            [],
            ELP_VERSION,
            true
        );

        wp_localize_script( 'elp-hubspot-form', 'elpEventConfig', [
            'portalId' => get_field( 'elp_hubspot_portal_id', $post_id ) ?: '',
            'formId'   => get_field( 'elp_hubspot_form_id', $post_id ) ?: '',
        ] );
    }
}
