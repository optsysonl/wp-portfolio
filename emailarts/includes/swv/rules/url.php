<?php

class WPEA_SWV_URLRule extends WPEA_SWV_Rule {

	const rule_name = 'url';

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
			if ( ! wpea_is_url( $i ) ) {
				return new WP_Error( 'wpea_invalid_url',
					$this->get_property( 'error' )
				);
			}
		}

		return true;
	}

}
