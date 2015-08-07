<?php

class CRP_Output {

    public function __construct()
    {
    }

    public function output_list( $post_id, $args, $widget = false )
    {
        $post_types = CustomRelatedPosts::option( 'general_post_types', array( 'post', 'page' ) );
        if( !in_array( get_post_type( $post_id ), $post_types ) ) return '';

        $args = shortcode_atts(
            array(
                'title' => __( 'Related Posts', 'custom-related-posts' ),
                'order_by' => 'title',
                'order' => 'ASC',
                'none_text' => __( 'None found', 'custom-related-posts' ),
            ), $args
        );

        $relations = CustomRelatedPosts::get()->relations_to( $post_id );

        // Sort relations
        if( $args['order_by'] == 'title' ) {
            usort( $relations, array( $this, 'sortByTitle' ) );
        } elseif( $args['order_by'] == 'date' ) {
            usort( $relations, array( $this, 'sortByDate' ) );
        } else {
            shuffle( $relations );
        }

        if( $args['order'] == 'DESC') {
            $relations = array_reverse( $relations, true );
        }

        // Check if we can output any relations
        $relations_output = '';
        foreach( $relations as $relation ) {
            if( $relation['status'] == 'publish' ) {
                $relations_output .= apply_filters( 'crp_output_list_item', '<li><a href="' . $relation['permalink'] . '">' . $relation['title'] . '</a></li>', $post_id );
            }
        }

        // Don't output widget if no relations and no text to show
        if( $relations_output == '' && !$args['none_text'] ) return '';

        // Start output
        $output = '';
        if( $widget ) {
            $output .= $widget['before_widget'];

            $title = apply_filters( 'widget_title', $args['title'] );
            if( !empty( $title ) ) {
                $output .= $widget['before_title'] . $title . $widget['after_title'];
            }
        } else {
            if( $args['title'] ) {
                $output .= apply_filters( 'crp_output_list_title', '<h3 class="crp-list-title">' . $args['title'] . '</h3>', $post_id );
            }
        }

        if( $relations_output == '' ) {
            $output .= '<p>' . $args['none_text'] . '</p>';
        } else {
            $output .= '<ul class="crp-list">' . $relations_output . '</ul>';
        }

        if( $widget ) $output .= $widget['after_widget'];

        return apply_filters( 'crp_output_list', $output, $post_id );;
    }

    public function sortByTitle( $a, $b )
    {
        return $a['title'] > $b['title'];
    }

    public function sortByDate( $a, $b )
    {
        return $a['date'] > $b['date'];
    }
}