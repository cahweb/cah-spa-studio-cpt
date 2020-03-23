<?php
/**
 * Plugin Name: CAH Studio CPT (SPA)
 * Description: A Custom Post Type for displaying information about studios for the School of Performing Arts
 * Author: Mike W. Leavitt
 * Version: 0.1.0
 */
defined( 'ABSPATH' ) or die( "No direct access plzthx." );

// Set useful constants
define( 'CAH_SPA_STUDIO__VERSION', '0.1.0' );
define( 'CAH_SPA_STUDIO__PLUGIN_FILE', __FILE__ );
define( 'CAH_SPA_STUDIO__PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'CAH_SPA_STUIDO__PLUGIN_DIR_URL', plugin_dir_url( __FILE__ ) );

// Include other files
require_once 'includes/cah-spa-studio-cpt-registrar.php';
require_once 'includes/cah-spa-studio-cpt-templater.php';
require_once 'includes/cah-spa-studio-cpt-editor.php';

// Flush rewrites on activation/deactivation
register_activation_hook( __FILE__, function() {
    flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, function() {
    flush_rewrite_rules();
});

// Queue up our helper classes, to let them do that voodoo
// that they do so well
add_action( 'init', [ 'CAH_SPAStudioCPTRegistrar', 'register' ], 10, 0 );
add_action( 'init', [ 'CAH_SPAStudioCPTTemplater', 'set' ], 10, 0 );
add_action( 'init', [ 'CAH_SPAStudioCPTEditor', 'setup' ], 10, 0 );
?>