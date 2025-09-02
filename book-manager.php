<?php
/**
 * Plugin Name: Book Manager
 * Plugin URI:  #
 * Description: A plugin to manage book information, including ISBNs, using a custom database table.
 * Version:     1.0.0
 * Author:      Parsa Mirzaie
 * Author URI:  https://parsamirzaie.com
 * License:     GPL-2.0-or-later
 * Text Domain: book-manager
 * Domain Path: /languages
 */

use BookManager\Providers\BookServiceProvider;
use Rabbit\Application;
use Rabbit\Database\DatabaseServiceProvider;
use Rabbit\Logger\LoggerServiceProvider;
use Rabbit\Plugin;
use Rabbit\Redirects\AdminNotice;
use Rabbit\Redirects\RedirectServiceProvider;
use Rabbit\Templates\TemplatesServiceProvider;
use Rabbit\Utils\Singleton;
use Exception;
use League\Container\Container;



if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$autoloader_path = __DIR__ . '/vendor/autoload.php';

/**
 * Ensure Composer dependencies are available before bootstrapping the plugin.
 */
if ( ! file_exists( $autoloader_path ) ) {
	add_action(
		'admin_notices',
		function () {
			printf(
				'<div class="notice notice-error is-dismissible"><p><strong>%s</strong></p><p>%s</p></div>',
				esc_html__( 'Book Manager Plugin requires Composer dependencies to be installed.', 'book-manager' ),
				esc_html__( 'Please run `composer install` from the plugin directory to activate this plugin.', 'book-manager' )
			);
		}
	);
	return;
}

require_once $autoloader_path;

/**
 * Main plugin class for Book Manager.
 *
 * Handles the initialization, service registration, hooks, and exception handling
 * for the Book Manager plugin. Uses the Rabbit Framework for service providers,
 * dependency injection, and bootstrapping.
 */
class BookManager extends Singleton {

	/**
	 * The Rabbit application container instance.
	 *
	 * @var Container
	 */
	private $application;

	/**
	 * BookManager constructor.
	 *
	 * Initializes the plugin by loading it into the Rabbit Application
	 * and setting up hooks and services.
	 */
	public function __construct() {
		$this->application = Application::get()->loadPlugin( __DIR__, __FILE__, 'config' );
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 *
	 * Sets up service providers, registers activation and deactivation hooks,
	 * boots the plugin, and handles exceptions gracefully by displaying
	 * admin notices and logging errors.
	 *
	 * @return void
	 */
	public function init() {
		try {
			/**
			 * Load service providers
			 */
			$this->registerServices();

			/**
			 * Activation hooks
			 */
			$this->application->onActivation(
				function () {
					$this->setupHooks();
				}
			);

			/**
			 * Deactivation hooks
			 */
			$this->application->onDeactivation(
				function () {
				}
			);

			$this->application->boot(
				function ( Plugin $plugin ) {
					$plugin->loadPluginTextDomain();
				}
			);

		} catch ( Exception $e ) {
			/**
			 * Print the exception message to admin notice area
			 */
			add_action(
				'admin_notices',
				function () use ( $e ) {
					AdminNotice::permanent(
						array(
							'type'    => 'error',
							'message' => $e->getMessage(),
						)
					);
				}
			);

			/**
			 * Log the exception to file
			 */
			add_action(
				'init',
				function () use ( $e ) {
					if ( $this->application->has( 'logger' ) ) {
						$this->application->get( 'logger' )->warning( $e->getMessage() );
					}
				}
			);

		}
	}

	/**
	 * Retrieve the application container instance.
	 *
	 * Provides access to the Rabbit Application container, which
	 * manages dependency injection and registered services.
	 *
	 * @return Container The application container.
	 */
	public function getApplication() {
		return $this->application;
	}

	/**
	 * Register all required service providers for this plugin.
	 *
	 * Registers Redirects, Database, Templates, and Logger service providers
	 * with the Rabbit Application container.
	 *
	 * @return void
	 */
	private function registerServices() {
		$this->application->addServiceProvider( RedirectServiceProvider::class );
		$this->application->addServiceProvider( DatabaseServiceProvider::class );
		$this->application->addServiceProvider( TemplatesServiceProvider::class );
		$this->application->addServiceProvider( LoggerServiceProvider::class );

		$this->application->addServiceProvider( BookServiceProvider::class );
	}

	/**
	 * Setup activation hooks for the plugin.
	 *
	 * This method is invoked during plugin activation to register
	 * custom hooks or initialization routines.
	 *
	 * @return void
	 */
	private function setupHooks() {
		if ( $this->application->has( 'books.repo' ) ) {
			$this->application->get( 'books.repo' )->create_table();
		}

		flush_rewrite_rules();
	}
}

return BookManager::get();
