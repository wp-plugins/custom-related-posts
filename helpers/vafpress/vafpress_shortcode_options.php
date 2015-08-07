<?php

$shortcode_generator = array(
    'Shortcodes' => array(
        'elements' => array(
            'custom_related_posts' => array(
                'title'   => 'Custom Related Posts',
                'code'    => '[custom-related-posts]',
                'attributes' => array(
                    array(
                        'type' => 'textbox',
                        'name' => 'title',
                        'label' => __('Title', 'custom-related-posts'),
                        'default' => __( 'Related Posts', 'custom-related-posts' ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'order_by',
                        'label' => __('Order By', 'custom-related-posts'),
                        'items' => array(
                            array(
                                'value' => 'title',
                                'label' => __('Title', 'custom-related-posts'),
                            ),
                            array(
                                'value' => 'date',
                                'label' => __('Date', 'custom-related-posts'),
                            ),
                            array(
                                'value' => 'rand',
                                'label' => __('Random', 'custom-related-posts'),
                            ),
                        ),
                        'default' => array(
                            'title',
                        ),
                    ),
                    array(
                        'type' => 'select',
                        'name' => 'order',
                        'label' => __('Order', 'custom-related-posts'),
                        'items' => array(
                            array(
                                'value' => 'ASC',
                                'label' => __('Ascending', 'custom-related-posts'),
                            ),
                            array(
                                'value' => 'DESC',
                                'label' => __('Descending', 'custom-related-posts'),
                            ),
                        ),
                        'default' => array(
                            'ASC',
                        ),
                    ),
                    array(
                        'type' => 'textbox',
                        'name' => 'none_text',
                        'label' => __('No related posts text', 'custom-related-posts'),
                        'default' => __( 'None found', 'custom-related-posts' ),
                    ),
                ),
            ),
        ),
    ),
);