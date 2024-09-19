<?php

class MetaBoxMainInfo {
    // Реєстрація метабоксу
    public function register() {
        add_meta_box(
            'worker_main_info',
            'Основна інформація',
            [$this, 'display'],
            'worker',
            'normal',
            'high'
        );
    }

    // Виведення метабоксу у вигляді таблиці
    public function display($post) {
        // Отримуємо значення метаданих (якщо вони існують)
        $specialty = get_post_meta($post->ID, '_worker_specialty', true);
        $category = get_post_meta($post->ID, '_worker_category', true);
        
        // Виведення HTML з таблицею
        ?>
        <table class="form-table">
            <tbody>
                <tr>
                    <th>
                        <label for="worker_specialty">Спеціальність</label>
                    </th>
                    <td>
                        <!-- Додаємо клас 'full-width' для 100% ширини -->
                        <input type="text" id="worker_specialty" name="worker_specialty" class="regular-text full-width" value="<?php echo esc_attr($specialty); ?>" />
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="worker_category">Категорія</label>
                    </th>
                    <td>
                        <select id="worker_category" name="worker_category" class="full-width">
                            <option value="">Оберіть категорію</option>
                            <option value="first" <?php selected($category, 'first'); ?>>Перша категорія</option>
                            <option value="second" <?php selected($category, 'second'); ?>>Друга категорія</option>
                            <option value="highest" <?php selected($category, 'highest'); ?>>Вища категорія</option>
                        </select>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php
    }

    // Збереження даних метабоксу
    public function save($post_id) {
        // Зберігаємо спеціальність
        if (isset($_POST['worker_specialty'])) {
            update_post_meta($post_id, '_worker_specialty', sanitize_text_field($_POST['worker_specialty']));
        }

        // Зберігаємо категорію
        if (isset($_POST['worker_category'])) {
            update_post_meta($post_id, '_worker_category', sanitize_text_field($_POST['worker_category']));
        }
    }
}

// Додаємо дію для збереження метаданих при збереженні посту
add_action('save_post', [new MetaBoxMainInfo(), 'save']);
