<?php

namespace BookManager\Services\Book\Admin;

use BookManager\Services\Book\Repositories\BooksInfoRepository;
use WP_List_Table;

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Class BooksListTable
 *
 * Implements a WP_List_Table to display the contents of the books_info table.
 *
 * @package BookManager\Admin
 */
class BooksListTable extends WP_List_Table {
	/**
	 * The books repository instance.
	 *
	 * @var BooksInfoRepository
	 */
	private $books_repo;
	/**
	 * BooksListTable constructor.
	 *
	 * @param BooksInfoRepository $books_repo The books repository object.
	 */
	public function __construct( BooksInfoRepository $books_repo ) {
		parent::__construct(
			array(
				'singular' => __( 'book', 'book-manager' ),
				'plural'   => __( 'books', 'book-manager' ),
				'ajax'     => false,
			)
		);
		$this->books_repo = $books_repo;
	}

	/**
	 * Defines the columns for the table.
	 *
	 * @return array<string, string>
	 */
	public function get_columns(): array {
		return array(
			'ID'         => __( 'ID', 'book-manager' ),
			'post_id'    => __( 'Post ID', 'book-manager' ),
			'book_title' => __( 'Book Title', 'book-manager' ),
			'isbn'       => __( 'ISBN', 'book-manager' ),
			'actions'    => __( 'Actions', 'book-manager' ),
		);
	}

	/**
	 * Prepares the items for the table display.
	 *
	 * @return void
	 */
	public function prepare_items(): void {
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$this->items           = $this->get_items();
	}

	/**
	 * Retrieves the data from the repository and adds the book title.
	 *
	 * @return array<int, object>
	 */
	private function get_items(): array {
		$items = $this->books_repo->get_all_books();
		foreach ( $items as $item ) {
			$post             = get_post( $item->post_id );
			$item->book_title = $post ? $post->post_title : __( 'N/A', 'book-manager' );
		}
		return $items;
	}

	/**
	 * Renders the data for a single column.
	 *
	 * @param object $item The current item object.
	 * @param string $column_name The name of the column.
	 * @return string The formatted output.
	 */
	protected function column_default( $item, $column_name ): string {
		switch ( $column_name ) {
			case 'ID':
			case 'post_id':
			case 'isbn':
				return esc_html( $item->$column_name );
			case 'book_title':
				return esc_html( $item->book_title );
			case 'actions':
				$edit_link = get_edit_post_link( $item->post_id );
				if ( ! $edit_link ) {
					return __( 'N/A', 'book-manager' );
				}
				return sprintf(
					'<a href="%s" class="button button-small">%s</a>',
					esc_url( $edit_link ),
					esc_html__( 'Edit', 'book-manager' )
				);
			default:
				return 'N/A';
		}
	}
}
