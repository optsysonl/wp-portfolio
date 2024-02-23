<?php
/**
 * Schema-Woven Validation API
 */

require_once WPEA_PLUGIN_DIR . '/includes/swv/schema-holder.php';
require_once WPEA_PLUGIN_DIR . '/includes/swv/script-loader.php';


/**
 * Returns an associative array of SWV rules.
 */
function wpea_swv_available_rules() {
	$rules = array(
		'required' => 'WPEA_SWV_RequiredRule',
		'requiredfile' => 'WPEA_SWV_RequiredFileRule',
		'email' => 'WPEA_SWV_EmailRule',
		'url' => 'WPEA_SWV_URLRule',
		'tel' => 'WPEA_SWV_TelRule',
		'number' => 'WPEA_SWV_NumberRule',
		'date' => 'WPEA_SWV_DateRule',
		'time' => 'WPEA_SWV_TimeRule',
		'file' => 'WPEA_SWV_FileRule',
		'enum' => 'WPEA_SWV_EnumRule',
		'dayofweek' => 'WPEA_SWV_DayofweekRule',
		'minitems' => 'WPEA_SWV_MinItemsRule',
		'maxitems' => 'WPEA_SWV_MaxItemsRule',
		'minlength' => 'WPEA_SWV_MinLengthRule',
		'maxlength' => 'WPEA_SWV_MaxLengthRule',
		'minnumber' => 'WPEA_SWV_MinNumberRule',
		'maxnumber' => 'WPEA_SWV_MaxNumberRule',
		'mindate' => 'WPEA_SWV_MinDateRule',
		'maxdate' => 'WPEA_SWV_MaxDateRule',
		'minfilesize' => 'WPEA_SWV_MinFileSizeRule',
		'maxfilesize' => 'WPEA_SWV_MaxFileSizeRule',
	);

	return apply_filters( 'wpea_swv_available_rules', $rules );
}


add_action( 'wpea_init', 'wpea_swv_load_rules', 10, 0 );

/**
 * Loads SWV fules.
 */
function wpea_swv_load_rules() {
	$rules = wpea_swv_available_rules();

	foreach ( array_keys( $rules ) as $rule ) {
		$file = sprintf( '%s.php', $rule );
		$path = path_join( WPEA_PLUGIN_DIR . '/includes/swv/rules', $file );

		if ( file_exists( $path ) ) {
			include_once $path;
		}
	}
}


/**
 * Creates an SWV rule object.
 *
 * @param string $rule_name Rule name.
 * @param string|array $properties Optional. Rule properties.
 * @return WPEA_SWV_Rule|null The rule object, or null if it failed.
 */
function wpea_swv_create_rule( $rule_name, $properties = '' ) {
	$rules = wpea_swv_available_rules();

	if ( isset( $rules[$rule_name] ) ) {
		return new $rules[$rule_name]( $properties );
	}
}


/**
 * Returns an associative array of JSON Schema for EmailArts SWV.
 */
function wpea_swv_get_meta_schema() {
	return array(
		'$schema' => 'https://json-schema.org/draft/2020-12/schema',
		'title' => 'EmailArts SWV',
		'description' => 'EmailArts SWV meta-schema',
		'type' => 'object',
		'properties' => array(
			'version' => array(
				'type' => 'string',
			),
			'locale' => array(
				'type' => 'string',
			),
			'rules' => array(
				'type' => 'array',
				'items' => array(
					'type' => 'object',
					'properties' => array(
						'rule' => array(
							'type' => 'string',
							'enum' => array_keys( wpea_swv_available_rules() ),
						),
						'field' => array(
							'type' => 'string',
							'pattern' => '^[A-Za-z][-A-Za-z0-9_:]*$',
						),
						'error' => array(
							'type' => 'string',
						),
						'accept' => array(
							'type' => 'array',
							'items' => array(
								'type' => 'string',
							),
						),
						'threshold' => array(
							'type' => 'string',
						),
					),
					'required' => array( 'rule' ),
				),
			),
		),
	);
}


/**
 * The base class of SWV rules.
 */
abstract class WPEA_SWV_Rule {

	protected $properties = array();

	public function __construct( $properties = '' ) {
		$this->properties = wp_parse_args( $properties, array() );
	}


	/**
	 * Returns true if this rule matches the given context.
	 *
	 * @param array $context Context.
	 */
	public function matches( $context ) {
		$field = $this->get_property( 'field' );

		if ( ! empty( $context['field'] ) ) {
			if ( $field and ! in_array( $field, (array) $context['field'], true ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Validates with this rule's logic.
	 *
	 * @param array $context Context.
	 */
	public function validate( $context ) {
		return true;
	}


	/**
	 * Converts the properties to an array.
	 *
	 * @return array Array of properties.
	 */
	public function to_array() {
		$properties = (array) $this->properties;

		if ( defined( 'static::rule_name' ) and static::rule_name ) {
			$properties = array( 'rule' => static::rule_name ) + $properties;
		}

		return $properties;
	}


	/**
	 * Returns the property value specified by the given property name.
	 *
	 * @param string $name Property name.
	 * @return mixed Property value.
	 */
	public function get_property( $name ) {
		if ( isset( $this->properties[$name] ) ) {
			return $this->properties[$name];
		}
	}

}


/**
 * The base class of SWV composite rules.
 */
abstract class WPEA_SWV_CompositeRule extends WPEA_SWV_Rule {

	protected $rules = array();


	/**
	 * Adds a sub-rule to this composite rule.
	 *
	 * @param WPEA_SWV_Rule $rule Sub-rule to be added.
	 */
	public function add_rule( $rule ) {
		if ( $rule instanceof WPEA_SWV_Rule ) {
			$this->rules[] = $rule;
		}
	}


	/**
	 * Returns an iterator of sub-rules.
	 */
	public function rules() {
		foreach ( $this->rules as $rule ) {
			yield $rule;
		}
	}


	/**
	 * Returns true if this rule matches the given context.
	 *
	 * @param array $context Context.
	 */
	public function matches( $context ) {
		return true;
	}


	/**
	 * Validates with this rule's logic.
	 *
	 * @param array $context Context.
	 */
	public function validate( $context ) {
		foreach ( $this->rules() as $rule ) {
			if ( $rule->matches( $context ) ) {
				$result = $rule->validate( $context );

				if ( is_wp_error( $result ) ) {
					return $result;
				}
			}
		}

		return true;
	}


	/**
	 * Converts the properties to an array.
	 *
	 * @return array Array of properties.
	 */
	public function to_array() {
		$rules_arrays = array_map(
			static function ( $rule ) {
				return $rule->to_array();
			},
			$this->rules
		);

		return array_merge(
			parent::to_array(),
			array(
				'rules' => $rules_arrays,
			)
		);
	}

}


/**
 * The schema class as a composite rule.
 */
class WPEA_SWV_Schema extends WPEA_SWV_CompositeRule {

	const version = 'EmailArts SWV Schema 2023-07';

	public function __construct( $properties = '' ) {
		$this->properties = wp_parse_args( $properties, array(
			'version' => self::version,
		) );
	}

}
