<?php

class CRP_Ajax {

    public function __construct()
    {
        add_action( 'wp_ajax_crp_search_posts', array( $this, 'ajax_search_posts' ) );
        add_action( 'wp_ajax_crp_link_posts', array( $this, 'ajax_link_posts' ) );
        add_action( 'wp_ajax_crp_remove_relation', array( $this, 'ajax_remove_relation' ) );
    }

    public function url()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
        $ajaxurl = admin_url( 'admin-ajax.php', $scheme );
        $ajaxurl .= '?crp_ajax=1';

        // WPML AJAX Localization Fix
        global $sitepress;
        if( isset( $sitepress) ) {
            $ajaxurl .= '&lang='.$sitepress->get_current_language();
        }

        return $ajaxurl;
    }

    public function ajax_search_posts()
    {
        if( check_ajax_referer( 'crp_admin', 'security', false ) )
        {
            $term = $_POST['term'];
            $base_id = intval( $_POST['base'] );

            $args = array(
                's' => $term,
                'post_type' => CustomRelatedPosts::option( 'general_post_types', array( 'post', 'page' ) ),
                'post_status' => 'any',
                'orderby' => 'title',
                'order' => 'ASC',
            );

            $query = new WP_Query( $args );

            $html = '';
            if( $query->have_posts() ) {
                $relations_to = CustomRelatedPosts::get()->relations_to( $base_id );
                $relations_from = CustomRelatedPosts::get()->relations_from( $base_id );

                $posts = $query->posts;

                foreach( $posts as $post ) {
                    $post_type = get_post_type_object( $post->post_type );

                    $html .= '<tr id="crp_post_' . $post->ID . '">';
                    $html .= '<td>' . $post_type->labels->singular_name . '</td>';
                    $html .= '<td>' . mysql2date( "j M 'y", $post->post_date ) . '</td>';
                    $html .= '<td id="crp_post_' . $post->ID . '_title">' . $post->post_title . '</td>';
                    $html .= '<td id="crp_post_' . $post->ID . '_actions">';

                    if( $post->ID == $base_id ) {
                        $html .= '<div class="button" disabled>' . __( 'Current post', 'custom-related-posts' ) . '</div>';
                    } elseif( array_key_exists( $post->ID, $relations_to ) && array_key_exists( $post->ID, $relations_from ) ) {
                        $html .= '<div class="button" disabled>' . __( 'Already linked', 'custom-related-posts' ) . '</div>';
                    } else {
                        $disabled = array_key_exists( $post->ID, $relations_to ) || array_key_exists( $post->ID, $relations_from ) ? ' disabled' : '';
                        $html .= '<button class="button" onclick="CustomRelatedPosts.linkTo(' . $post->ID . ')"' . $disabled . '>' . __( 'To', 'custom-related-posts' ) . '</button>';
                        $html .= '<button class="button button-primary" onclick="CustomRelatedPosts.linkBoth(' . $post->ID . ')">' . __( 'Both', 'custom-related-posts' ) . '</button>';
                        $html .= '<button class="button" onclick="CustomRelatedPosts.linkFrom(' . $post->ID . ')"' . $disabled . '>' . __( 'From', 'custom-related-posts' ) . '</button>';
                    }

                    $html .= '</td>';
                    $html .= '</tr>';
                }
            }

            echo $html;
        }

        die();
    }

    public function ajax_link_posts()
    {
        if( check_ajax_referer( 'crp_admin', 'security', false ) )
        {
            $base_id = intval( $_POST['base'] );
            $target_id = intval( $_POST['target'] );

            $base = get_post( $base_id );
            $target = get_post( $target_id );

            $post_types = CustomRelatedPosts::option( 'general_post_types', array( 'post', 'page' ) );

            if( $base_id !== $target_id && is_object( $base ) && is_object( $target ) && in_array( $base->post_type, $post_types ) && in_array( $target->post_type, $post_types ) ) {
                $from = $_POST['from'] == 'true' ? true : false;
                $to = $_POST['to'] == 'true' ? true : false;

                $base_data = CustomRelatedPosts::get()->relation_data( $base );
                $target_data = CustomRelatedPosts::get()->relation_data( $target );

                // Current Relations
                $base_relations_from = CustomRelatedPosts::get()->relations_from( $base_id );
                $base_relations_to = CustomRelatedPosts::get()->relations_to( $base_id );
                $target_relations_from = CustomRelatedPosts::get()->relations_from( $target_id );
                $target_relations_to = CustomRelatedPosts::get()->relations_to( $target_id );

                if( $from ) {
                    $base_relations_from[$target_id] = $target_data;
                    $target_relations_to[$base_id] = $base_data;

                    CustomRelatedPosts::get()->helper( 'relations' )->update_to( $target_id, $target_relations_to );
                    CustomRelatedPosts::get()->helper( 'relations' )->update_from( $base_id, $base_relations_from );
                }

                if( $to ) {
                    $base_relations_to[$target_id] = $target_data;
                    $target_relations_from[$base_id] = $base_data;

                    CustomRelatedPosts::get()->helper( 'relations' )->update_to( $base_id, $base_relations_to );
                    CustomRelatedPosts::get()->helper( 'relations' )->update_from( $target_id, $target_relations_from );
                }
            }
        }

        die();
    }

    public function ajax_remove_relation()
    {
        if( check_ajax_referer( 'crp_admin', 'security', false ) )
        {
            $base_id = intval( $_POST['base'] );
            $target_id = intval( $_POST['target'] );

            $base = get_post( $base_id );
            $target = get_post( $target_id );

            $post_types = CustomRelatedPosts::option( 'general_post_types', array( 'post', 'page' ) );

            if( $base_id !== $target_id && is_object( $base ) && is_object( $target ) && in_array( $base->post_type, $post_types ) && in_array( $target->post_type, $post_types ) ) {
                $from = $_POST['from'] == 'true' ? true : false;
                $to = $_POST['to'] == 'true' ? true : false;

                // Current Relations
                $base_relations_from = CustomRelatedPosts::get()->relations_from( $base_id );
                $base_relations_to = CustomRelatedPosts::get()->relations_to( $base_id );
                $target_relations_from = CustomRelatedPosts::get()->relations_from( $target_id );
                $target_relations_to = CustomRelatedPosts::get()->relations_to( $target_id );

                if( $from ) {
                    unset( $base_relations_from[$target_id] );
                    unset( $target_relations_to[$base_id] );

                    CustomRelatedPosts::get()->helper( 'relations' )->update_to( $target_id, $target_relations_to );
                    CustomRelatedPosts::get()->helper( 'relations' )->update_from( $base_id, $base_relations_from );
                }

                if( $to ) {
                    unset( $base_relations_to[$target_id] );
                    unset( $target_relations_from[$base_id] );

                    CustomRelatedPosts::get()->helper( 'relations' )->update_to( $base_id, $base_relations_to );
                    CustomRelatedPosts::get()->helper( 'relations' )->update_from( $target_id, $target_relations_from );
                }
            }
        }

        die();
    }
}