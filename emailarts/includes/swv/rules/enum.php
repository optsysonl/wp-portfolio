<?php

class WPEA_SWV_EnumRule extends WPEA_SWV_Rule {

	const rule_name = 'enum';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		if ( empty( $context['text'] ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		$field = $this->get_property( 'field' );

		$input = isset( $_POST[$field] ) ? $_POST[$field] : '';

		$input = wpea_array_flatten( $input );
		$input = wpea_exclude_blank( $input );

		$acceptable_values = (array) $this->get_property( 'accept' );
		$acceptable_values = array_map( 'strval', $acceptable_values );
		$acceptable_values = array_filter( $acceptable_values );
		$acceptable_values = array_unique( $acceptable_values );

		foreach ( $input as $i ) {
			if ( ! in_array( $i, $acceptable_values, true ) ) {
				return new WP_Error( 'wpea_invalid_enum',
					$this->get_property( 'error' )
				);
			}
		}

		return true;
	}

}
