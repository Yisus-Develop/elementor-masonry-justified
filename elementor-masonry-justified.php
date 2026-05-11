<?php
/**
 * Plugin Name:       EWEB - Elementor Masonry Justified
 * Description:       Justified rows and masonry gallery widget for Elementor with responsive lightbox support.
 * Version:           1.2.1
 * Author:            Yisus_Dev
 * Author URI:        https://github.com/Yisus-Develop
 * Plugin URI:        https://github.com/Yisus-Develop/elementor-masonry-justified
 * License:           GPL v2 or later
 * Requires at least: 6.0
 * Requires PHP:      8.1+
 * Tested up to:      6.8
 * Text Domain:       emj
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'EMJ_VERSION', '1.2.1' );
define( 'EMJ_FILE', __FILE__ );
define( 'EMJ_DIR', plugin_dir_path( __FILE__ ) );
define( 'EMJ_URL', plugin_dir_url( __FILE__ ) );
define( 'EMJ_GITHUB_USER', 'Yisus-Develop' );
define( 'EMJ_GITHUB_REPO', 'elementor-masonry-justified' );

require_once EMJ_DIR . 'includes/class-eweb-github-updater.php';

function emj_bootstrap(): void {
    if ( ! did_action( 'elementor/loaded' ) || ! class_exists( '\\Elementor\\Plugin' ) ) {
        add_action( 'admin_notices', 'emj_missing_elementor_notice' );
        return;
    }

    add_action( 'elementor/widgets/register', 'emj_register_widget' );
    add_action( 'elementor/frontend/after_register_scripts', 'emj_register_assets' );
    add_action( 'elementor/editor/before_enqueue_scripts', 'emj_enqueue_editor_assets' );
}
add_action( 'plugins_loaded', 'emj_bootstrap' );

function emj_missing_elementor_notice(): void {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        return;
    }

    echo '<div class="notice notice-error"><p><strong>EWEB - Elementor Masonry Justified</strong> requires Elementor to be installed and active.</p></div>';
}

function emj_register_widget( $widgets_manager ): void {
    require_once EMJ_DIR . 'widgets/class-masonry-justified.php';
    $widgets_manager->register( new \EMJ_Justified_Widget() );
}

function emj_register_assets(): void {
    wp_register_style( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css', array(), '11.0.0' );
    wp_register_script( 'swiper', 'https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js', array(), '11.0.0', true );

    wp_register_style( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css', array(), '3.3.0' );
    wp_register_script( 'glightbox', 'https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js', array(), '3.3.0', true );

    wp_register_style( 'emj-justified', EMJ_URL . 'assets/css/justified.css', array( 'swiper', 'glightbox' ), EMJ_VERSION );
    wp_register_script( 'emj-justified', EMJ_URL . 'assets/js/justified.js', array( 'swiper', 'glightbox' ), EMJ_VERSION, true );
}

function emj_enqueue_editor_assets(): void {
    wp_enqueue_style( 'emj-justified' );
    wp_enqueue_script( 'emj-justified' );
}

add_action(
    'wp_enqueue_scripts',
    static function (): void {
        wp_deregister_script( 'elementor-frontend-modules-lightbox' );
    },
    20
);

if ( class_exists( 'EWEB_GitHub_Updater' ) ) {
    new EWEB_GitHub_Updater( EMJ_FILE, EMJ_GITHUB_USER, EMJ_GITHUB_REPO );
}
