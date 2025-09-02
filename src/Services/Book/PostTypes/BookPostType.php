<?php

namespace BookManager\Services\Book\PostTypes;

/**
 * Class BookPostType
 *
 * Registers the custom post type 'book' and associated taxonomies 'publisher' and 'authors'
 * for the Book Manager plugin.
 */
class BookPostType {

	/**
	 * Register the custom post type and taxonomies.
	 *
	 * Initializes the registration of the 'book' custom post type and its associated
	 * taxonomies ('publisher' and 'authors') in WordPress.
	 *
	 * @return void
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomies();
	}

	/**
	 * Register the 'book' custom post type.
	 *
	 * Sets up the 'book' custom post type with its labels, settings, and supported features
	 * for use in the WordPress admin interface and front-end.
	 *
	 * @return void
	 */
	public function register_post_type() {
		register_post_type(
			'book',
			array(
				'labels'          => array(
					'name'               => __( 'Books', 'book-manager' ),
					'singular_name'      => __( 'Book', 'book-manager' ),
					'add_new'            => __( 'Add New', 'book-manager' ),
					'add_new_item'       => __( 'Add New Book', 'book-manager' ),
					'edit_item'          => __( 'Edit Book', 'book-manager' ),
					'new_item'           => __( 'New Book', 'book-manager' ),
					'view_item'          => __( 'View Book', 'book-manager' ),
					'search_items'       => __( 'Search Books', 'book-manager' ),
					'not_found'          => __( 'No books found', 'book-manager' ),
					'not_found_in_trash' => __( 'No books found in Trash', 'book-manager' ),
					'menu_name'          => __( 'Books', 'book-manager' ),
				),
				'public'          => true,
				'show_ui'         => true,
				'show_in_menu'    => true,
				'show_in_rest'    => true,
				'menu_icon'       => 'dashicons-book-alt',
				'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
				'has_archive'     => true,
				'rewrite'         => array( 'slug' => 'books' ),
				'capability_type' => 'post',
				'taxonomies'      => array( 'publisher', 'authors' ),
			)
		);
	}

	/**
	 * Register the 'publisher' and 'authors' taxonomies.
	 *
	 * Sets up the 'publisher' and 'authors' taxonomies for the 'book' custom post type,
	 * enabling categorization and management of books by publisher and author.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		register_taxonomy(
			'publisher',
			'book',
			array(
				'labels'            => array(
					'name'          => __( 'Publishers', 'book-manager' ),
					'singular_name' => __( 'Publisher', 'book-manager' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
			)
		);

		register_taxonomy(
			'authors',
			'book',
			array(
				'labels'            => array(
					'name'          => __( 'Authors', 'book-manager' ),
					'singular_name' => __( 'Author', 'book-manager' ),
				),
				'public'            => true,
				'show_ui'           => true,
				'show_in_rest'      => true,
				'show_admin_column' => true,
				'hierarchical'      => false,
			)
		);
	}
}
