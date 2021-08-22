<?php
/**
 * Custom functions that act independently of the theme templates.
 *
 * Eventually, some of the functionality here could be replaced by core features.
 *
 * @package Rvdx Theme
 */

/**
 * Sidebar position
 */
add_filter( 'theme_mod_sidebar_position', 'rvdx_theme_set_post_meta_value' );

/**
 * Container type
 */
add_filter( 'theme_mod_container_type', 'rvdx_theme_set_post_meta_value' );

/**
 * Header layout type
 */
add_filter( 'theme_mod_header_layout_type', 'rvdx_theme_set_header_layout_value' );

/**
 * Set post specific meta value.
 *
 * @param  string $value Default meta-value.
 * @return string
 */
function rvdx_theme_set_post_meta_value( $value ) {
	$queried_obj = rvdx_theme_get_queried_obj();

	if ( ! $queried_obj ) {
		return $value;
	}

	$meta_key   = 'rvdx_theme_' . str_replace( 'theme_mod_', '', current_filter() );
	$meta_value = get_post_meta( $queried_obj, $meta_key, true );

	if ( ! $meta_value || 'inherit' === $meta_value ) {
		return $value;
	}

	return $meta_value;
}

/**
 * Set header layout meta value.
 *
 * @param  string $value Default meta-value.
 * @return string
 */
function rvdx_theme_set_header_layout_value( $value ) {

	if ( wp_is_mobile() ) {
		return 'mobile';
	}

	return rvdx_theme_set_post_meta_value( $value );
}

/**
 * Get queried object.
 *
 * @return string|boolean
 */
function rvdx_theme_get_queried_obj() {
	$queried_obj = apply_filters( 'rvdx-theme/posts/queried_object_id', false );

	if ( ! $queried_obj && ! rvdx_theme_maybe_need_rewrite_mod() ) {
		return false;
	}

	$queried_obj = is_home() ? get_option( 'page_for_posts' ) : false;
	$queried_obj = ! $queried_obj ? get_the_id() : $queried_obj;

	return $queried_obj;
}

/**
 * Check if we need to try rewrite theme mod or not
 *
 * @return boolean
 */
function rvdx_theme_maybe_need_rewrite_mod() {

	if ( is_front_page() && 'page' !== get_option( 'show_on_front' ) ) {
		return false;
	}

	if ( is_home() && 'page' == get_option( 'show_on_front' ) ) {
		return true;
	}

	if ( ! is_singular() ) {
		return false;
	}

	return true;
}

/**
 * Render existing macros in passed string.
 *
 * @since  1.0.0
 * @param  string $string String to parse.
 * @return string
 */
function rvdx_theme_render_macros( $string ) {

	$macros = apply_filters( 'rvdx-theme/data_macros', array(
		'/%%year%%/' => date( 'Y' ),
		'/%%date%%/' => date( get_option( 'date_format' ) ),
	) );

	return preg_replace( array_keys( $macros ), array_values( $macros ), $string );
}

/**
 * Render font icons in content
 *
 * @param  string $content content to render
 * @return string
 */
function rvdx_theme_render_icons( $content ) {
	$icons     = rvdx_theme_get_render_icons_set();
	$icons_set = implode( '|', array_keys( $icons ) );

	$regex = '/icon:(' . $icons_set . ')?:?([a-zA-Z0-9-_]+)/';

	return preg_replace_callback( $regex, 'rvdx_theme_render_icons_callback', $content );
}

/**
 * Callback for icons render.
 *
 * @param  array $matches Search matches array.
 * @return string
 */
function rvdx_theme_render_icons_callback( $matches ) {

	if ( empty( $matches[1] ) && empty( $matches[2] ) ) {
		return $matches[0];
	}

	if ( empty( $matches[1] ) ) {
		return sprintf( '<i class="fa fa-%s"></i>', $matches[2] );
	}

	$icons = rvdx_theme_get_render_icons_set();

	if ( ! isset( $icons[ $matches[1] ] ) ) {
		return $matches[0];
	}

	return sprintf( $icons[ $matches[1] ], $matches[2] );
}

/**
 * Get list of icons to render.
 *
 * @return array
 */
function rvdx_theme_get_render_icons_set() {
	return apply_filters( 'rvdx-theme/icons/icons-set', array(
		'fa'       => '<i class="fa fa-%s"></i>',
		'material' => '<i class="material-icons">%s</i>',
	) );
}

/**
 * Replace %s with theme URL.
 *
 * @param  string $url Formatted URL to parse.
 * @return string
 */
function rvdx_theme_render_theme_url( $url ) {
	return sprintf( $url, get_template_directory_uri() );
}

/**
 * Get post template part slug.
 *
 * @return string
 */
function rvdx_theme_get_post_template_part_slug() {
	return apply_filters( 'rvdx-theme/posts/template-part-slug', 'template-parts/content' );
}

/**
 * Get post template part slug.
 *
 * @return string
 */
function rvdx_theme_get_post_style() {
	return apply_filters( 'rvdx-theme/posts/post-style', false );
}

/**
 * Return a list of allowed tags and attributes.
 *
 * @param array $additional_allowed_html Additional allowed tags and attributes
 *
 * @return array
 */
function rvdx_theme_kses_post_allowed_html( $additional_allowed_html = array() ) {
	$allowed_html = wp_kses_allowed_html( 'post' );

	if ( ! empty( $additional_allowed_html ) ) {
		foreach ( $additional_allowed_html as $tag => $attr ) {
			if ( array_key_exists( $tag, $allowed_html ) ) {
				$allowed_html[ $tag ] = array_merge( $allowed_html[ $tag ], $attr );
			} else {
				$allowed_html[ $tag ] = $attr;
			}
		}
	}

	return $allowed_html;
}


/**
 * Adds custom classes to the array of admin body classes.
 *
 * @param array $classes Classes for the body element.
 * @return array
 */
function voelas_admin_body_classes( $classes ) {

	if ( df_is_theme_activated() ) {
		$classes .= ' voelas-is-activated';
	} else {
		$classes .= ' voelas-is-not-activated';
	}

	return $classes;
}
add_filter( 'admin_body_class', 'voelas_admin_body_classes' );


// Theme Info
function df_get_theme_info() {
	$theme      = wp_get_theme();
	$theme_name = $theme->get('Name');
	$theme_v    = $theme->get('Version');

	$theme_info = array(
		'name' => $theme_name,
		'slug' => sanitize_file_name( strtolower( $theme_name ) ),
		'v'    => $theme_v,
	);

	return $theme_info;
}

/**
 * Activation helper class
 */
if ( ! function_exists( 'df_is_theme_activated' ) ) {
	function df_is_theme_activated() {
		return apply_filters( 'voelas_is_theme_activated', false );
	}
}
