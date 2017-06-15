<?php
/**
 * Creates the menu item for the plugin.
 *
 * @package WooCommerceSharpSpringIntegration
 */

/**
 * Creates the menu item for the plugin.
 *
 * Registers a new menu item under 'Tools' and uses the dependency passed into
 * the constructor in order to display the page corresponding to this menu item.
 *
 * @package WooCommerceSharpSpringIntegration
 */
class WCSSINTSettingsPage {

	/**
	 * A reference the class responsible for rendering the submenu page.
	 *
	 * @var    Menu_Page
	 * @access private
	 */
	private $menu_page;

	/**
	 * Initializes all of the partial classes.
	 *
	 * @param Menu_Page $menu_page A reference to the class that renders the page for the plugin.
	 */
	public function __construct( $menu_page ) {
		$this->menu_page = $menu_page;
	}

	/**
	 * Adds a menu for this plugin.
	 */
	public function init() {
		 add_action( 'admin_menu', array( $this, 'init_settings_page' ) );
		 add_action( 'admin_init', array( $this->menu_page, 'page_init' ) );
	}

	/**
	 * Creates the menu item and calls on the Submenu Page object to render
	 * the actual contents of the page.
	 */
	public function init_settings_page() {
		add_menu_page(
			__( 'SharpSpring Integration Settings', 'textdomain' ),
			'SharpSpring Integration',
			'manage_options',
			'wcssint',
			array( $this->menu_page, 'render' ),
			'dashicons-editor-kitchensink'
		);
	}
}
