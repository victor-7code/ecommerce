<?php
// We need at least this many categories selected for the layout to work correctly.
$required_terms = 2;

/** @var $term_ids array */
// Assume we were passed an array of term IDs.
if ( empty( $term_ids ) || ! is_array( $term_ids ) || count( $term_ids ) < $required_terms ) {
	return;
}

?>
<div class="block-layout">
	<div class="row">
		<div class="col-md-6 col-12">
			<?php nozama_lite_get_template_part( 'template-parts/categories/item', 'md', array(
				'term' => get_term( $term_ids[0] ),
			) ); ?>
		</div>
		<div class="col-md-6 col-12">
			<?php nozama_lite_get_template_part( 'template-parts/categories/item', 'md', array(
				'term' => get_term( $term_ids[1] ),
			) ); ?>
		</div>
	</div>
</div>
