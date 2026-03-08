<?php

namespace EventLandingPages\Frontend;

use EventLandingPages\PostType\EventPostType;

defined( 'ABSPATH' ) || exit;

class TemplateLoader {

    public function __construct() {
        add_filter( 'single_template', [ $this, 'load_template' ] );
        add_filter( 'body_class', [ $this, 'add_body_class' ] );
    }

    /**
     * Override the single template for elp_event posts.
     *
     * Priority: theme directory > plugin directory.
     */
    public function load_template( string $template ): string {
        if ( ! is_singular( EventPostType::SLUG ) ) {
            return $template;
        }

        // Check theme override first.
        $theme_template = locate_template( 'event-landing-pages/single-elp_event.php' );
        if ( $theme_template ) {
            return $theme_template;
        }

        $plugin_template = ELP_PLUGIN_DIR . 'templates/single-elp_event.php';
        if ( file_exists( $plugin_template ) ) {
            return $plugin_template;
        }

        return $template;
    }

    /**
     * Add a body class for styling hooks.
     */
    public function add_body_class( array $classes ): array {
        if ( is_singular( EventPostType::SLUG ) ) {
            $classes[] = 'elp-event-page';
        }
        return $classes;
    }
}
