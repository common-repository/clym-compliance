<?php
/**
 * Plugin Name: Clym Compliance
 * Description: An easy to set up customizable cookie compliance widget: cookie notice, privacy policy, DSARs, and legal documents/policy management. Facilitates compliance with various data privacy regulations.
 * Author: Clym
 * Author URI: https://clym.io
 * Version: 1.1.4
 * Text Domain: clym-compliance
 * License: GPLv2 or later
 * @package Clym Integration
 */

// Prevent direct access to the script
if (!defined('ABSPATH')) {
  exit;
}

// Define plugin paths
define('CLYM_INTEGRATION_PATH', trailingslashit(plugin_dir_path(__FILE__)));
define('CLYM_INTEGRATION_URL', trailingslashit(plugin_dir_url(__FILE__)));

// Load necessary scripts
add_action('admin_enqueue_scripts', 'load_scripts');
add_action('wp_head', 'insert_header_scripts_if_they_exist');
add_action('wp_footer', 'insert_footer_scripts_if_they_exist');

/**
 * Enqueue scripts and styles for the plugin.
 *
 * @return void
 */
function load_scripts()
{
  // Enqueue JavaScript file
  wp_enqueue_script('clym-integration', plugins_url('clym-compliance/dist/index.js'), ['jquery', 'wp-element'], '1.0.0', true);

  // Localize script with data
  wp_localize_script('clym-integration', 'appLocalizer', [
    'apiUrl' => home_url('/wp-json'),
    'nonce' => wp_create_nonce('wp_rest'),
  ]);

  // Localize script with admin data
  wp_localize_script(
    'clym-integration',
    'adminLocalizer',
    array(
      'adminEmail' => wp_get_current_user()->user_email,
      'siteUrl' => home_url(),
      'nonce' => wp_create_nonce('admin_nonce'),
    )
  );

  // Enqueue CSS file
  wp_enqueue_style('clym-integration', plugins_url('clym-compliance/dist/index.css'), [], '1.0.0');
}

/**
 * Inserts custom scripts into the header if they exist.
 *
 * @return false if the option does not exist or is empty.
 */
function insert_header_scripts_if_they_exist()
{
  $privacy_widget = get_option('clym_settings_privacy_widget');
  if ($privacy_widget) {
    // 'esc_attr' will espace the double quotes of our widgets scripts tag
    // and replace it with the html symbol '&#039;' so we replace it with single quotes.
    $escapedWidget = str_replace("&#039;", "'", esc_attr($privacy_widget));
    echo html_entity_decode($escapedWidget);
  } else {
    return false;
  }
}

/**
 * Inserts custom scripts into the footer if they exist and are enabled.
 */
function insert_footer_scripts_if_they_exist()
{
  $sub_footer = get_option('clym_settings_sub_footer');
  $has_sub_footer = get_option('clym_settings_has_sub_footer');
  if ($sub_footer && $has_sub_footer) {
    echo html_entity_decode(esc_attr($sub_footer));
  } else {
    return false;
  }
}

// Include necessary files for admin menu and settings route
require_once CLYM_INTEGRATION_PATH . 'classes/class-create-admin-menu.php';
require_once CLYM_INTEGRATION_PATH . 'classes/create-settings-route.php';
