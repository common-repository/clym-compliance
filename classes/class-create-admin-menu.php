<?php

/**
 * Class responsible for creating an admin menu page for Clym compliance integration within WordPress.
 * It also registers shortcodes for displaying privacy-related links.
 */
class ClymIntegration_CreateAdminMenuPage {

    /**
     * Constructor method.
     * Adds hooks to create an admin menu and registers shortcodes for Clym privacy and DNT links.
     */
    public function __construct()
    {
        // Adds a new admin menu page.
        add_action('admin_menu', array($this, 'create_admin_menu_page'));
        // Registers a shortcode for displaying a privacy link.
        add_shortcode('clym_privacy_link', array($this, 'clym_privacy_link'));
        // Registers a shortcode for displaying a DNT (Do Not Track) link.
        add_shortcode('clym_dnt_link', array($this, 'clym_dnt_link'));
    }

    /**
     * Creates an admin menu page for Clym compliance settings.
     * This method is hooked to the WordPress admin_menu action.
     */
    public function create_admin_menu_page()
    {
        $capability = 'manage_options'; // Capability required to view this menu.
        $menu_slug = 'clym-integration-settings'; // Slug identifier for the menu.

        // Adds a top-level menu page.
        add_menu_page(
            __('Clym Compliance', 'clym-integration'), // Page title.
            __('Clym Compliance', 'clym-integration'), // Menu title.
            $capability, // Capability required.
            $menu_slug, // Menu slug.
            array($this, 'menu_page_template'), // Function to output the content of the menu page.
            'dashicons-admin-site', // Icon URL.
        );
    }

    /**
     * Outputs the HTML content for the Clym Integration admin page.
     * This is the callback function for the menu page content.
     */
    public function menu_page_template ()
    {
        // Echoes the admin page content, primarily a container for a JS app.
        echo '<div class="wrap">
         <div id="clym-integration-admin-app"></div>
        </div>';
    }

    /**
     * Returns the privacy center link stored in WordPress options.
     * This method is used as a shortcode handler.
     *
     * @return string|false The privacy center link or false if not set.
     */
    public function clym_privacy_link()
    {
        // Retrieves the link from WordPress options.
        $privacy_center_link = get_option('clym_settings_widget_footer_link_1');
        return $privacy_center_link ?: false;
    }

    /**
     * Returns the DNT link stored in WordPress options.
     * This method is used as a shortcode handler.
     *
     * @return string|false The DNT link or false if not set.
     */
    public function clym_dnt_link ()
    {
        // Retrieves the link from WordPress options.
        $dnt_link = get_option('clym_settings_widget_footer_link_2');
        return $dnt_link ?: false;
    }
}

// Instantiates the class to ensure the admin menu and shortcodes are registered.
new ClymIntegration_CreateAdminMenuPage();
