<?php

namespace BookManager\Services\Book\Admin;

/**
 * Class AdminPage
 *
 * Creates the admin menu page for displaying the books information table.
 *
 * @package BookManager\Admin
 */
class AdminPage {

	/**
	 * Adds the 'Book Manager' submenu page under the 'Books' menu.
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
	 * Renders the content of the admin page.
	 *
	 * @return void
	 */
	public function render_page(): void {
		?>
		<div class="wrap">
			<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
			<p><?php echo esc_html__( 'This page displays the contents of the custom `books_info` database table.', 'book-manager' ); ?></p>

		</div>
		<?php
	}
}
