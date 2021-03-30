<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://sunilprajapati.com/
 * @since             1.0.0
 * @package           Sharechat_Autopost
 *
 * @wordpress-plugin
 * Plugin Name:       Sharechat Autopost addon
 * Plugin URI:        
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Sunil Prajapati
 * Author URI:        http://sunilprajapati.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       sharechat-autopost
 * Domain Path:       /languages
 */
// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('SHARECHAT_AUTOPOST_VERSION', '1.0.0');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-sharechat-autopost-activator.php
 */
function activate_sharechat_autopost() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-sharechat-autopost-activator.php';
    Sharechat_Autopost_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-sharechat-autopost-deactivator.php
 */
function deactivate_sharechat_autopost() {
    require_once plugin_dir_path(__FILE__) . 'includes/class-sharechat-autopost-deactivator.php';
    Sharechat_Autopost_Deactivator::deactivate();
}

register_activation_hook(__FILE__, 'activate_sharechat_autopost');
register_deactivation_hook(__FILE__, 'deactivate_sharechat_autopost');

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path(__FILE__) . 'includes/class-sharechat-autopost.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_sharechat_autopost() {

    $plugin = new Sharechat_Autopost();
    $plugin->run();
}

run_sharechat_autopost();
