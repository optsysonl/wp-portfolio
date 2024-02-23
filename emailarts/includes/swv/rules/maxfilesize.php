<?php

class WPEA_SWV_MaxFileSizeRule extends WPEA_SWV_Rule {

	const rule_name = 'maxfilesize';

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
		$input = isset( $_FILES[$field]['size'] ) ? $_FILES[$field]['size'] : '';
		$input = wpea_array_flatten( $input );
		$input = wpea_exclude_blank( $input );

		if ( empty( $input ) ) {
			return true;
		}

		$threshold = $this->get_property( 'threshold' );

		if ( $threshold < array_sum( $input ) ) {
			return new WP_Error( 'wpea_invalid_maxfilesize',
				$this->get_property( 'error' )
			);
		}

		return true;
	}

}
