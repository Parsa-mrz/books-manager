<?php

namespace BookManager\Services\Book\Metaboxes;

use BookManager\Services\Book\Repositories\BooksInfoRepository;
use WP_Post;
/**
 * Class BookMetabox
 *
 * Manages the ISBN metabox for the Book custom post type.
 *
 * @package BookManager\Metaboxes
 */
class BookMetabox {
	/**
	 * The books repository instance.
	 *
	 * @var BooksInfoRepository
	 */
	private $books_repo;
	/**
	 * BookMetabox constructor.
	 *
	 * @param BooksInfoRepository $books_repo The books repository object.
	 */
	public function __construct( BooksInfoRepository $books_repo ) {
		$this->books_repo = $books_repo;
	}

	/**
	 * Adds the ISBN metabox to the 'book' post type.
	 *
	 * @return void
	 */
	public function add_metabox(): void {
		add_meta_box(
			'book_isbn_metabox',
			__( 'Book ISBN', 'book-manager' ),
			array( $this, 'render_metabox' ),
			'book',
			'side',
			'default'
		);
	}

	/**
	 * Renders the HTML for the ISBN metabox.
	 *
	 * @param WP_Post $post The current post object.
	 * @return void
	 */
	public function render_metabox( WP_Post $post ): void {
		$isbn = $this->books_repo->get_isbn_by_post_id( $post->ID );
		wp_nonce_field( 'save_book_isbn', 'book_isbn_nonce' );
		echo '<label for="book_isbn">' . esc_html__( 'ISBN:', 'book-manager' ) . '</label>';
		echo '<input type="text" id="book_isbn" name="book_isbn" value="' . esc_html( $isbn ) . '" style="width:100%;">';
	}


	/**
	 * Saves the ISBN data when the post is saved.
	 *
	 * @param int $post_id The ID of the post being saved.
	 * @return int|void The post ID if no action is taken, otherwise void.
	 */
	public function save_metabox( $post_id ) {
		if ( ! isset( $_POST['book_isbn_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['book_isbn_nonce'] ) ), 'save_book_isbn' ) ) {
			return $post_id;
		}

		if ( 'book' !== get_post_type( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		$isbn = isset( $_POST['book_isbn'] ) ? sanitize_text_field( wp_unslash( $_POST['book_isbn'] ) ) : '';

		$isbn = preg_replace( '/[^a-zA-Z0-9]/', '', $isbn );

		if ( ! $this->is_valid_isbn( $isbn ) ) {
			return $post_id;
		}

		if ( empty( $isbn ) ) {
			$this->books_repo->delete_isbn( $post_id );
		} else {
			$this->books_repo->save_isbn( $post_id, $isbn );
		}

		return $post_id;
	}

	/**
	 * Validates ISBN-10 or ISBN-13 format.
	 *
	 * @param string $isbn The ISBN to validate.
	 * @return bool True if valid, false otherwise.
	 */
	private function is_valid_isbn( string $isbn ): bool {
		$isbn = preg_replace( '/[^0-9X]/', '', $isbn );

		if ( 10 === strlen( $isbn ) ) {
			$check = 0;
			for ( $i = 0; $i < 9; $i++ ) {
				$check += (int) $isbn[ $i ] * ( 10 - $i );
			}
			$last   = 'X' == $isbn[9] ? 10 : (int) $isbn[9];
			$check += $last;
			return 0 === $check % 11;
		}

		if ( 13 === strlen( $isbn ) ) {
			$check = 0;
			for ( $i = 0; $i < 12; $i++ ) {
				$check += (int) $isbn[ $i ] * ( 0 === $i % 2 ? 1 : 3 );
			}
			$checksum = ( 10 - ( $check % 10 ) ) % 10;
			return $checksum === (int) $isbn[12];
		}

		return false;
	}
}
