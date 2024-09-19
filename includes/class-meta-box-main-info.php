<?php

class MetaBoxMainInfo {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post', [$this, 'save']);
    }

    // Реєстрація метабоксу
    public function register() {
        add_meta_box(
            'worker_main_info',
            'Основна інформація',
            [$this, 'display'],
            'worker', // Користувацький тип запису «Працівник»
            'normal',
            'high'
        );
    }

    // Виведення метабоксу
    public function display($post) {
        // Отримуємо значення метаданих
        $speciality = get_post_meta($post->ID, '_worker_specialty', true);
        $category = get_post_meta($post->ID, '_worker_category', true);

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
                <th><label for="worker_specialty">Спеціальність</label></th>
                <td>
                    <input type="text" id="worker_specialty" name="worker_specialty" value="<?php echo esc_attr($speciality); ?>" class="regular-text" style="width: 100%;" />
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
        </table>
        <?php
    }

    // Збереження даних
    public function save($post_id) {
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['worker_specialty'])) {
            update_post_meta($post_id, '_worker_specialty', sanitize_text_field($_POST['worker_specialty']));
        }

        if (isset($_POST['worker_category'])) {
            update_post_meta($post_id, '_worker_category', sanitize_text_field($_POST['worker_category']));
        }
    }
}

// Створюємо екземпляр класу
new MetaBoxMainInfo();
