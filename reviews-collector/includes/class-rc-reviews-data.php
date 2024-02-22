<?php

class RC_Reviews_Data {

	public function add($data)
	{
		global $wpdb;
		$wpdb->insert(
			"{$wpdb->prefix}rc_reviews", $data
		);

		return $wpdb->insert_id;
	}

    /**
     * @name get
     * @description Get review by id.
     *
     * @param {integer} $id
     * @return {array}
     */
	public function get($id){
        global $wpdb;

        $sql = "SELECT 
                  review_id,
                  review_author,
                  review_date,
                  review_content, 
                  review_rating, 
                  review_author_email,
                  review_approved 
                  FROM {$wpdb->prefix}rc_reviews
                  WHERE review_id=".$id."
                ";
        return $wpdb->get_results($sql);
    }

    public function update($data = array()){
        global $wpdb;
	    if( empty( $data )){
	        return false;
        }

        $table_name = $wpdb->prefix . "rc_reviews";
        return $wpdb->update(
            $table_name,
            array(
                "review_author" => (isset($data['review_author']) && !empty($data['review_author'])) ? $data['review_author'] : "",
                "review_author_email" => (isset($data['review_author_email']) && !empty($data['review_author_email'])) ? $data['review_author_email'] : "",
                "review_date" => (isset($data['review_date']) && !empty($data['review_date'])) ? $data['review_date'] : "",
                "review_content" => (isset($data['review_content']) && !empty($data['review_content'])) ? $data['review_content'] : "",
                "review_rating" => (isset($data['review_rating']) && !empty($data['review_rating'])) ? $data['review_rating'] : "",
                "review_approved" => (isset($data['review_approved'])) ? $data['review_approved'] : 0,
            ),
            array(
                "review_id" => $data['id']
            )
        );
    }

	public function get_list($data, $page)
	{
		global $wpdb;
		$sql   = "SELECT review_id, review_author, review_date, review_content, review_rating, review_author_email FROM {$wpdb->prefix}rc_reviews 
			where review_approved = 1 ";

		$count = $this->get_count();

		$items_per_page = 5;
		if (isset($data['per_page'])) {
			$items_per_page = $data['per_page'];
		}

		$offset = $wpdb->prepare( 'offset %d ', ( $page * $items_per_page ) - $items_per_page);
		$limit = $wpdb->prepare( 'limit %d ', $items_per_page );

		$results['reviews'] = $wpdb->get_results($sql . ' order by review_date DESC ' . $limit . $offset);
		$results['pagination'] = [
				'page_count'=> ceil($count / $items_per_page),
				'per_page'	=> $limit,
				'count'		=> $count
			];
		return $results;
	}

	private function get_count()
	{
		global $wpdb;
		$sql   = "SELECT count(*) as rows_count FROM {$wpdb->prefix}rc_reviews where review_approved = 1 ";
		$results = $wpdb->get_row($sql);

		return $results->rows_count;
	}

	public function get_carousel($data)
	{
		global $wpdb;
		$sql   = "SELECT review_id, review_author, review_date, review_content, review_rating, review_author_email FROM {$wpdb->prefix}rc_reviews 
			where review_approved = 1 ";

		$items_per_page = 5;
		if (isset($data['per_page'])) {
			$items_per_page = (int)$data['per_page'];
		}

		$limit = $wpdb->prepare( 'limit %d ', $items_per_page );

		$results['reviews'] = $wpdb->get_results($sql . ' order by review_date ' . $limit);
		return $results;
	}
}