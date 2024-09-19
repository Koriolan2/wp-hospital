<?php
/**
 * Plugin Name: WP Hospital
 * Description: Плагін для міської лікарні: створює тип запису "Працівники" та додає метабокси для спеціальності і QR-коду.
 * Version: 2.0
 * Author: Yuriy Kozmin aka Yuriy Knysh
 * Plugin URI: https://github.com/Koriolan2/wp-hospital.git
 */

// Захист від прямого доступу до файлу
if (!defined('ABSPATH')) {
    exit;
}

// Підключаємо необхідні файли плагіну
require_once plugin_dir_path(__FILE__) . 'includes/class-create-post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-meta-box-main-info.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-meta-box-qr-code.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-qr-code-handler.php';

// Основний клас плагіну
class WPHospitalPlugin {
    public function __construct() {
        // Створення типу запису "Працівники"
        add_action('init', [$this, 'initialize_post_type']);

        // Реєстрація метабоксів
        add_action('add_meta_boxes', [$this, 'register_meta_boxes']);

        // Підключення JS для QR-коду
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);

        // Підключення стилів для метабоксів
        add_action('admin_enqueue_scripts', [$this, 'enqueue_styles']);
    }

    // Ініціалізація користувацького типу запису "Працівники"
    public function initialize_post_type() {
        $post_type = new CreatePostType();
        $post_type->register();
    }

    // Реєстрація метабоксів
    public function register_meta_boxes() {
        $meta_box_main_info = new MetaBoxMainInfo();
        $meta_box_main_info->register();

        $meta_box_qr_code = new MetaBoxQRCode();
        $meta_box_qr_code->register();
    }

    // Підключення JS-файлу з timestamp для уникнення кешування
    public function enqueue_scripts($hook) {
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_script('qr-code-script', plugin_dir_url(__FILE__) . 'assets/js/qr-code.js', [], time(), true);
        }
    }

    // Підключення стилів для метабоксів
    public function enqueue_styles($hook) {
        // Підключаємо стилі тільки на сторінках редагування записів
        if ('post.php' === $hook || 'post-new.php' === $hook) {
            wp_enqueue_style('wp-hospital-css', plugin_dir_url(__FILE__) . 'assets/css/wp-hospital.css');
        }
    }
}

// Запуск плагіну
new WPHospitalPlugin();
