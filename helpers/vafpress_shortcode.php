<?php

class CRP_Vafpress_Shortcode {

    public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'vafpress_shortcode_init' ), 11 );
    }

    public function vafpress_shortcode_init()
    {
        require_once( CustomRelatedPosts::get()->coreDir . '/helpers/vafpress/vafpress_shortcode_whitelist.php');
        require_once( CustomRelatedPosts::get()->coreDir . '/helpers/vafpress/vafpress_shortcode_options.php');

        new VP_ShortcodeGenerator(array(
            'name'           => 'crp_shortcode_generator',
            'template'       => $shortcode_generator,
            'modal_title'    => 'Custom Related Posts ' . __( 'Shortcodes', 'wp-ultimate-post-grid' ),
            'button_title'   => 'Custom Related Posts',
            'types'          => CustomRelatedPosts::option( 'general_post_types', array( 'post', 'page' ) ),
            'main_image'     => CustomRelatedPosts::get()->coreUrl . '/img/icon_20.png',
            'sprite_image'   => CustomRelatedPosts::get()->coreUrl . '/img/icon_sprite.png',
        ));
    }
}