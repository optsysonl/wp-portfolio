<?php

class WPEA_SWV_MaxNumberRule extends WPEA_SWV_Rule {

	const rule_name = 'maxnumber';

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

		$threshold = $this->get_property( 'threshold' );

		if ( ! wpea_is_number( $threshold ) ) {
			return true;
		}

		foreach ( $input as $i ) {
			if ( wpea_is_number( $i ) and (float) $threshold < (float) $i ) {
				return new WP_Error( 'wpea_invalid_maxnumber',
					$this->get_property( 'error' )
				);
			}
		}

		return true;
	}

}
