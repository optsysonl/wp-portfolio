<?php
/**
 * Returns true if the given locale code looks valid.
 *
 * @param string $locale Locale code.
 */
function wpea_is_valid_locale( $locale ) {
    if ( ! is_string( $locale ) ) {
        return false;
    }

    $pattern = '/^[a-z]{2,3}(?:_[a-zA-Z_]{2,})?$/';
    return (bool) preg_match( $pattern, $locale );
}