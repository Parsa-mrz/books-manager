<?php
namespace BookManager\Providers;

use BookManager\Services\Book\Metaboxes\BookMetabox;
use BookManager\Services\Book\PostTypes\BookPostType;
use BookManager\Services\Book\Repositories\BooksInfoRepository;
use League\Container\ServiceProvider\AbstractServiceProvider;

/**
 * Class BookServiceProvider
 *
 * Service provider for registering Book Manager services
 * into the container. Handles binding of the WordPress
 * database instance and the book information repository.
 *
 * @package BookManager\Providers
 */
class BookServiceProvider extends AbstractServiceProvider {

	/**
	 * List of services provided by this service provider.
	 *
	 * @var array<int, string>
	 */
	protected $provides = array(
		'wpdb',
		'books.repo',
		'books.cpt',
		'books.metabox',
	);

	/**
	 * Register services into the container.
	 *
	 * Adds the WordPress database object and the
	 * BooksInfoRepository to the service container.
	 *
	 * @return void
	 */
	public function register(): void {
		$container = $this->getContainer();

		global $wpdb;
		$container->add( 'wpdb', $wpdb );

		// Repositories.
		$container->add(
			'books.repo',
			function () use ( $container ) {
				return new BooksInfoRepository( $container->get( 'wpdb' ) );
			}
		);

		// CPT and Taxonomies Register.
		$container->add(
			'books.cpt',
			function () {
				return new BookPostType();
			}
		);

		// Metabox.
		$container->add(
			'books.metabox',
			function () use ( $container ) {
				return new BookMetabox( $container->get( 'books.repo' ) );
			}
		);
	}
}
