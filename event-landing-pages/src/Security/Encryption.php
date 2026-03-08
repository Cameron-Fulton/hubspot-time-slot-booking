<?php

namespace EventLandingPages\Security;

defined( 'ABSPATH' ) || exit;

class Encryption {

    /**
     * Encrypt a value using sodium_crypto_secretbox.
     */
    public static function encrypt( string $plaintext ): string {
        $key   = self::derive_key();
        $nonce = random_bytes( SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

        $cipher = sodium_crypto_secretbox( $plaintext, $nonce, $key );
        sodium_memzero( $key );

        return base64_encode( $nonce . $cipher );
    }

    /**
     * Decrypt a value encrypted with encrypt().
     */
    public static function decrypt( string $encoded ): string {
        $key     = self::derive_key();
        $decoded = base64_decode( $encoded, true );

        if ( false === $decoded || strlen( $decoded ) < SODIUM_CRYPTO_SECRETBOX_NONCEBYTES ) {
            sodium_memzero( $key );
            return '';
        }

        $nonce  = substr( $decoded, 0, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );
        $cipher = substr( $decoded, SODIUM_CRYPTO_SECRETBOX_NONCEBYTES );

        $plaintext = sodium_crypto_secretbox_open( $cipher, $nonce, $key );
        sodium_memzero( $key );

        if ( false === $plaintext ) {
            return '';
        }

        return $plaintext;
    }

    /**
     * Check whether a usable encryption key is available.
     */
    public static function is_available(): bool {
        return defined( 'AUTH_KEY' ) && AUTH_KEY !== 'put your unique phrase here';
    }

    /**
     * Derive a 32-byte key from WP's AUTH_KEY constant.
     */
    private static function derive_key(): string {
        if ( ! self::is_available() ) {
            throw new \RuntimeException(
                'AUTH_KEY is not configured. Cannot encrypt or decrypt data.'
            );
        }
        return sodium_crypto_generichash( AUTH_KEY, '', SODIUM_CRYPTO_SECRETBOX_KEYBYTES );
    }
}
