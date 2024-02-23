<?php

class WPEA_SWV_MinLengthRule extends WPEA_SWV_Rule {

	const rule_name = 'minlength';

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

		if ( empty( $input ) ) {
			return true;
		}

		$total = 0;

		foreach ( $input as $i ) {
			$total += wpea_count_code_units( $i );
		}

		$threshold = (int) $this->get_property( 'threshold' );

		if ( $threshold <= $total ) {
			return true;
		} else {
			return new WP_Error( 'wpea_invalid_minlength',
				$this->get_property( 'error' )
			);
		}
	}

}
