<?php

class WPEA_SWV_DateRule extends WPEA_SWV_Rule {

	const rule_name = 'date';

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

		foreach ( $input as $i ) {
			if ( ! wpea_is_date( $i ) ) {
				return new WP_Error( 'wpea_invalid_date',
					$this->get_property( 'error' )
				);
			}
		}

		return true;
	}

}
