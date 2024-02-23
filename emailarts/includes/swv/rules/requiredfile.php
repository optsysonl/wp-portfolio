<?php

class WPEA_SWV_RequiredFileRule extends WPEA_SWV_Rule {

	const rule_name = 'requiredfile';

	public function matches( $context ) {
		if ( false === parent::matches( $context ) ) {
			return false;
		}

		if ( empty( $context['file'] ) ) {
			return false;
		}

		return true;
	}

	public function validate( $context ) {
		$field = $this->get_property( 'field' );

		$input = isset( $_FILES[$field]['tmp_name'] )
			? $_FILES[$field]['tmp_name'] : '';

		$input = wpea_array_flatten( $input );
		$input = wpea_exclude_blank( $input );

		if ( empty( $input ) ) {
			return new WP_Error( 'wpea_invalid_requiredfile',
				$this->get_property( 'error' )
			);
		}

		return true;
	}

}
