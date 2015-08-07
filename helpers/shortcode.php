<?php

class CRP_Shortcode {

    public function __construct()
    {
        add_shortcode( 'custom-related-posts', array( $this, 'shortcode' ) );
    }

    public function shortcode( $options )
    {
        return CustomRelatedPosts::get()->helper( 'output' )->output_list( get_the_ID(), $options );
    }
}