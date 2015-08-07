<?php

// Include part of site URL hash in HTML settings to update when site URL changes
$sitehash = substr( md5( CustomRelatedPosts::get()->coreUrl ), 0, 8 );

$admin_menu = array(
    'title' => 'Custom Related Posts ' . __('Settings', 'custom-related-posts'),
    'logo'  => CustomRelatedPosts::get()->coreUrl . '/img/logo.png',
    'menus' => array(
//=-=-=-=-=-=-= GENERAL =-=-=-=-=-=-=
	    array(
		    'title' => __('General', 'custom-related-posts'),
		    'name' => 'general',
		    'icon' => 'font-awesome:fa-link',
		    'controls' => array(
			    array(
				    'type' => 'section',
				    'title' => __('Post Types', 'custom-related-posts'),
				    'name' => 'general_section_post_types',
				    'fields' => array(
					    array(
						    'type' => 'multiselect',
						    'name' => 'general_post_types',
						    'label' => __('Post Types', 'custom-related-posts'),
						    'description' => __( 'Which post types do you want to enable the Related Posts for?', 'custom-related-posts' ),
						    'items' => array(
							    'data' => array(
								    array(
									    'source' => 'function',
									    'value' => 'crp_admin_post_types',
								    ),
							    ),
						    ),
						    'default' => array(
							    'post',
							    'page',
						    ),
					    ),
				    ),
			    ),
		    ),
	    ),
//=-=-=-=-=-=-= ADVANCED =-=-=-=-=-=-=
        array(
            'title' => __('Advanced', 'custom-related-posts'),
            'name' => 'advanced',
            'icon' => 'font-awesome:fa-wrench',
            'controls' => array(
                array(
                    'type' => 'section',
                    'title' => __('Assets', 'custom-related-posts'),
                    'name' => 'advanced_section_assets',
                    'fields' => array(
                        array(
                            'type' => 'toggle',
                            'name' => 'assets_use_cache',
                            'label' => __('Cache Assets', 'custom-related-posts'),
                            'description' => __( 'Disable this while developing.', 'custom-related-posts' ),
                            'default' => '1',
                        ),
                    ),
                ),
            ),
        ),
//=-=-=-=-=-=-= CUSTOM CODE =-=-=-=-=-=-=
        array(
            'title' => __('Custom Code', 'custom-related-posts'),
            'name' => 'custom_code',
            'icon' => 'font-awesome:fa-code',
            'controls' => array(
                array(
                    'type' => 'codeeditor',
                    'name' => 'custom_code_public_css',
                    'label' => __('Public CSS', 'custom-related-posts'),
                    'theme' => 'github',
                    'mode' => 'css',
                ),
            ),
        ),
//=-=-=-=-=-=-= FAQ & SUPPORT =-=-=-=-=-=-=
        array(
            'title' => __('FAQ & Support', 'custom-related-posts'),
            'name' => 'faq_support',
            'icon' => 'font-awesome:fa-book',
            'controls' => array(
                array(
                    'type' => 'notebox',
                    'name' => 'faq_support_notebox',
                    'label' => __('Need more help?', 'custom-related-posts'),
                    'description' => '<a href="mailto:support@bootstrapped.ventures" target="_blank">Custom Related Posts ' .__('FAQ & Support', 'custom-related-posts') . '</a>',
                    'status' => 'info',
                ),
            ),
        ),
    ),
);