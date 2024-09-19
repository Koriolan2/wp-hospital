<?php

class MetaBoxEducationInfo {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'register']);
        add_action('save_post', [$this, 'save']);
    }

    // Реєстрація метабоксу
    public function register() {
        add_meta_box(
            'worker_education_info',
            'Дані про освіту',
            [$this, 'display'],
            'worker', // Користувацький тип запису «Працівник»
            'normal',
            'high'
        );
    }

    // Виведення метабоксу
    public function display($post) {
        // Отримуємо значення метаданих
        $education = get_post_meta($post->ID, '_worker_education', true);
        ?>
        <table class="form-table">
            <tr>
                <th><label for="worker_education">Освіта</label></th>
                <td>
                    <input type="text" id="worker_education" name="worker_education" value="<?php echo esc_attr($education); ?>" class="regular-text" style="width: 100%;" />
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

        if (isset($_POST['worker_education'])) {
            update_post_meta($post_id, '_worker_education', sanitize_text_field($_POST['worker_education']));
        }
    }
}

// Створюємо екземпляр класу
new MetaBoxEducationInfo();
