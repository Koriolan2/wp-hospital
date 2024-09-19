<?php

class MetaBoxEducationInfo {
    public function __construct() {
        // Реєстрація метабоксу
        add_action('add_meta_boxes', [$this, 'register']);
        // Збереження даних при збереженні посту
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
        $courses = get_post_meta($post->ID, '_worker_courses', true);

        // Якщо курсів немає, створюємо порожній масив
        if (empty($courses)) {
            $courses = [];
        }

        ?>
        <table class="form-table">
            <tr>
                <th><label for="worker_education">Освіта</label></th>
                <td>
                    <input type="text" id="worker_education" name="worker_education" value="<?php echo esc_attr($education); ?>" class="regular-text" style="width: 100%;" />
                </td>
            </tr>
        </table>

        <h4>Курси підвищення кваліфікації</h4>
        <table id="qualification_courses" class="widefat striped" style="width: 100%;">
            <thead>
                <tr>
                    <th>Назва навчального закладу</th>
                    <th>Назва курсу</th>
                    <th style="width:80px">Дії</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($courses)) : ?>
                    <?php foreach ($courses as $index => $course) : ?>
                        <tr>
                            <td><input type="text" name="worker_courses[<?php echo $index; ?>][institution]" value="<?php echo esc_attr($course['institution']); ?>" style="width: 100%;" /></td>
                            <td><input type="text" name="worker_courses[<?php echo $index; ?>][course]" value="<?php echo esc_attr($course['course']); ?>" style="width: 100%;" /></td>
                            <td>
                                <span class="remove-row dashicons dashicons-remove"></span>
                                <span class="add-row dashicons dashicons-plus-alt"></span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <p>
            <button type="button" id="add_row_outside" class="button">Додати новий рядок</button>
        </p>

        <?php
    }

    // Збереження даних
    public function save($post_id) {
        // Перевіряємо, чи має користувач право редагувати пост
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Перевіряємо, чи це не автозбереження
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Збереження поля "Освіта"
        if (isset($_POST['worker_education'])) {
            update_post_meta($post_id, '_worker_education', sanitize_text_field($_POST['worker_education']));
        }

        // Збереження курсів (тільки тих, які є у формі)
        if (isset($_POST['worker_courses'])) {
            $courses = array_map(function($course) {
                return [
                    'institution' => sanitize_text_field($course['institution']),
                    'course' => sanitize_text_field($course['course'])
                ];
            }, $_POST['worker_courses']);

            update_post_meta($post_id, '_worker_courses', $courses);
        } else {
            // Якщо курсів немає, видаляємо мета-дані
            delete_post_meta($post_id, '_worker_courses');
        }
    }
}

// Створюємо екземпляр класу
new MetaBoxEducationInfo();
