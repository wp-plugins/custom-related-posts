<?php

class CRP_Support_Tab {

    public function __construct()
    {
        add_action( 'admin_footer-settings_page_crp_admin', array( $this, 'add_support_tab' ) );
    }

    public function add_support_tab()
    {
        include(CustomRelatedPosts::get()->coreDir . '/static/support_tab.html');
    }
}