<?php
/**
 * Theme hooks.
 *
 * @package Rvdx Theme
 */

// Adds the meta viewport to the header.
add_action( 'wp_head', 'rvdx_theme_meta_viewport', 0 );

// Additional body classes.
add_filter( 'body_class', 'rvdx_theme_body_classes' );

// Enqueue sticky menu if required.
add_filter( 'rvdx-theme/assets-depends/script', 'rvdx_theme_enqueue_misc' );

// Additional image sizes for media gallery.
add_filter( 'image_size_names_choose', 'rvdx_theme_image_size_names_choose' );

// Modify a comment form.
add_filter( 'comment_form_defaults', 'rvdx_theme_modify_comment_form' );

// Modify a nav menu.
add_filter( 'widget_nav_menu_args', 'rvdx_theme_modify_nav_menu', 10, 4 );

// Render macros in widget text.
add_filter( 'widget_text', 'rvdx_theme_widget_text_parse_content', 10, 1 );

// Add a pingback url auto-discovery header for single posts, pages, or attachments.
add_action( 'wp_head', 'rvdx_theme_pingback_header' );

// form button
add_action( 'wpcf7_init', 'rvdx_theme_wpcf7_add_form_tag_submit', 11, 0 );

// Login Form
add_filter( 'rxta/login_form/html', 'rvdx_theme_change_form_html', 10, 2);

// change comment form fields order
add_filter('comment_form_fields', 'rvdx_theme_reorder_comment_fields' );

add_filter('rxta-dynamic-image-html', 'rvdx_change_dynamic_image_html', 11, 1 );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
  *
 * @since  1.0.0
 * @return string `<link>` tag for pingback.
 */
function rvdx_theme_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}

/**
 * Adds the meta viewport to the header.
 *
 * @since  1.0.0
 * @return string `<meta>` tag for viewport.
 */
function rvdx_theme_meta_viewport() {
	echo '<meta name="viewport" content="width=device-width, initial-scale=1" />' . "\n";
}

/**
 * Add extra body classes
 *
 * @param  array $classes Existing classes.
 * @return array
 */
function rvdx_theme_body_classes( $classes ) {
	global $post;

	// Adds a class of group-blog to blogs with more than 1 published author.
	if ( is_multi_author() ) {
		$classes[] = 'group-blog';
	}

	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	if ( ! rvdx_theme_is_top_panel_visible() ) {
		$classes[] = 'top-panel-invisible';
	}

	// Adds a options-based classes.
	$options_based_classes = array();

	$layout      = rvdx_theme()->customizer->get_value( 'container_type' );
	$blog_layout = rvdx_theme()->customizer->get_value( 'blog_layout_type' );
	$blog_style = rvdx_theme()->customizer->get_value( 'blog_style' );
	$blog_image_size  = rvdx_theme()->customizer->get_value( 'blog_post_image_size' );
	$page_preloader = ( rvdx_theme()->customizer->get_value( 'page_preloader' ) ) ? 'website-loading' : '' ;
	$sb_position = rvdx_theme()->sidebar_position;
	$sidebar     = rvdx_theme()->customizer->get_value( 'sidebar_width' );
	$post_name   = ! empty( $post->post_name ) ? $post->post_name : '';

	array_push( $options_based_classes, 'layout-' . $layout, 'blog-' . $blog_layout, $post_name, 'blog-image-size-' . $blog_image_size, $page_preloader );
	if( 'none' !== $sb_position ) {
		array_push( $options_based_classes, 'sidebar_enabled', 'position-' . $sb_position, 'sidebar-' . str_replace( '/', '-', $sidebar ) );
	}

	return array_merge( $classes, $options_based_classes );
}

/**
 * Add misc to theme script dependencies if required.
 *
 * @param  array $depends Default dependencies.
 * @return array
 */
function rvdx_theme_enqueue_misc( $depends ) {
	$totop_visibility = rvdx_theme()->customizer->get_value( 'totop_visibility' );

	if ( $totop_visibility ) {
		$depends[] = 'jquery-totop';
	}

	return $depends;
}

/**
 * Add image sizes for media gallery
 *
 * @param  array $classes Existing classes.
 * @return array
 */
function rvdx_theme_image_size_names_choose( $image_sizes ) {
	$image_sizes['post-thumbnail'] = esc_html__( 'Post Thumbnail', 'voelas' );

	return $image_sizes;
}

/**
 * Add placeholder attributes for comment form fields.
 *
 * @param  array $args Argumnts for comment form.
 * @return array
 */
function rvdx_theme_modify_comment_form( $args ) {
	$args = wp_parse_args( $args );

	if ( ! isset( $args['format'] ) ) {
		$args['format'] = current_theme_supports( 'html5', 'comment-form' ) ? 'html5' : 'xhtml';
	}

	$req       = get_option( 'require_name_email' );
	$aria_req  = ( $req ? " aria-required='true'" : '' );
	$html_req  = ( $req ? " required='required'" : '' );
	$html5     = 'html5' === $args['format'];
	$commenter = wp_get_current_commenter();

	$args['label_submit'] = esc_html__( 'Submit Comment', 'voelas' );

	$args['fields']['author'] = '<p class="comment-form-author"><input id="author" class="comment-form__field" name="author" type="text" placeholder="' . esc_attr__( 'Name', 'voelas' ) . ( $req ? ' *' : '' ) . '" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30"' . $aria_req . $html_req . ' /></p>';

	$args['fields']['email'] = '<p class="comment-form-email"><input id="email" class="comment-form__field" name="email" ' . ( $html5 ? 'type="email"' : 'type="text"' ) . ' placeholder="' . esc_attr__( 'E-mail', 'voelas' ) . ( $req ? ' *' : '' ) . '" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" aria-describedby="email-notes"' . $aria_req . $html_req  . ' /></p>';

	$args['fields']['url'] = '<p class="comment-form-url"><input id="url" class="comment-form__field" name="url" ' . ( $html5 ? 'type="url"' : 'type="text"' ) . ' placeholder="' . esc_attr__( 'Website', 'voelas' ) . '" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" /></p>';

	$args['comment_field'] = '<p class="comment-form-comment"><textarea id="comment" class="comment-form__field" name="comment" placeholder="' . esc_attr__( 'Comments *', 'voelas' ) . '" cols="45" rows="7" aria-required="true" required="required"></textarea></p>';

	return $args;
}

/**
 * Adds an extra div around the menu.
 *
 * @param  array $args Argumnts for nav menu.
 * @return array
 */
function rvdx_theme_modify_nav_menu( $args ) {
	$args['items_wrap'] = '<div class="main-navigation"><div class="main-navigation-inner"><ul id="%1$s" class="%2$s">%3$s</ul></div></div>';

	return $args;
}

/**
 * Render macros in widget text
 *
 * @since  1.0.0
 * @return string widget content.
 */
function rvdx_theme_widget_text_parse_content( $content ) {
	$content = rvdx_theme_render_macros( $content );

	return $content;
}

/* Button contact form 7 */
function rvdx_theme_wpcf7_add_form_tag_submit() {
	wpcf7_remove_form_tag( 'submit' );
	wpcf7_add_form_tag( 'submit', 'rvdx_theme_wpcf7_submit_form_tag_handler' );
}

function rvdx_theme_wpcf7_submit_form_tag_handler( $tag ) {
	$class = wpcf7_form_controls_class( $tag->type );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = __( 'Send', 'voelas' );
	}

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	$atts = wpcf7_format_atts( $atts );

	$html = sprintf( '<button %1$s><span>%2$s</span></button>', $atts, $value);

	return $html;
}

/* Login Form */
function rvdx_theme_change_form_html( $html, $settings ){

	$html = str_replace( 'name="log"', 'name="log" placeholder="' . $settings['label_password'] . '"', $html );
	$html = str_replace( 'name="pwd"', 'name="pwd" placeholder="' . $settings['label_username'] . '"', $html );

	return $html;
}

/* Change comment form fields order */
function rvdx_theme_reorder_comment_fields( $fields ){
	$new_fields = array();
	$myorder = array('author','email','url','comment');

	foreach( $myorder as $key ){
		$new_fields[ $key ] = $fields[ $key ];
		unset( $fields[ $key ] );
	}

	if( $fields )
		foreach( $fields as $key => $val )
			$new_fields[ $key ] = $val;

	return $new_fields;
}

/* Srcset */
function rvdx_change_dynamic_image_html( $html ){
    return '<div class="elementor-background-overlay"></div><img src="%3$s" %2$s alt="%4$s" %5$s>';
}

/* Disable ACF plugin updates */
add_action( 'acf/init', 'voelas_acf_updates' );
function voelas_acf_updates() {
	acf_update_setting( 'show_updates', false );
}

function get_speaker_events() {
	global $post;
	$speaker_slug = $post->ID;
	$query_args = array(
		'posts_per_page' => '5',
		'ignore_sticky_posts' => true,
		'post_type' => 'schedule_posts',
		'post_status' => 'publish',
		'order' => 'ASC',
		'orderby' => 'date',
		'meta_query' => [
			'relation' => 'OR',
			[
				'key' => 'speakers-1',
				'value' => $speaker_slug
			],
			[
				'key' => 'speakers-2',
				'value' => $speaker_slug
			]
		]
	);

	return $query_args;
}
