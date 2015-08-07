<?php
function crp_admin_premium_not_installed()
{
    return !CustomRelatedPosts::is_premium_active();
}

function crp_admin_premium_installed()
{
    return CustomRelatedPosts::is_premium_active();
}

function crp_admin_post_types()
{
    $post_types = get_post_types( '', 'names' );
    $types = array();

    foreach( $post_types as $post_type ) {
        $types[] = array(
            'value' => $post_type,
            'label' => ucfirst( $post_type )
        );
    }

    return $types;
}

VP_Security::instance()->whitelist_function( 'crp_admin_premium_not_installed' );
VP_Security::instance()->whitelist_function( 'crp_admin_premium_installed' );
VP_Security::instance()->whitelist_function( 'crp_admin_post_types' );