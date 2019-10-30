<?php
class Nozama_Lite_SCSS_Colors {

	public $colors = array();

	protected $filename = false;
	protected $data     = false;

	public function __construct( $filename ) {
		if ( is_readable( $filename ) && is_file( $filename ) ) {
			$this->filename = $filename;

			// We have already established that the file is local and is readable.
			// Since file_get_contents() is not allowed, and WP_Filesystem is an overkill for our needs (which
			// does a file_get_contents() itself anyway), we use file() instead. It is no different than require()ing
			// or include()ing the file and saving its contents into an output buffer.
			// Related slack discussion: https://wordpress.slack.com/archives/C02RP4Y3K/p1538832288000100
			$this->data = implode( '', file( $this->filename ) );

			$this->populate_colors();
			$this->expand_3_xdigit_colors();
			$this->resolve_variables();
			$this->remove_unresolved_variables();
		}
	}

	/**
	 * Returns the color value of a variable.
	 *
	 * @param string      $var     The name of the SCSS variable, without the dollar sign.
	 * @param string|bool $default The default value to return, if the variable isn't set.
	 *
	 * @return string
	 */
	public function get( $var, $default = '' ) {
		if ( isset( $this->colors[ $var ] ) ) {
			return $this->colors[ $var ];
		}

		return $default;
	}

	protected function remove_unresolved_variables() {
		// Remove any unresolved variables.
		foreach ( $this->colors as $name => $value ) {
			if ( '$' === $value[0] ) {
				unset( $this->colors[ $name ] );
			}
		}
	}

	protected function resolve_variables() {
		// Resolve variables that point to variables.
		do {
			// Track if anything has changed. If not, stop looping endlessly.
			$changed = false;

			foreach ( $this->colors as $name => $value ) {
				if ( '$' === $value[0] ) {
					$var_name = ltrim( $value, '$' );
					if ( ! empty( $this->colors[ $var_name ] ) ) {
						$this->colors[ $name ] = $this->colors[ $var_name ];

						$changed = true;
					}
				}
			}
		} while ( true === $changed );
	}

	protected function expand_3_xdigit_colors() {
		// Expand 3-digit colors.
		foreach ( $this->colors as $name => $value ) {
			if ( preg_match( '/^#[[:xdigit:]]{3}$/', $value ) ) {
				$value = '#' . $value[1] . $value[1] . $value[2] . $value[2] . $value[3] . $value[3];

				$this->colors[ $name ] = $value;
			}
		}
	}

	protected function populate_colors() {
		$pattern = '/
		^
		\$([\w\-_]+)  # Variables that start with a $, but without capturing it.
		\s*?          # Consume whitespace.
		:             # Expect a colon.
		\s*?          # Consume whitespace.
		(
			\#[[:xdigit:]]*  # Match a hex color 
			|                # or
			(\$[\w\-_]+)     # match a variable (include $ so we know it is a variable.
		)
		/mx';

		$count = preg_match_all( $pattern, $this->data, $matches );

		if ( false === $count || 0 === $count ) {
			return $this->colors;
		}

		// Create the colors array.
		for ( $i = 0; $i < $count; $i++ ) {
			$name  = trim( $matches[1][ $i ] );
			$value = trim( $matches[2][ $i ] );

			$this->colors[ $name ] = $value;
		}

		return $this->colors;

	}
}
