<?php

class MetaBoxMainInfo {
    public function __construct() {
        // Реєстрація метабоксу
        add_action('add_meta_boxes', [$this, 'register']);
        // Збереження даних
        add_action('save_post', [$this, 'save']);
    }

    // Реєстрація метабоксу
    public function register() {
        add_meta_box(
            'worker_main_info',
            'Основна інформація',
            [$this, 'display'],
            'worker', // Замініть на ваш користувацький тип запису, якщо потрібно
            'normal',
            'high'
        );
    }

    // Виведення метабоксу
    public function display($post) {
        // Отримуємо значення збережених метаданих
        $speciality = get_post_meta($post->ID, '_worker_speciality', true);
        $category = get_post_meta($post->ID, '_worker_category', true);
        $education = get_post_meta($post->ID, '_worker_education', true); // Нове поле освіти

        // Масив категорій
        $categories = [
            'default' => 'Оберіть категорію',
            'first' => 'Перша категорія',
            'second' => 'Друга категорія',
            'higher' => 'Вища категорія',
        ];

        ?>
        <table class="form-table">
            <tr>
                <th><label for="worker_speciality">Спеціальність</label></th>
                <td>
                    <input type="text" id="worker_speciality" name="worker_speciality" value="<?php echo esc_attr($speciality); ?>" class="regular-text" style="width: 100%;" />
                </td>
            </tr>
            <tr>
                <th><label for="worker_category">Категорія</label></th>
                <td>
                    <select id="worker_category" name="worker_category" class="regular-text" style="width: 100%;">
                        <?php foreach ($categories as $value => $label): ?>
                            <option value="<?php echo esc_attr($value); ?>" <?php selected($category, $value); ?>><?php echo esc_html($label); ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="worker_education">Освіта</label></th>
                <td>
                    <input type="text" id="worker_education" name="worker_education" value="<?php echo esc_attr($education); ?>" class="regular-text" style="width: 100%;" />
                </td>
            </tr>
        </table>
        <?php
    }

    // Збереження метаданих
    public function save($post_id) {
        // Перевіряємо права користувача на редагування посту
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Перевірка на автозбереження
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Перевіряємо та зберігаємо спеціальність
        if (isset($_POST['worker_speciality'])) {
            update_post_meta($post_id, '_worker_speciality', sanitize_text_field($_POST['worker_speciality']));
        }

        // Перевіряємо та зберігаємо категорію
        if (isset($_POST['worker_category'])) {
            update_post_meta($post_id, '_worker_category', sanitize_text_field($_POST['worker_category']));
        }

        // Перевіряємо та зберігаємо освіту
        if (isset($_POST['worker_education'])) {
            update_post_meta($post_id, '_worker_education', sanitize_text_field($_POST['worker_education']));
        }
    }
}

// Створюємо екземпляр класу
new MetaBoxMainInfo();
