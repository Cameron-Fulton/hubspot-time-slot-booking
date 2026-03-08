<?php

namespace EventLandingPages\Frontend;

defined( 'ABSPATH' ) || exit;

class BrandResolver {

    /**
     * Resolve brand data for an event, falling back to global defaults,
     * then to the WordPress custom logo if nothing else is set.
     *
     * @return array{name: string, logo_url: string, logo_alt: string, website: string, invert: bool}
     */
    public static function resolve( int $post_id ): array {
        $use_global = get_field( 'elp_use_global_brand', $post_id );

        $brand = $use_global ? self::global_brand() : self::event_brand( $post_id );

        // Fall back to WordPress custom logo if no brand logo is configured.
        if ( empty( $brand['logo_url'] ) ) {
            $brand = self::apply_wp_logo_fallback( $brand );
        }

        return $brand;
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

    /**
     * Fill in logo from the WordPress custom logo (set in Appearance → Editor
     * or Customizer) when no brand logo is configured.
     */
    private static function apply_wp_logo_fallback( array $brand ): array {
        $custom_logo_id = (int) get_theme_mod( 'custom_logo' );
        if ( ! $custom_logo_id ) {
            return $brand;
        }

        $image = wp_get_attachment_image_src( $custom_logo_id, 'full' );
        if ( ! $image ) {
            return $brand;
        }

        $brand['logo_url'] = $image[0];
        $brand['logo_alt'] = get_post_meta( $custom_logo_id, '_wp_attachment_image_alt', true )
                             ?: get_bloginfo( 'name' );

        // Use site name and home URL as fallbacks if brand fields are also empty.
        if ( empty( $brand['name'] ) ) {
            $brand['name'] = get_bloginfo( 'name' );
        }
        if ( empty( $brand['website'] ) ) {
            $brand['website'] = home_url();
        }

        return $brand;
    }
}
