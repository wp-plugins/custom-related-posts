<?php

class CRP_Css {

    public function __construct()
    {
        add_action( 'wp_head', array( $this, 'custom_css' ), 20 );
    }

    public function custom_css()
    {
        if( CustomRelatedPosts::option( 'custom_code_public_css', '' ) !== '' ) {
            echo '<style type="text/css">';
            echo CustomRelatedPosts::option( 'custom_code_public_css', '' );
            echo '</style>';
        }
    }
}