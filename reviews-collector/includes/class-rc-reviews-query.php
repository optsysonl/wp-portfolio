<?php

class RC_Review_Query extends RC_Object_Query {

    /**
     * Valid query vars for products.
     *
     * @return array
     */
    protected function get_default_query_vars() {
        return array_merge(
            parent::get_default_query_vars(),
            array(

            )
        );
    }

    /**
     * Get products matching the current query vars.
     *
     * @return array|object of WC_Product objects
     */
    public function get_reviews() {
        global $wpdb;
        $args = $this->get_query_vars();
        //TODO update query with custom params
        $data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}rc_reviews");
        return $data;
    }
}