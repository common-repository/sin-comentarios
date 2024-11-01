<?php
/*
Plugin Name: Sin comentarios
Description: Deshabilita los comentarios completamente. Simplemente activa este plugin para deshabilitar los comentarios.
Version: 1.2
Requires at least: 2.5.1
Requires PHP: 5.2
Author: Christian Cabrero
Author URI: https://cabrero.ch
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: cabrero-plugins
Domain Path: /languages
 */

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Añade más links en los detalles del plugin
function sin_comentarios_meta_links( $links, $file ) {
	if ( $file === 'sin-comentarios/sin-comentarios.php' ) {
		$links[] = '<a href="https://profiles.wordpress.org/christiancabrero/#content-plugins" target="_blank" title="' . __( 'Más plugins &#187;' ) . '"><strong>' . __( 'Más plugins &#187;' ) . '</strong></a>';
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'sin_comentarios_meta_links', 10, 2 );

//Deshabilita los comentarios totalmente
add_action('admin_init', function () {
    // Redirect any user trying to access comments page
    global $pagenow;
    
    if ($pagenow === 'edit-comments.php') {
        wp_redirect(admin_url());
        exit;
    }

    // Remove comments metabox from dashboard
    remove_meta_box('dashboard_recent_comments', 'dashboard', 'normal');

    // Disable support for comments and trackbacks in post types
    foreach (get_post_types() as $post_type) {
        if (post_type_supports($post_type, 'comments')) {
            remove_post_type_support($post_type, 'comments');
            remove_post_type_support($post_type, 'trackbacks');
        }
    }
});

// Close comments on the front-end
add_filter('comments_open', '__return_false', 20, 2);
add_filter('pings_open', '__return_false', 20, 2);

// Hide existing comments
add_filter('comments_array', '__return_empty_array', 10, 2);

// Remove comments page in menu
add_action('admin_menu', function () {
    remove_menu_page('edit-comments.php');
});

// Remove comments links from admin bar
add_action('init', function () {
    if (is_admin_bar_showing()) {
        remove_action('admin_bar_menu', 'wp_admin_bar_comments_menu', 60);
    }
});

?>
