<?php

namespace EventLandingPages\PostType;

defined( 'ABSPATH' ) || exit;

class EventPostType {

    public const SLUG = 'elp_event';

    public function __construct() {
        add_action( 'init', [ $this, 'register' ] );
        add_filter( 'post_type_link', [ $this, 'custom_permalink' ], 10, 2 );
        add_action( 'save_post_' . self::SLUG, [ $this, 'sync_slug' ] );
    }

    public function register(): void {
        $labels = [
            'name'               => __( 'Events', 'event-landing-pages' ),
            'singular_name'      => __( 'Event', 'event-landing-pages' ),
            'add_new'            => __( 'Add New Event', 'event-landing-pages' ),
            'add_new_item'       => __( 'Add New Event', 'event-landing-pages' ),
            'edit_item'          => __( 'Edit Event', 'event-landing-pages' ),
            'new_item'           => __( 'New Event', 'event-landing-pages' ),
            'view_item'          => __( 'View Event', 'event-landing-pages' ),
            'search_items'       => __( 'Search Events', 'event-landing-pages' ),
            'not_found'          => __( 'No events found.', 'event-landing-pages' ),
            'not_found_in_trash' => __( 'No events found in Trash.', 'event-landing-pages' ),
            'menu_name'          => __( 'Events', 'event-landing-pages' ),
        ];

        register_post_type( self::SLUG, [
            'labels'        => $labels,
            'public'        => true,
            'show_in_rest'  => true,
            'menu_icon'     => 'dashicons-calendar-alt',
            'supports'      => [ 'title', 'thumbnail' ],
            'has_archive'   => false,
            'rewrite'       => [ 'slug' => 'events', 'with_front' => false ],
            'template_lock' => 'all',
        ] );
    }

    /**
     * Sync the post slug when the custom slug ACF field changes.
     */
    public function sync_slug( int $post_id ): void {
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        $custom_slug = get_field( 'elp_custom_slug', $post_id );
        if ( empty( $custom_slug ) ) {
            return;
        }

        $custom_slug = sanitize_title( $custom_slug );
        $post        = get_post( $post_id );

        if ( $post && $post->post_name !== $custom_slug ) {
            remove_action( 'save_post_' . self::SLUG, [ $this, 'sync_slug' ] );
            wp_update_post( [
                'ID'        => $post_id,
                'post_name' => $custom_slug,
            ] );
            add_action( 'save_post_' . self::SLUG, [ $this, 'sync_slug' ] );
        }
    }

    /**
     * Replace the permalink with the custom slug if set.
     */
    public function custom_permalink( string $url, \WP_Post $post ): string {
        if ( $post->post_type !== self::SLUG ) {
            return $url;
        }

        $custom_slug = get_field( 'elp_custom_slug', $post->ID );
        if ( ! empty( $custom_slug ) ) {
            $custom_slug = sanitize_title( $custom_slug );
            return home_url( '/events/' . $custom_slug . '/' );
        }

        return $url;
    }
}
