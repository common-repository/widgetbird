<?php
/**
 * Plugin Name: Widgetbird
 * Description: Easily add your Widgetbird widget to your WordPress site.
 * Version: 1.0.2
 * Author: Widgetbird
 * Author URI: https://widgetbird.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Prevent direct access to the file
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

// Add the Widgetbird script to the footer
function widgetbird_add_widget() {
    $widget_id = get_option( 'widgetbird_widget_id' );
    if ( !empty( $widget_id ) ) {
        ?>
        <script>
            (function (w,g,t,b,i,r,d) {
                w['wbWidget']=b;w[b] = w[b] || function () { (w[b].q = w[b].q || []).push(arguments) };
                r = g.createElement(t), d = g.getElementsByTagName(t)[0];
                r.id = b; r.src = i; r.async = 1; d.parentNode.insertBefore(r, d);
            }(window, document, 'script', 'wb_init', 'https://app.widgetbird.com/widget.min.js'));

            wb_init('init', '<?php echo esc_html( $widget_id ); ?>');
        </script>
        <?php
    }
}
add_action( 'wp_footer', 'widgetbird_add_widget' );

// Add settings page
function widgetbird_add_admin_menu() {
    add_options_page( 'Widgetbird', 'Widgetbird', 'manage_options', 'widgetbird', 'widgetbird_options_page' );
}
add_action( 'admin_menu', 'widgetbird_add_admin_menu' );

// Register settings
function widgetbird_settings_init() {
    register_setting( 'widgetbird', 'widgetbird_widget_id' );
    
    add_settings_section(
        'widgetbird_widgetbird_section',
        __( 'Widgetbird', 'widgetbird' ),
        'widgetbird_settings_section_callback',
        'widgetbird'
    );
    
    add_settings_field(
        'widgetbird_widget_id',
        __( 'Widget ID', 'widgetbird' ),
        'widgetbird_widget_id_render',
        'widgetbird',
        'widgetbird_widgetbird_section'
    );
}
add_action( 'admin_init', 'widgetbird_settings_init' );

// Widget ID field render
function widgetbird_widget_id_render() {
    $widget_id = get_option( 'widgetbird_widget_id' );
    ?>
    <input type='text' name='widgetbird_widget_id' value='<?php echo esc_attr( $widget_id ); ?>' style='width: 100%; max-width: 500px;'>
    <?php
}

// Settings section callback
function widgetbird_settings_section_callback() {
    echo esc_html__( 'Enter your Widgetbird Widget ID below. ', 'widgetbird' );
    echo wp_kses( __( 'Need help? Check out our', 'widgetbird') . ' <a href="https://widgetbird.com/support/how-to-install-widget-on-wordpress/" target="_blank">' . __( 'support article', 'widgetbird' ) . '</a>.', array(
		'a' => array(
			'href' => array(),
			'target' => array()
		),
	) );
}

// Options page
function widgetbird_options_page() {
    ?>
    <form action='options.php' method='post'>
        <?php
            settings_fields( 'widgetbird' );
            do_settings_sections( 'widgetbird' );
            submit_button();
        ?>
    </form>
    <?php
}

function widgetbird_check_settings_updated() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] && isset($_GET['page']) && $_GET['page'] === 'widgetbird') {
        widgetbird_clear_cache();
    }
}
add_action('admin_init', 'widgetbird_check_settings_updated');

function widgetbird_clear_cache() {
    // W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
    }

    // WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
    }

    // WP Rocket
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
    }

    // LiteSpeed Cache
    if (class_exists('LiteSpeed_Cache_API')) {
        LiteSpeed_Cache_API::purge_all();
    }

    // Cache Enabler
    if (function_exists('cache_enabler_clear_complete_cache')) {
        cache_enabler_clear_complete_cache();
    }

    // Comet Cache
    if (class_exists('comet_cache')) {
        comet_cache::clear();
    }

    // WP Fastest Cache
    if (class_exists('WpFastestCache')) {
        $wpFastestCache = new WpFastestCache();
        $wpFastestCache->deleteCache(true);
    }

    // Hummingbird
    if (function_exists('hummingbird_clear_cache')) {
        hummingbird_clear_cache();
    }
}
