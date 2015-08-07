<?php

class CRP_Assets {

    private $assets = array();

    public function __construct()
    {
        add_action( 'init', array( $this, 'default_assets' ) );
        add_action( 'init', array( $this, 'default_public_assets' ) );
        add_action( 'admin_init', array( $this, 'default_admin_assets' ) );

        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
    }

    public function default_assets()
    {

    }

    public function default_public_assets()
    {
        if( !is_admin() ) {

        }
    }

    public function default_admin_assets()
    {
        $this->add(
            array(
                'file' => '/css/admin.css',
                'admin' => true,
            ),
	        array(
		        'file' => '/js/admin_meta_box.js',
		        'admin' => true,
		        'deps' => array(
			        'jquery',
		        ),
		        'data' => array(
			        'name' => 'crp_admin',
			        'ajax_url' => CustomRelatedPosts::get()->helper('ajax')->url(),
			        'core_url' => CustomRelatedPosts::get()->coreUrl,
			        'nonce' => wp_create_nonce( 'crp_admin' ),
                    'remove_image_to' => '<img src="' . CustomRelatedPosts::get()->coreUrl . '/img/minus.png" class="crp_remove_relation crp_remove_relation_to" title="' . __( 'Remove link to this post', 'custom-related-posts' ). '" />',
                    'remove_image_both' => '<img src="' . CustomRelatedPosts::get()->coreUrl . '/img/minus.png" class="crp_remove_relation crp_remove_relation_both" title="' . __( 'Remove both link to and from this post', 'custom-related-posts' ). '" />',
                    'remove_image_from' => '<img src="' . CustomRelatedPosts::get()->coreUrl . '/img/minus.png" class="crp_remove_relation crp_remove_relation_from" title="' . __( 'Remove link from this post', 'custom-related-posts' ). '" />',
		        ),
	        )
        );
    }

    public function add()
    {
        $assets = func_get_args();

        foreach( $assets as $asset )
        {
            if( isset( $asset['file'] ) ) {

                if( !isset( $asset['type'] ) ) {
                    $asset['type'] = pathinfo( $asset['file'], PATHINFO_EXTENSION );
                }

                if( !isset( $asset['priority'] ) ) {
                    $asset['priority'] = 10;
                }

                // Set a URL and DIR variable
                if( isset( $asset['direct'] ) && $asset['direct'] ) {
                    $asset['url'] = $asset['file'];
                    $asset['dir'] = $asset['file'];
                } else {
                    $base_url = CustomRelatedPosts::get()->coreUrl;
                    $base_dir = CustomRelatedPosts::get()->coreDir;

                    if( isset( $asset['premium'] ) && $asset['premium'] ) {
                        $base_url = CustomRelatedPostsPremium::get()->premiumUrl;
                        $base_dir = CustomRelatedPostsPremium::get()->premiumDir;
                    }

                    $asset['url'] = $base_url . $asset['file'];
                    $asset['dir'] = $base_dir . $asset['file'];
                }

                $this->assets[] = $asset;
            }
        }
    }

    public function enqueue( $hook = '' )
    {
        $assets = $this->assets;

        // Check which assets to enqueue
        $css_to_enqueue = array();
        $js_to_enqueue = array();

        foreach( $assets as $asset )
        {
            // Check if asset is intended for admin or public side
            if( !is_admin() && ( !isset( $asset['public'] ) || !$asset['public'] ) ) continue;
            if( is_admin() && ( !isset( $asset['admin'] ) || !$asset['admin'] ) ) continue;

            // Check if we're on a certain page
            if( isset( $asset['page'] ) ) {
                $screen = get_current_screen();
                
                switch ( strtolower( $asset['page'] ) ) {

                    case 'grid_posts':
                        if( $hook != 'edit.php' || $screen->post_type != CRP_POST_TYPE ) continue 2; // Switch is consider a loop statement for continue
                        break;

                    case 'grid_form':
                        if( !in_array( $hook, array( 'post.php', 'post-new.php' ) ) || $screen->post_type != CRP_POST_TYPE ) continue 2;
                        break;

                    default:
                        if( $hook != strtolower( $asset['page'] ) ) continue 2;
                        break;
                }
            }

            // Check for shortcode
            if( isset( $asset['shortcode'] ) ) {
                if( !$this->check_for_shortcode( $asset['shortcode'] ) ) continue;
            }

            // Check if setting equals value
            if( isset( $asset['setting'] ) && count( $asset['setting'] ) == 2 ) {
                if( CustomRelatedPosts::option( $asset['setting'][0], $asset['setting'][1] ) != $asset['setting'][1] ) continue;
            }

            // Check if setting does not equal value
            if( isset( $asset['setting_inverse'] ) && count( $asset['setting_inverse'] ) == 2 ) {
                if( CustomRelatedPosts::option( $asset['setting_inverse'][0], $asset['setting_inverse'][1] ) == $asset['setting_inverse'][1] ) continue;
            }

            // If we've made it here, this asset should be included
            switch( strtolower( $asset['type'] ) ) {

                case 'css':
                    $css_to_enqueue[] = $asset;
                    break;
                case 'js':
                    $js_to_enqueue[] = $asset;
                    break;
            }
        }

        // We've got the assets we need, enqueue them
        if( count( $css_to_enqueue ) > 0 )   $this->enqueue_css( $css_to_enqueue );
        if( count( $js_to_enqueue ) > 0 )    $this->enqueue_js( $js_to_enqueue );
    }

    private function enqueue_css( $assets )
    {
        $i = 1;
        foreach( $assets as $asset ) {
            $version = CustomRelatedPosts::option( 'assets_use_cache', '1' ) == '1' ? CRP_VERSION : time();

            wp_enqueue_style( 'crp_style' . $i, $asset['url'], false, $version, 'all' );
            $i++;
        }
    }

    private function enqueue_js( $assets )
    {
        $i = 1;
        foreach( $assets as $asset ) {
            $name = isset( $asset['name'] ) ? $asset['name'] : 'crp_script' . $i;
            $deps = isset( $asset['deps'] ) ? $asset['deps'] : '';

            $version = CustomRelatedPosts::option( 'assets_use_cache', '1' ) == '1' ? CRP_VERSION : time();

            wp_enqueue_script( $name, $asset['url'], $deps, $version, true );

            if( isset( $asset['data'] ) && isset( $asset['data']['name'] ) ) {
                $data_name = $asset['data']['name'];
                unset( $asset['data']['name'] );

                wp_localize_script( $name, $data_name, $asset['data'] );
            }

            $i++;
        }
    }

    /**
     * Check if any of the shortcodes is used in post
     */
    public function check_for_shortcode( $shortcodes ) {
        if( !is_single() ) return apply_filters( 'crp_check_for_shortcode', true, $shortcodes ); // TODO Needs better solution

        global $post;

        if( function_exists( 'has_shortcode' ) ) {

            // Multiple shortcodes passed, if one shortcode is in the post, return true
            if( is_array( $shortcodes ) ) {
                $shortcode_used = false;

                foreach( $shortcodes as $shortcode ) {
                    if( isset( $post->post_content ) && has_shortcode( $post->post_content, $shortcode ) ) {
                        $shortcode_used = true;
                    }
                }

                return apply_filters( 'crp_check_for_shortcode', $shortcode_used, $shortcodes );
            }

            // Only one shortcode passed, true if that one is in the post
            if( isset( $post->post_content ) && has_shortcode( $post->post_content, $shortcodes ) ) {
                return apply_filters( 'crp_check_for_shortcode', true, $shortcodes );
            }

            return apply_filters( 'crp_check_for_shortcode', false, $shortcodes );
        }

        return apply_filters( 'crp_check_for_shortcode', true, $shortcodes ); // In older versions of WP just enqueue everything
    }
}