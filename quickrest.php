<?php
/**
 * Plugin Name: QuickREST
 * Plugin URI:  https://www.deliciousmedia.co.uk/
 * Description: Speed up REST API requests by selective loading of plugins.
 * Version:     2.0.0
 * Author:      Delicious Media Limited
 * Author URI:  https://www.deliciousmedia.co.uk/
 * Text Domain: dm-quickrest
 * License:     GPLv3 or later
 *
 * @package dm-quickrest
 */

/**
 * Filters the active plugins for this request.
 *
 * @param  array $plugins Activated WordPress plugins.
 *
 * @return array
 */
function quickrest_filter_plugins( $plugins ) {

	if ( ! isset( $_SERVER['REQUEST_URI'] ) || false === strpos( stripcslashes( $_SERVER['REQUEST_URI'] ), rest_get_url_prefix() ) ) {
		return $plugins;
	}

	$plugin_whitelist = apply_filters( 'quickrest_plugin_map', [ '_default' => [] ] );

	// Split out the request URI, we're interested in element [2] which will be the namespace.
	$url_parts = explode( '/', $_SERVER['REQUEST_URI'] );

	// If we have something for this namespace, use that, otherwise fallback to a default.
	$plugins_allowed = isset( $plugin_whitelist[ $url_parts[2] ] ) ? $plugin_whitelist[ $url_parts[2] ] : $plugin_whitelist['_default'];

	// Return only plugins which are active and in our whitelist.
	return array_intersect( $plugins, (array) $plugins_allowed );
}
add_filter( 'option_active_plugins', 'quickrest_filter_plugins', 999, 1 );
