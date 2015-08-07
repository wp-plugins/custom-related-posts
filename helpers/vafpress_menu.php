<?php

class CRP_Vafpress_Menu {

    public function __construct()
    {
        add_action( 'after_setup_theme', array( $this, 'vafpress_menu_init' ), 11 );
    }

    public function vafpress_menu_init()
    {
        require_once( CustomRelatedPosts::get()->coreDir . '/helpers/vafpress/vafpress_menu_whitelist.php');
        require_once( CustomRelatedPosts::get()->coreDir . '/helpers/vafpress/vafpress_menu_options.php');

        new VP_Option(array(
            'is_dev_mode'           => false,
            'option_key'            => 'crp_option',
            'page_slug'             => 'crp_admin',
            'template'              => $admin_menu,
//            'menu_page'             => 'custom_related_posts',
            'menu_page'             => 'options-general.php',
            'use_auto_group_naming' => true,
            'use_exim_menu'         => true,
            'minimum_role'          => 'manage_options',
            'layout'                => 'fluid',
            'page_title'            => __( 'Settings', 'custom-related-posts' ),
//            'menu_label'            => __( 'Settings', 'custom-related-posts' ),
            'menu_label'            => 'Custom Related Posts',
        ));
    }
}