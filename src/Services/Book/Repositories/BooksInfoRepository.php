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
		$this->table = $this->wpdb->prefix . 'books_info';
	}

	/**
	 * Get the name of the custom table.
	 *
	 * @return string
	 */
	public function table_name(): string {
		return $this->table;
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
			UNIQUE KEY post_id (post_id)
		) {$charset_collate};";

		dbDelta( $sql );
	}
}
