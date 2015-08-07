<?php

class CRP_Plugin_Action_Link {

    public function __construct()
    {
        add_filter( 'plugin_action_links_custom-related-posts/custom-related-posts.php', array( $this, 'action_links' ) );
    }

    public function action_links( $links )
    {
        $links[] = '<a href="'. get_admin_url(null, 'options-general.php?page=crp_admin') .'">'.__( 'Settings', 'wp-ultimate-recipe' ).'</a>';
        $links[] = '<a href="http://bootstrapped.ventures" target="_blank">'.__( 'More information', 'custom-related-posts' ).'</a>';

        return $links;
    }
}