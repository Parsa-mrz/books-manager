<?php
namespace BookManager\Services\Book\Repositories;

use wpdb;

/**
 * Class BooksInfoRepository
 *
 * Repository for handling book information stored in the
 * custom database table. Provides methods for managing
 * the table and retrieving the table name.
 *
 * @package BookManager\Repositories
 */
class BooksInfoRepository {

	/**
	 * WordPress database instance.
	 *
	 * @var wpdb
	 */
	private $wpdb;

	/**
	 * Name of the custom database table.
	 *
	 * @var string
	 */
	private $table;

	/**
	 * BooksInfoRepository constructor.
	 *
	 * @param wpdb $wpdb WordPress database object.
	 */
	public function __construct( wpdb $wpdb ) {
		$this->wpdb  = $wpdb;
		$this->table = $wpdb->prefix . 'books_info';
	}

	/**
	 * Create the custom books_info table if it does not exist.
	 *
	 * Uses dbDelta to handle table creation and schema updates.
	 *
	 * @return void
	 */
	public function create_table(): void {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate = $this->wpdb->get_charset_collate();
		$sql             = "CREATE TABLE {$this->table} (
			ID bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			post_id bigint(20) unsigned NOT NULL,
			isbn varchar(32) NOT NULL,
			PRIMARY KEY  (ID),
			UNIQUE KEY post_id (post_id) isbn
		) {$charset_collate};";

		dbDelta( $sql );
	}

	/**
	 * Saves or updates the ISBN for a given post ID.
	 *
	 * @param int    $post_id The ID of the book post.
	 * @param string $isbn    The ISBN value (ISBN-10 or ISBN-13).
	 * @return bool True on success, false on failure or invalid input.
	 */
	public function save_isbn( int $post_id, string $isbn ): bool {
		if ( $post_id <= 0 || strlen( $isbn ) > 32 ) {
			return false;
		}

		global $wpdb;
		$data   = array(
			'post_id' => $post_id,
			'isbn'    => $isbn,
		);
		$format = array( '%d', '%s' );

		$query           = $wpdb->prepare(
			"SELECT post_id FROM {$this->table} WHERE post_id = %d",
			$post_id
		);
		$existing_record = $wpdb->get_var( $query );

		if ( $existing_record ) {
			$result = $this->wpdb->update(
				$this->table,
				$data,
				array( 'post_id' => $post_id ),
				$format,
				array( '%d' )
			);
		} else {
			$result = $this->wpdb->insert( $this->table, $data, $format );
		}

		if ( false === $result && ! empty( $wpdb->last_error ) ) {
			error_log( 'BooksInfoRepository::save_isbn failed: ' . $wpdb->last_error );
		}

		return false !== $result;
	}


	/**
	 * Deletes the ISBN record for a given post ID.
	 *
	 * @param int $post_id The ID of the book post.
	 * @return bool True on success, false on failure or if no record exists.
	 */
	public function delete_isbn( int $post_id ): bool {
		if ( $post_id <= 0 ) {
			return false;
		}

		$result = $this->wpdb->delete(
			$this->table,
			array( 'post_id' => $post_id ),
			array( '%d' )
		);

		return false !== $result;
	}

	/**
	 * Retrieves the ISBN for a given post ID.
	 *
	 * @param int $post_id The ID of the book post.
	 * @return string The ISBN if found, empty string otherwise.
	 */
	public function get_isbn_by_post_id( int $post_id ): string {
		if ( $post_id <= 0 ) {
			return '';
		}

		$query  = $this->wpdb->prepare(
			"SELECT isbn FROM {$this->table} WHERE post_id = %d",
			$post_id
		);
		$result = $this->wpdb->get_var( $query );

		return $result ?? '';
	}
}
