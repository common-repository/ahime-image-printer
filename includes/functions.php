<?php
if ( ! function_exists( 'array_key_last' ) ) {
	/**
	 * Get the last key of array if function array_key_last not exist.
	 *
	 * @param array $array The provider array.
	 * @return array
	 */
	function array_key_last( $array ) {
		if ( ! is_array( $array ) || empty( $array ) ) {
			return null;
		}

		return array_keys( $array )[ count( $array ) - 1 ];
	}
}
