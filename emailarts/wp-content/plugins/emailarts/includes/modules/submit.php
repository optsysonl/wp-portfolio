<?php
/**
** A base module for [submit]
**/

/* form_tag handler */

add_action( 'wpea_init', 'wpea_add_form_tag_submit', 10, 0 );

function wpea_add_form_tag_submit() {
	wpea_add_form_tag( 'submit', 'wpea_submit_form_tag_handler' );
}

function wpea_submit_form_tag_handler( $tag ) {
	$class = wpea_form_controls_class( $tag->type, 'has-spinner' );

	$atts = array();

	$atts['class'] = $tag->get_class_option( $class );
	$atts['id'] = $tag->get_id_option();
	$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

	$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

	if ( empty( $value ) ) {
		$value = __( 'Send', 'emailarts' );
	}

	$atts['type'] = 'submit';
	$atts['value'] = $value;

	$atts = wpea_format_atts( $atts );

	$html = sprintf( '<input %1$s />', $atts );

	return $html;
}


/* Tag generator */

//add_action( 'wpea_admin_init', 'wpea_add_tag_generator_submit', 55, 0 );

function wpea_add_tag_generator_submit() {
	$tag_generator = WPEA_TagGenerator::get_instance();
	$tag_generator->add( 'submit', __( 'submit', 'emailarts' ),
		'wpea_tag_generator_submit', array( 'nameless' => 1 ) );
}

function wpea_tag_generator_submit( $contact_form, $args = '' ) {
	$args = wp_parse_args( $args, array() );

	$description = __( "Generate a form-tag for a submit button. For more details, see %s.", 'emailarts' );

	$desc_link = wpea_link( __( '#', 'emailarts' ), __( 'Submit button', 'emailarts' ) );

?>
<div class="control-box">
<fieldset>
<legend><?php echo sprintf( esc_html( $description ), $desc_link ); ?></legend>

<table class="form-table">
<tbody>
	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-values' ); ?>"><?php echo esc_html( __( 'Label', 'emailarts' ) ); ?></label></th>
	<td><input type="text" name="values" class="oneline" id="<?php echo esc_attr( $args['content'] . '-values' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-id' ); ?>"><?php echo esc_html( __( 'Id attribute', 'emailarts' ) ); ?></label></th>
	<td><input type="text" name="id" class="idvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-id' ); ?>" /></td>
	</tr>

	<tr>
	<th scope="row"><label for="<?php echo esc_attr( $args['content'] . '-class' ); ?>"><?php echo esc_html( __( 'Class attribute', 'emailarts' ) ); ?></label></th>
	<td><input type="text" name="class" class="classvalue oneline option" id="<?php echo esc_attr( $args['content'] . '-class' ); ?>" /></td>
	</tr>

</tbody>
</table>
</fieldset>
</div>

<div class="insert-box">
	<input type="text" name="submit" class="tag code" readonly="readonly" onfocus="this.select()" />

	<div class="submitbox">
	<input type="button" class="button button-primary insert-tag" value="<?php echo esc_attr( __( 'Insert Tag', 'emailarts' ) ); ?>" />
	</div>
</div>
<?php
}
