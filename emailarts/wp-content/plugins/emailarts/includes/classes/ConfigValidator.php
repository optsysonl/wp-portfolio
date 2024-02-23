<?php


class ConfigValidator
{

    private $form;
    private $errors = array();

    public function __construct(EmailArts $form){
        $this->form = $form;
    }

    public function form() {
        return $this->form;
    }

    public function validate() {
        $this->errors = array();

        return $this->is_valid();
    }

    public function is_valid() {
        return ! $this->count_errors();
    }

    public function count_errors( $args = '' ) {


        return 0;
    }

    public function save() {
        if ( $this->form->initial() ) {
            return;
        }

        delete_post_meta( $this->form->id(), '_config_errors' );

        if ( $this->errors ) {
            update_post_meta( $this->form->id(), '_config_errors',
                $this->errors );
        }
    }

}