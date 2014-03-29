<?php
/**
 * Plugin Name: TED in bed
 * Plugin URI: http://gawainlynch.github.io/ted-in-bed
 * Description: Enable TED video URLs to be embedded in WordPress via the internal oEmbed API
 * Version: 1.1
 * License: GPLv2 or later
 * Author: Gawain Lynch
 * Author URI: http://gawainlynch.com/
 */

/*
 * Add TED oEmbed API JSON target
 */
wp_oembed_add_provider( 'http://www.ted.com/talks/*', 'http://www.ted.com/talks/oembed.json' );


/**
 * Set default height to a more sane 16:9
 *
 * @see wp_embed_defaults() in wp-includes/media.php
 * @param $attrs - Array with 'height' and 'width' elements
 * @return Array
 */
function tib_embed_defaults( $attrs )
{
	/*
	 * WordPress for some reason defaults to an aspect ratio of 1:1.5
	 *
	 * The width defaults to the content width as specified by the theme. If the
	 * theme does not specify a content width, then 500px is used.
	 *
	 * The default height is 1.5 times the width, or 1000px, whichever is smaller.
	 *
	 * For a ratio of 4:3 change the divisor to 1.33
	 */
	$ratio = apply_filters( 'tib_aspect_ratio_divisor', 1.77 );
	$attrs['height'] = ceil( $attrs['width'] / $ratio );
	return $attrs;
}

add_filter( 'embed_defaults', 'tib_embed_defaults', 10 );

/**
 * Handle [ted] shortcodes
 *
 * Shorcode parameters:
 *   ~ id     - Numeric ID of TED talk
 *   ~ width  - Width in pixels
 *   ~ height - Height in pixels
 *
 * @param $attrs  - Array
 * @return String - Resulting HTML
 */
function tib_shortcode_handler( $attrs )
{
    global $wp_embed;

	// Height and width defaults
	extract( shortcode_atts( wp_embed_defaults(), $attrs ) );

    if ( empty( $attrs['id'] ) || ! is_numeric( ( $attrs['id'] ) ) )
        return false;

    $url = 'http://www.ted.com/talks/view/lang/eng/id/' . $attrs['id'];

    return $wp_embed->shortcode( $attrs, $url );
}

add_shortcode( 'ted', 'tib_shortcode_handler' );
