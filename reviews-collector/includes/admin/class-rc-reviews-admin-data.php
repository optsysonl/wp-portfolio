<?php

if (!class_exists('WP_List_Table')) {
	require_once (ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class RC_Reviews_Admin_Table extends WP_List_Table
{
	/**
	 * Max items.
	 *
	 * @var int
	 */
	protected $max_items;

	public function __construct()
	{
		parent::__construct(array(
			'singular' => 'Reviews',
			'plural' => 'Reviews',
			'ajax' => false
		));
	}

	/**
	 * Prepare the items for the table to process
	 *
	 * @return Void
	 */
	public function prepare_items()
	{
		$columns = $this->get_columns();
		$hidden = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();
		$this->process_bulk_action();

		$current_page          = absint( $this->get_pagenum() );
		$per_page              = 20; // TODO config

		$this->get_table_data( $current_page, $per_page );

		/**
		 * Pagination.
		 */
		$this->set_pagination_args(
			array(
				'total_items' => $this->max_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $this->max_items / $per_page ),
			)
		);


		$this->_column_headers = array($columns, $hidden, $sortable);
	}

	/**
	 * Override the parent columns method. Defines the columns to use in your listing table
	 *
	 * @return Array
	 */
	public function get_columns()
	{
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'review_id'          	=> 'ID',
			'review_author'       	=> 'Author',
			'review_author_email' 	=> 'Email',
			'review_author_ip'     	=> 'Author Ip',
			'review_date'    		=> 'Date',
			'review_content'      	=> 'Content',
			'review_rating'      	=> 'Rating',
			'review_approved'      	=> 'Approved',
		);
		return $columns;
	}

	/**
	 * Define which columns are hidden
	 *
	 * @return Array
	 */
	public function get_hidden_columns()
	{
		return array();
	}

	/**
	 * Define the sortable columns
	 *
	 * @return Array
	 */
	public function get_sortable_columns()
	{
		return array(
			'review_id'	=> array('review_id', false),
			'review_author' => 'review_author',
			'review_date'	=> 'review_date'
		);
	}

	/**
	 * Get the table data
	 *
	 * @return Array
	 */
	private function get_table_data($current_page, $per_page)
	{
		global $wpdb;

		$sql = "SELECT SQL_CALC_FOUND_ROWS  * FROM {$wpdb->prefix}rc_reviews ";

		if (!empty($_REQUEST['orderby'])) {
			$sql.= ' ORDER BY ' . esc_sql($_REQUEST['orderby']);
			$sql.= !empty($_REQUEST['order']) ? ' ' . esc_sql($_REQUEST['order']) : ' ASC';
		}else{
            $sql.= ' ORDER BY review_date DESC';
        }
		$data = $wpdb->get_results( $wpdb->prepare( $sql ." LIMIT %d, %d;", ( $current_page - 1 ) * $per_page, $per_page ), 'ARRAY_A');
		$this->max_items = $wpdb->get_var( 'SELECT FOUND_ROWS();' );
		$this->items = $data;
	}

	/**
	 * Define what data to show on each column of the table
	 *
	 * @param  Array $item        Data
	 * @param  String $column_name - Current column name
	 *
	 * @return Mixed
	 */
	public function column_default( $item, $column_name )
	{
		switch( $column_name ) {
			case 'review_id':
			case 'review_author':
			case 'review_author_email':
			case 'review_author_ip':
			case 'review_date':
			case 'review_approved':
			case 'review_type':
			case 'review_rating':
				return $item[ $column_name ];
			case 'review_content':
				return $this->review_content($item);
			default:
				return print_r( $item, true ) ;
		}
	}

	/**
	 * @param object $item
	 * @return string|void
	 */
	protected function column_cb($item)
	{
		return sprintf('<input type="checkbox" name="bulk-action[]" value="%s" />', $item['review_id']);
	}

	/**
	 * @return array
	 */
	public function get_bulk_actions()
	{
		$actions = ['bulk-delete' => 'Delete',
			'bulk-approve' => 'Approve'
			];
		return $actions;
	}

	public function process_bulk_action()
	{
		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )) {
			$delete_ids = esc_sql( $_POST['bulk-action'] );
			foreach ( $delete_ids as $id ) {
				self::delete_record( $id );
			}
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-approve' ) || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-approve' )) {
			$approve_ids = esc_sql( $_POST['bulk-action'] );
			foreach ( $approve_ids as $id ) {
				self::approve_record( $id );
			}
		}
	}

	function column_review_content($item)
	{
		$actions = array(
			'edit' => sprintf('<a href="?page=rc_reviews_form&id=%s">%s</a>', $item['review_id'], __('Edit', 'reviewscollector')),
			'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['review_id'], __('Delete', 'reviewscollector')),
		);

		return sprintf('%s %s',
			$item['review_content'],
			$this->row_actions($actions)
		);
	}

	/**
	 * @param $id
	 */
	protected static function delete_record($id)
	{
		global $wpdb;
		$wpdb->delete("{$wpdb->prefix}rc_reviews", ['review_id' => $id], ['%d']);
	}

	/**
	 * @param $id
	 */
	protected static function approve_record($id)
	{
		global $wpdb;
		$wpdb->update("{$wpdb->prefix}rc_reviews", ['review_approved' => 1] ,['review_id' => $id]);
	}

}

