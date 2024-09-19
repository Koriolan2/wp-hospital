<?php

class CreatePostType {
    // Реєстрація користувацького типу запису "Працівники"
    public function register() {
        $labels = [
            'name' => 'Працівники',
            'singular_name' => 'Працівник',
            'menu_name' => 'Працівники',
            'add_new' => 'Додати нового',
            'add_new_item' => 'Додати нового працівника',
            'edit_item' => 'Редагувати працівника',
            'new_item' => 'Новий працівник',
            'view_item' => 'Переглянути працівника',
            'all_items' => 'Всі працівники',
            'search_items' => 'Шукати працівника',
            'not_found' => 'Не знайдено',
            'not_found_in_trash' => 'Не знайдено в кошику',
        ];

        $args = [
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true,
            'menu_icon' => 'dashicons-businessman', // Іконка працівника з набору Dashicons
        ];

        register_post_type('worker', $args);
    }

    // Додавання нової колонки в таблицю записів
    public function add_custom_columns($columns) {
        // Додаємо нову колонку після заголовка
        $columns['completion_status'] = 'Ступінь заповненості';
        return $columns;
    }

    // Заповнення даними нової колонки
    public function fill_custom_column($column, $post_id) {
        if ($column === 'completion_status') {
            // Отримуємо метадані про спеціальність і категорію
            $specialty = get_post_meta($post_id, '_worker_specialty', true);
            $category = get_post_meta($post_id, '_worker_category', true);
            $has_thumbnail = has_post_thumbnail($post_id);

            // Обчислення ступеня заповненості
            $fields_filled = 0;
            $total_fields = 6; // Кількість полів для перевірки

            if (!empty($specialty)) $fields_filled++;
            if (!empty($category)) $fields_filled++;
            if ($has_thumbnail) $fields_filled++;

            // Підрахунок відсотків заповненості
            $completion_percentage = ($fields_filled / $total_fields) * 100;

            // Виведення відсотків заповненості
            echo round($completion_percentage) . '%';
        }
    }
}

// Додавання колонки та обробка даних
add_filter('manage_worker_posts_columns', [new CreatePostType(), 'add_custom_columns']);
add_action('manage_worker_posts_custom_column', [new CreatePostType(), 'fill_custom_column'], 10, 2);
