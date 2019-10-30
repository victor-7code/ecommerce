<?php
	$info = nozama_lite_get_layout_info();

	if ( ! $info['has_sidebar'] ) {
		return;
	}
?>
<div class="<?php nozama_lite_the_sidebar_classes(); ?>">
	<div class="sidebar">
		<?php
			dynamic_sidebar( 'shop' );
		?>
	</div>
</div>
