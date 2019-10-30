<?php
/**
 * Customize Text Control class.
 *
 * @see WP_Customize_Control
 */
class Nozama_Lite_Customize_Static_Text_Control extends WP_Customize_Control {
	/**
	 * Control type.
	 *
	 * @access public
	 * @var string
	 */
	public $type = 'static-text';

	public function __construct( $manager, $id, $args = array() ) {
		parent::__construct( $manager, $id, $args );
	}

	protected function render_content() {
		if ( ! empty( $this->label ) ) :
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		endif;

		if ( ! empty( $this->description ) ) :
			?><div class="description customize-control-description"><?php

			if ( is_array( $this->description ) ) {
				echo wp_kses( '<p>' . implode( '</p><p>', $this->description ) . '</p>', nozama_lite_get_allowed_tags( 'guide' ) );
			} else {
				echo wp_kses( $this->description, nozama_lite_get_allowed_tags() );
			}

			?></div><?php
		endif;

	}

}
