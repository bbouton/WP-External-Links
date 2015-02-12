<?php defined('ABSPATH') OR die('No direct access.');
/*
Plugin Name: WP External Links
Plugin URI: http://www.freelancephp.net/wp-external-links-plugin
Description: Open external links in a new window/tab, add "external" / "nofollow" to rel-attribute, set icon, XHTML strict, SEO friendly...
Author: Victor Villaverde Laan
Version: 1.55
Author URI: http://www.freelancephp.net
License: Dual licensed under the MIT and GPL licenses
*/

// constants
if (!defined('WP_EXTERNAL_LINKS_FILE')) { define('WP_EXTERNAL_LINKS_FILE', __FILE__); }
if (!defined('WP_EXTERNAL_LINKS_VERSION')) { define('WP_EXTERNAL_LINKS_VERSION', '1.55'); }
if (!defined('WP_EXTERNAL_LINKS_KEY')) { define('WP_EXTERNAL_LINKS_KEY', 'wp_external_links'); }
if (!defined('WP_EXTERNAL_LINKS_DOMAIN')) { define('WP_EXTERNAL_LINKS_DOMAIN', 'wp-external-links'); }
if (!defined('WP_EXTERNAL_LINKS_OPTIONS_NAME')) { define('WP_EXTERNAL_LINKS_OPTIONS_NAME', 'WP_External_Links_options'); }
if (!defined('WP_EXTERNAL_LINKS_ADMIN_PAGE')) { define('WP_EXTERNAL_LINKS_ADMIN_PAGE', 'wp-external-links-settings'); }

// check plugin compatibility
if (isset($wp_version)
            AND version_compare(preg_replace('/-.*$/', '', $wp_version), '3.4', '>=')
            AND version_compare(phpversion(), '5.2.4', '>=')) {

	// include classes
	require_once('includes/wp-plugin-dev-classes/class-wp-meta-box-page.php');
	require_once('includes/wp-plugin-dev-classes/class-wp-option-forms.php');
	require_once('includes/class-admin-external-links.php');
	require_once('includes/class-wp-external-links.php');

	// create instance
	$WP_External_Links = new WP_External_Links();

    // init test
    if (class_exists('Test_WP_External_Links')) {
        $Test_WP_External_Links = new Test_WP_External_Links;
    }

} else {

	// set error message
    if (!function_exists('wpel_error_notice')):
        function wpel_error_notice() {
            $plugin_title = get_admin_page_title();

            echo '<div class="error">'
                    . sprintf(__('<p>Warning - The plugin <strong>%s</strong> requires PHP 5.2.4+ and WP 3.4+.  Please upgrade your PHP and/or WordPress.'
                                 . '<br/>Disable the plugin to remove this message.</p>'
                                 , WP_EXTERNAL_LINKS_KEY), $plugin_title)
                . '</div>';
        }

        add_action('admin_notices', 'wpel_error_notice');
    endif;

}

/* ommit PHP closing tag, to prevent unwanted whitespace at the end of the parts generated by the included files */
