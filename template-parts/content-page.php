<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Rvdx Theme
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<?php rvdx_theme_post_thumbnail(); ?>

	<div class="page-content">
		<?php
			the_content();
			wp_link_pages( array(
				'before'      => '<div class="page-links">' . esc_html__( 'Pages:', 'voelas' ),
				'after'       => '</div>',
				'link_before' => '<span>',
				'link_after'  => '</span>',
			) );
		?>
	</div><!-- .page-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<div class="page-footer">
			<?php
				edit_post_link(
					sprintf(
						wp_kses(
							/* translators: %s: Name of current post. Only visible to screen readers */
							sprintf( esc_html__( 'Edit %s', 'voelas' ), '<span class="screen-reader-text">%s</span>' ),
							array(
								'span' => array(
									'class' => array(),
								),
							)
						),
						get_the_title()
					),
					'<span class="edit-link">',
					'</span>'
				);
			?>
		</div><!-- .page-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
