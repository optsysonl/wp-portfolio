<?php

/**
 * Class representing a list of pipes.
 */
class WPEAPipes {

	private $pipes = array();

	public function __construct( array $texts ) {
		foreach ( $texts as $text ) {
			$this->add_pipe( $text );
		}
	}

	private function add_pipe( $text ) {
		$pipe = new WPEAPipe( $text );
		$this->pipes[] = $pipe;
	}

	public function do_pipe( $input ) {
		$input_canonical = wpea_canonicalize( $input, array(
			'strto' => 'as-is',
		) );

		foreach ( $this->pipes as $pipe ) {
			$before_canonical = wpea_canonicalize( $pipe->before, array(
				'strto' => 'as-is',
			) );

			if ( $input_canonical === $before_canonical ) {
				return $pipe->after;
			}
		}

		return $input;
	}

	public function collect_befores() {
		$befores = array();

		foreach ( $this->pipes as $pipe ) {
			$befores[] = $pipe->before;
		}

		return $befores;
	}

	public function collect_afters() {
		$afters = array();

		foreach ( $this->pipes as $pipe ) {
			$afters[] = $pipe->after;
		}

		return $afters;
	}

	public function zero() {
		return empty( $this->pipes );
	}

	public function random_pipe() {
		if ( $this->zero() ) {
			return null;
		}

		return $this->pipes[array_rand( $this->pipes )];
	}

	public function to_array() {
		return array_map(
			static function ( WPEAPipe $pipe ) {
				return array(
					$pipe->before,
					$pipe->after,
				);
			},
			$this->pipes
		);
	}
}
