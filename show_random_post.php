<?php
/*
Plugin Name: Show_Random_Post
Plugin URI: http://zeidan.info/show-random-post-wordpress-plugin/
Description: Show a Random Post that loops automatically on a Widget.
Version: 0.1.0
Author: Eric Zeidan
Author URI: http://zeidan.es
License: GPL2
 */

/*  Copyright 2015 Eric Zeidan  (email : k2klettern@gmail.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

add_action('plugins_loaded', 'showrandompost_text');

function showrandompost_text() {
	load_plugin_textdomain('show_random_post', false, basename(dirname(__FILE__)) . '/langs');
}

require_once 'lib/functions.php';

// Make sure we don't expose any info if called directly
if (!function_exists('add_action')) {
	_e('Hi there!  I\'m just a plugin, not much I can do when called directly.', 'show_random_post');
	exit;
}

register_activation_hook(__FILE__, 'showrandompost_plugin_activate');
add_action('admin_init', 'showrandompost_plugin_redirect');

function showrandompost_plugin_activate() {
	add_option('showrandompost_plugin_do_activation_redirect', true);
}

function showrandompost_plugin_redirect() {
	if (get_option('showrandompost_plugin_do_activation_redirect', false)) {
		delete_option('showrandompost_plugin_do_activation_redirect');
		if (!isset($_GET['activate-multi'])) {
			wp_redirect("admin.php?page=showrandompost-plugin");
		}
	}
}


add_action('admin_menu', 'showrandompost_setup_menu');

function showrandompost_setup_menu() {
	add_management_page('showrandompost Plugin Page', 'Show Random Post Options', 'manage_options', 'showrandompost-plugin', 'showrandompost_init', 'dashicons-admin-tools', 81);
}

function showrandompost_init() {

	if (!current_user_can('manage_options')) {
		wp_die(_e('You are not authorized to view this page.', 'show_random_post'));
	}

?>

	<div class="wrap">
	<div id="welcome-panel" class="welcome-panel">
		<div class="welcome-panel-content">
			<div class="welcome-panel-column-container">
	<h1><?php _e('Show Random Post','show_random_post'); ?></h1>
    <p><span><?php _e('by Eric Zeidan','show_random_post'); ?></span><p>
    		</div>
    	</div>
    </div>
	</div> <!-- end wrap -->
<?php
}
?>