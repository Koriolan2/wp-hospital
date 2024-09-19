<?php

// Переконайтеся, що клас існує лише один раз
if (!class_exists('QRCodeHandler')) {
    class QRCodeHandler {

        public function __construct() {
            // Реєструємо AJAX дії
            add_action('wp_ajax_generate_qr_code', [$this, 'generate_qr_code']);
            add_action('wp_ajax_delete_qr_code', [$this, 'delete_qr_code']);
        }

        // Генерація QR-коду
        public function generate_qr_code() {
            // Перевіряємо права користувача
            if (!current_user_can('edit_posts')) {
                wp_send_json_error(['message' => 'Недостатньо прав для виконання цієї операції.']);
                return;
            }

            // Перевіряємо наявність URL-адреси та ID поста
            if (empty($_POST['qr_url']) || empty($_POST['post_id'])) {
                wp_send_json_error(['message' => 'URL або ID посту відсутні.']);
                return;
            }

            $url = esc_url_raw($_POST['qr_url']);
            $post_id = intval($_POST['post_id']);

            // Отримуємо заголовок поста
            $post_title = get_the_title($post_id);

            // Параметри за замовчуванням для генерації QR-коду
            $size = '300x300'; // Розмір QR-коду
            $color = '003E7C'; // Колір пікселів
            $bgcolor = 'FFFFFF'; // Колір фону
            $format = 'svg'; // Формат файлу

            // Генеруємо URL для отримання QR-коду з вказаними параметрами
            $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?data=' . urlencode($url) . '&size=' . $size . '&color=' . $color . '&bgcolor=' . $bgcolor . '&format=' . $format;

            // Завантажуємо зображення з QR-сервера
            $response = wp_remote_get($qr_code_url);
            if (is_wp_error($response)) {
                wp_send_json_error(['message' => 'Не вдалося отримати QR-код.']);
                return;
            }

            // Отримуємо тіло зображення
            $image_body = wp_remote_retrieve_body($response);
            if (empty($image_body)) {
                wp_send_json_error(['message' => 'Порожнє зображення QR-коду.']);
                return;
            }

            // Назва файлу буде базуватися на заголовку поста
            $sanitized_title = sanitize_title($post_title);
            $filename = 'qr-code-' . $sanitized_title . '.svg';

            // Зберігаємо зображення у тимчасовий файл
            $upload = wp_upload_bits($filename, null, $image_body);
            if ($upload['error']) {
                wp_send_json_error(['message' => 'Не вдалося зберегти QR-код: ' . $upload['error']]);
                return;
            }

            // Додаємо зображення в медіабібліотеку
            $filetype = wp_check_filetype($upload['file'], null);
            $attachment = [
                'post_mime_type' => $filetype['type'],
                'post_title' => 'QR Code for ' . $post_title,
                'post_content' => '',
                'post_status' => 'inherit'
            ];

            $attachment_id = wp_insert_attachment($attachment, $upload['file'], $post_id);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
            wp_update_attachment_metadata($attachment_id, $attach_data);

            // Зберігаємо ID зображення як метадані посту
            update_post_meta($post_id, '_worker_qr_image_id', $attachment_id);

            // Повертаємо URL зображення як відповідь
            wp_send_json_success(['image_url' => wp_get_attachment_url($attachment_id)]);
        }

        // Видалення QR-коду
        public function delete_qr_code() {
            // Перевіряємо права користувача
            if (!current_user_can('edit_posts')) {
                wp_send_json_error(['message' => 'Недостатньо прав для виконання цієї операції.']);
                return;
            }

            // Перевіряємо наявність post_id
            if (!isset($_POST['post_id'])) {
                wp_send_json_error(['message' => 'ID посту не переданий.']);
                return;
            }

            $post_id = intval($_POST['post_id']);
            $qr_code_image_id = get_post_meta($post_id, '_worker_qr_image_id', true);

            if ($qr_code_image_id) {
                // Видаляємо зображення з медіабібліотеки
                wp_delete_attachment($qr_code_image_id, true);

                // Видаляємо метадані про QR-код
                delete_post_meta($post_id, '_worker_qr_image_id');

                // Повертаємо успішну відповідь
                wp_send_json_success(['message' => 'QR-код успішно видалено.']);
            } else {
                wp_send_json_error(['message' => 'QR-код не знайдено.']);
            }
        }
    }
}

// Створюємо екземпляр класу, щоб підключити всі функції
new QRCodeHandler();
