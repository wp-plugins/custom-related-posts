<?php

class CRP_Relations {

	private $relations_from = array();
	private $relations_to = array();

    public function __construct()
    {
    }

    public function get_data( $post )
    {
        if( !is_object( $post ) ) $post = get_post( $post );

        return array(
            'title' => $post->post_title,
            'permalink' => get_permalink( $post ),
            'status' => $post->post_status,
            'date' => $post->post_date,
        );
    }

    public function get_from( $post_id )
    {
//        delete_post_meta_by_key( 'crp_relations_from' );
//        delete_post_meta_by_key( 'crp_relations_to' );
	    if( !array_key_exists( $post_id, $this->relations_from ) ) {
		    $relations = get_post_meta( $post_id, 'crp_relations_from', true );
		    if( !$relations ) $relations = array();

		    $this->relations_from[$post_id] = $relations;
	    }

	    return $this->relations_from[$post_id];
    }

	public function get_to( $post_id )
	{
		if( !array_key_exists( $post_id, $this->relations_to ) ) {
			$relations = get_post_meta( $post_id, 'crp_relations_to', true );
			if( !$relations ) $relations = array();

			$this->relations_to[$post_id] = $relations;
		}

		return $this->relations_to[$post_id];
	}

    public function update_from( $post_id, $relations )
    {
        $this->relations_from[$post_id] = $relations;
        update_post_meta( $post_id, 'crp_relations_from', $relations );
    }

    public function update_to( $post_id, $relations )
    {
        $this->relations_from[$post_id] = $relations;
        update_post_meta( $post_id, 'crp_relations_to', $relations );
    }
}