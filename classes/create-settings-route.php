<?php

/**
 * Handles the registration of custom REST API routes for Clym integration settings.
 * This class enables CRUD operations on Clym settings through the WordPress REST API.
 */
class ClymIntegrationSettingsRoutes
{
    /**
     * Constructor.
     * Hooks into WordPress to register custom REST API routes upon initialization.
     */
    public function __construct()
    {
        // Registers custom REST API routes.
        add_action('rest_api_init', array($this, 'register_routes'));
    }

    /**
     * Registers custom REST API routes for accessing and manipulating Clym settings.
     */
    public function register_routes()
    {
        $namespace = 'clym-integration/v1';
        $base_route = '/settings';

        // Register route for retrieving settings.
        register_rest_route(
            $namespace,
            $base_route,
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_settings'),
                'permission_callback' => array($this, 'get_settings_permission'),
            )
        );

        // Register route for updating settings.
        register_rest_route(
            $namespace,
            $base_route,
            array(
                'methods' => 'POST',
                'callback' => array($this, 'save_settings'),
                'permission_callback' => array($this, 'save_settings_permission'),
            )
        );

        // Register route for updating subfooter specifically.
        register_rest_route(
            $namespace,
            $base_route . '/style',
            array(
                'methods' => 'POST',
                'callback' => array($this, 'save_subfooter'),
                'permission_callback' => array($this, 'save_settings_permission'),
            )
        );

        // Register route for deleting settings.
        register_rest_route(
            $namespace,
            $base_route,
            array(
                'methods' => 'DELETE',
                'callback' => array($this, 'delete_settings'),
                'permission_callback' => array($this, 'delete_settings_permission'),
            )
        );
    }

    /**
     * Retrieves Clym integration settings.
     * 
     * @return WP_REST_Response REST response with the settings data.
     */
    public function get_settings()
    {       
    
        // Fetch settings from WordPress options.
        $settings = [
            'email' => get_option('clym_settings_email', false),
            'domain' => get_option('clym_settings_domain', false),
            'widget_footer_link_1' => get_option('clym_settings_widget_footer_link_1', false),
            'widget_footer_link_2' => get_option('clym_settings_widget_footer_link_2', false),
            'property_id' => get_option('clym_settings_property_id', false),
            'privacy_widget' => get_option('clym_settings_privacy_widget', false),
            'integration_id' => get_option('clym_settings_integration_id', false),
            'api_key' => get_option('clym_settings_api_key', false)
        ];
        $settingsNames = [
            'email' => 'email address',
            'domain' => 'domain name',
            'widget_footer_link_1' => 'widget link 1',
            'widget_footer_link_2' =>  'widget link 2',
            'property_id' => 'property details',
            'privacy_widget' => 'privacy widget details',
            'integration_id' => 'integration details',
            'api_key' => 'api key details'
        ];
        $settingsCount = count($settings);


        $missingSettings = [];
        foreach ($settings as $key => $value) {
            if ($value === false) {
                $missingSettings[] = $settingsNames[$key];
            }
        }

        if (!empty($missingSettings)) {
            return rest_ensure_response([
                'status' => 'incomplete',
                'message' => 'Some settings are missing',
                'missing_settings' => $missingSettings,
                'settings' => $settings,
                'settings_count' => $settingsCount
            ]);
        }

        return rest_ensure_response([
            'status' => 'complete',
            'settings' => $settings,
        ]);

    }

    /**
     * Checks if the current user has permission to get Clym settings.
     * 
     * @return bool True if permission is granted, otherwise false.
     */
    public function get_settings_permission()
    {
        // Example permission check: allow any user to read settings.
        // Implement more secure checks as needed.
        return true;
    }

    /**
     * Saves or updates Clym integration settings.
     * 
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response REST response indicating the result of save operation.
     */
    public function save_settings($request)
    {
        // Sanitize and save settings from request.
        $options = [
            'clym_settings_email' => sanitize_email($request['email']),
            'clym_settings_domain' => sanitize_text_field($request['domain']),
            'clym_settings_widget_footer_link_1' => esc_url_raw($request['widget_footer_link_1']),
            'clym_settings_widget_footer_link_2' => esc_url_raw($request['widget_footer_link_2']),
            'clym_settings_property_id' => sanitize_text_field($request['property_id']),
            'clym_settings_privacy_widget' => $request['privacy_widget'],
            'clym_settings_integration_id' => sanitize_text_field($request['integration_id']),
            'clym_settings_api_key' => sanitize_text_field($request['api_key'])
        ];

        foreach ($options as $option => $value) {
            update_option($option, $value);
        }

        return rest_ensure_response('Settings updated successfully.');
    }

    /**
     * Checks if the current user has permission to save Clym settings.
     * 
     * @return bool True if permission is granted, otherwise false.
     */
    public function save_settings_permission()
    {
        // Example permission check: require user to have capability to manage options.
        return current_user_can('manage_options');
    }

    /**
     * Saves or updates the subfooter section of Clym settings.
     * 
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response REST response indicating the result of the operation.
     */
    public function save_subfooter($request)
    {
        update_option('clym_settings_has_sub_footer', sanitize_text_field($request['has_sub_footer']));
        update_option('clym_settings_sub_footer', sanitize_textarea_field($request['sub_footer']));

        return rest_ensure_response('Subfooter updated successfully.');
    }

    /**
     * Deletes Clym integration settings.
     * 
     * @param WP_REST_Request $request The request object.
     * @return WP_REST_Response REST response indicating the result of the delete operation.
     */
    public function delete_settings($request)
    {
        // List of option names to delete.
        $options = [
            'clym_settings_email',
            'clym_settings_domain',
            'clym_settings_widget_footer_link_1',
            'clym_settings_widget_footer_link_2',
            'clym_settings_property_id',
            'clym_settings_privacy_widget',
            'clym_settings_integration_id',
            'clym_settings_api_key',
            'clym_settings_has_sub_footer',
            'clym_settings_sub_footer'
        ];

        foreach ($options as $option) {
            delete_option($option);
        }

        return rest_ensure_response('All Clym settings have been deleted.');
    }

    /**
     * Checks if the current user has permission to delete Clym settings.
     * 
     * @return bool True if permission is granted, otherwise false.
     */
    public function delete_settings_permission()
    {
        // Example permission check: require user to have capability to manage options.
        return current_user_can('manage_options');
    }
}

// Instantiate the class to ensure the REST API routes are registered.
new ClymIntegrationSettingsRoutes();
