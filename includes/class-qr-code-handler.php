<?php

// Реєстрація обробника для AJAX-запиту
add_action('wp_ajax_generate_qr_code', 'generate_qr_code');

function generate_qr_code() {
    // Перевіряємо права користувача
    if (!current_user_can('edit_posts')) {
        wp_send_json_error(['message' => 'Недостатньо прав для виконання цієї операції.']);
        return;
    }

    // Перевіряємо наявність URL-адреси
    if (empty($_POST['qr_url']) || empty($_POST['post_id'])) {
        wp_send_json_error(['message' => 'URL або ID посту відсутні.']);
        return;
    }

    $url = esc_url_raw($_POST['qr_url']);
    $post_id = intval($_POST['post_id']);

    // Генеруємо URL для отримання QR-коду
    $qr_code_url = 'https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=' . urlencode($url);

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

    // Зберігаємо зображення у тимчасовий файл
    $upload = wp_upload_bits('qr-code-' . $post_id . '.png', null, $image_body);
    if ($upload['error']) {
        wp_send_json_error(['message' => 'Не вдалося зберегти QR-код: ' . $upload['error']]);
        return;
    }

    // Додаємо зображення в медіабібліотеку
    $filetype = wp_check_filetype($upload['file'], null);
    $attachment = [
        'post_mime_type' => $filetype['type'],
        'post_title' => 'QR Code for Post ' . $post_id,
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
