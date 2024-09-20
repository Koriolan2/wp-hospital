<?php

class WorkerStatistics {
    
    // Додавання нових колонок у таблицю записів
    public function add_custom_columns($columns) {
        $columns['basic_info'] = 'Основне';
        $columns['education_info'] = 'Освіта';
        $columns['qr_info'] = 'Запис он-лайн';
        return $columns;
    }

    // Заповнення нових колонок
    public function fill_custom_column($column, $post_id) {
        switch ($column) {
            case 'basic_info':
                $this->display_basic_info($post_id);
                break;
            case 'education_info':
                $this->display_education_info($post_id);
                break;
            case 'qr_info':
                $this->display_qr_info($post_id);
                break;
        }
    }

    // Відображення статистики для "Основне"
    private function display_basic_info($post_id) {
        $specialty = get_post_meta($post_id, '_worker_specialty', true);
        $category = get_post_meta($post_id, '_worker_category', true);
        $has_thumbnail = has_post_thumbnail($post_id);
        $content = get_post_field('post_content', $post_id); // Основний текст поста
        $title = get_the_title($post_id); // Заголовок поста

        $fields_filled = 0;
        $total_fields = 5; // Заголовок, основний текст, зображення, спеціальність, категорія

        if (!empty($title)) $fields_filled++;
        if (!empty($specialty)) $fields_filled++;
        if (!empty($category)) $fields_filled++;
        if ($has_thumbnail) $fields_filled++;
        if (!empty($content) && trim($content) !== '') $fields_filled++;

        $completion_percentage = ($fields_filled / $total_fields) * 100;

        // Відображення кольорового кружечка
        $this->display_colored_circle($completion_percentage);
    }

    // Відображення статистики для "Освіта"
    private function display_education_info($post_id) {
        $education = get_post_meta($post_id, '_worker_education', true);
        $courses = get_post_meta($post_id, '_worker_courses', true);

        $fields_filled = 0;
        $total_fields = 2; // Освіта та курси

        if (!empty($education)) $fields_filled++;
        if (!empty($courses) && is_array($courses) && count($courses) > 0) $fields_filled++;

        $completion_percentage = ($fields_filled / $total_fields) * 100;

        // Відображення кольорового кружечка
        $this->display_colored_circle($completion_percentage);
    }

    // Відображення статистики для "Запис он-лайн" (QR-код)
    private function display_qr_info($post_id) {
        $qr_url = get_post_meta($post_id, '_worker_qr_url', true);
        $qr_code_image_id = get_post_meta($post_id, '_worker_qr_image_id', true);

        $fields_filled = 0;
        $total_fields = 2; // URL для QR-коду та наявність згенерованого зображення

        if (!empty($qr_url)) $fields_filled++;
        if (!empty($qr_code_image_id) && wp_get_attachment_url($qr_code_image_id)) $fields_filled++;

        $completion_percentage = ($fields_filled / $total_fields) * 100;

        // Відображення кольорового кружечка
        $this->display_colored_circle($completion_percentage);
    }

    // Функція для відображення кольорового кружечка на основі відсотків заповнення
    private function display_colored_circle($percentage) {
        $color = 'red'; // За замовчуванням червоний

        if ($percentage >= 75) {
            $color = '#00aa00'; // Все чудово
        } elseif ($percentage >= 50) {
            $color = 'yellow'; // Добре
        } elseif ($percentage >= 25) {
            $color = 'orange'; // Задовільно
        }

        echo '<span class="completion-circle" style="background-color: ' . $color . ';"></span>';
    }

    // Додавання можливості сортування колонок
    public function make_columns_sortable($sortable_columns) {
        $sortable_columns['basic_info'] = 'basic_info';
        $sortable_columns['education_info'] = 'education_info';
        $sortable_columns['qr_info'] = 'qr_info';
        return $sortable_columns;
    }

    // Обробка сортування
    public function sort_columns($query) {
        if (!is_admin() || !$query->is_main_query()) {
            return;
        }

        // Перевіряємо, чи сортуємо по колонці "Основне"
        if ('basic_info' === $query->get('orderby')) {
            $query->set('meta_key', '_worker_specialty');
            $query->set('orderby', 'meta_value');
        }

        // Перевіряємо, чи сортуємо по колонці "Освіта"
        if ('education_info' === $query->get('orderby')) {
            $query->set('meta_key', '_worker_education');
            $query->set('orderby', 'meta_value');
        }

        // Перевіряємо, чи сортуємо по колонці "Запис он-лайн"
        if ('qr_info' === $query->get('orderby')) {
            $query->set('meta_key', '_worker_qr_url');
            $query->set('orderby', 'meta_value');
        }
    }
}

// Реєструємо нові колонки, сортування та обробку даних
add_filter('manage_worker_posts_columns', [new WorkerStatistics(), 'add_custom_columns']);
add_action('manage_worker_posts_custom_column', [new WorkerStatistics(), 'fill_custom_column'], 10, 2);

// Додаємо можливість сортування
add_filter('manage_edit-worker_sortable_columns', [new WorkerStatistics(), 'make_columns_sortable']);
add_action('pre_get_posts', [new WorkerStatistics(), 'sort_columns']);
