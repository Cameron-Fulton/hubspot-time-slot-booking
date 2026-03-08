<?php

namespace EventLandingPages\Frontend;

defined( 'ABSPATH' ) || exit;

class BrandResolver {

    /**
     * Resolve brand data for an event, falling back to global defaults.
     *
     * @return array{name: string, logo_url: string, logo_alt: string, website: string, invert: bool}
     */
    public static function resolve( int $post_id ): array {
        $use_global = get_field( 'elp_use_global_brand', $post_id );

        if ( $use_global ) {
            return self::global_brand();
        }

        return self::event_brand( $post_id );
    }

    private static function event_brand( int $post_id ): array {
        $logo = get_field( 'elp_brand_logo', $post_id );

        return [
            'name'     => get_field( 'elp_brand_name', $post_id ) ?: '',
            'logo_url' => is_array( $logo ) ? ( $logo['url'] ?? '' ) : '',
            'logo_alt' => is_array( $logo ) ? ( $logo['alt'] ?? '' ) : '',
            'website'  => get_field( 'elp_brand_website', $post_id ) ?: '',
            'invert'   => (bool) get_field( 'elp_brand_logo_invert', $post_id ),
        ];
    }

    private static function global_brand(): array {
        $logo = get_field( 'elp_default_brand_logo', 'option' );

        return [
            'name'     => get_field( 'elp_default_brand_name', 'option' ) ?: '',
            'logo_url' => is_array( $logo ) ? ( $logo['url'] ?? '' ) : '',
            'logo_alt' => is_array( $logo ) ? ( $logo['alt'] ?? '' ) : '',
            'website'  => get_field( 'elp_default_brand_website', 'option' ) ?: '',
            'invert'   => (bool) get_field( 'elp_default_brand_logo_invert', 'option' ),
        ];
    }
}
