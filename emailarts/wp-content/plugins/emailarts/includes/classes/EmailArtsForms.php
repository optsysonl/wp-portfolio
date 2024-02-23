<?php


class EmailArtsForms
{
    const post_type = 'wpea_form';


    public function __construct(){
        $this->register_post_type();
    }

    public function register_post_type(){
        register_post_type( self::post_type, array(
            'labels' => array(
                'name' => __( 'EmailArts Forms', 'emailarts' ),
                'singular_name' => __( 'EmailArts Form', 'emailarts' ),
            ),
            'rewrite' => false,
            'query_var' => false,
            'public' => false,
            'capability_type' => 'page',
            'capabilities' => array(
            ),
        ) );
    }
}

new EmailArtsForms();