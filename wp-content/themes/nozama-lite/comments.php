<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
if ( post_password_required() ) {
	return;
}

$show_comments = have_comments() || comments_open();

do_action( 'nozama_lite_before_comments', $show_comments ); ?>

<?php if ( $show_comments ) : ?>
	<div id="comments" class="comments-area">
<?php endif; ?>

	<?php if ( have_comments() ) : ?>
		<h3 class="comments-title">
			<?php comments_number(); ?>
		</h3><!-- .comments-title -->

		<?php the_comments_navigation(); ?>

		<ol class="comment-list">
			<?php
				wp_list_comments( array(
					'style'       => 'ol',
					'avatar_size' => 80,
					'short_ping'  => true,
				) );
			?>
		</ol><!-- .comment-list -->

		<?php the_comments_navigation(); ?>

	<?php endif; // Check for have_comments(). ?>


	<?php // If comments are closed and there are comments, let's leave a little note, shall we? ?>
	<?php if ( ! comments_open() && get_comments_number() && post_type_supports( get_post_type(), 'comments' ) ) : ?>
		<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'nozama-lite' ); ?></p>
	<?php endif; ?>

	<?php comment_form(); ?>

<?php if ( $show_comments ) : ?>
	</div><!-- #comments -->
<?php endif;

do_action( 'nozama_lite_after_comments', $show_comments );
