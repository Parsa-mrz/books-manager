<?php

namespace BookManager\Services\Book\Admin;

use BookManager\Services\Book\Repositories\BooksInfoRepository;

/**
 * Class AdminPage
 *
 * Creates the admin menu page for displaying the books information table.
 * Uses a lazy-loaded factory callback to instantiate the BooksListTable
 * only when the page is rendered, ensuring WordPress admin APIs are available.
 *
 * @package BookManager\Admin
 */
class AdminPage {

	/**
	 * Callable that returns a BooksListTable instance.
	 *
	 * @var callable
	 */
	private $list_table_factory;

	/**
	 * AdminPage constructor.
	 *
	 * @param callable $list_table_factory A callable that returns a BooksListTable instance.
	 */
	public function __construct( callable $list_table_factory ) {
		$this->list_table_factory = $list_table_factory;
	}

	/**
	 * Adds the 'Book Manager' submenu page under the 'Books' post type menu.
	 *
	 * @return void
	 */
	public function add_menu_page(): void {
		add_submenu_page(
			'edit.php?post_type=book',
			__( 'Book Manager', 'book-manager' ),
			__( 'Book Manager', 'book-manager' ),
			'manage_options',
			'book-manager',
			array( $this, 'render_page' )
		);
	}

	/**
	 * Renders the content of the Book Manager admin page.
	 *
	 * Instantiates the BooksListTable via the factory callback,
	 * prepares the items, and displays the table.
	 *
	 * @return void
	 */
	public function render_page(): void {
		$list_table = call_user_func( $this->list_table_factory );
		$list_table->prepare_items();
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<?php $list_table->display(); ?>
		</div>
		<?php
	}
}
