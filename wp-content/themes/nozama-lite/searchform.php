<form action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get" class="searchform" role="search">
	<div>
		<label for="s" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'nozama-lite' ); ?></label>
		<input type="search" id="s" name="s" value="<?php echo get_search_query(); ?>" placeholder="<?php echo esc_attr_x( 'Search', 'search box placeholder', 'nozama-lite' ); ?>">
		<button class="searchsubmit" type="submit"><i class="fas fa-search"></i><span class="screen-reader-text"> <?php echo esc_html_x( 'Search', 'submit button', 'nozama-lite' ); ?></span></button>
	</div>
</form>
