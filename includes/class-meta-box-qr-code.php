<?php

class MetaBoxQRCode {
    // Реєстрація метабоксу
    public function register() {
        add_meta_box(
            'worker_qr_code',
            'QR-код',
            [$this, 'display'],
            'worker',
            'side',
            'low'
        );
    }

    // Виведення метабоксу
    public function display($post) {
        // Отримуємо метадані (URL та зображення QR-коду)
        $qr_url = get_post_meta($post->ID, '_worker_qr_url', true);
        $qr_code_image_id = get_post_meta($post->ID, '_worker_qr_image_id', true);
        $qr_code_image_url = $qr_code_image_id ? wp_get_attachment_url($qr_code_image_id) : '';
        ?>
        <div class="qr-code-metabox-content">
            <p>
                <label for="worker_qr_url"><strong>URL-адреса</strong></label>
                <input type="url" id="worker_qr_url" name="worker_qr_url" class="full-width" value="<?php echo esc_attr($qr_url); ?>" />
            </p>
            
            <p>
                <button type="button" id="generate_qr_code" class="button button-primary full-width-btn" data-post-id="<?php echo $post->ID; ?>">Згенерувати QR-код</button>
            </p>
            <!-- <p>
                <button type="button" id="upload_qr_code" class="button full-width-btn">Завантажити готовий QR-код</button>
            </p> -->

            <?php if ($qr_code_image_url): ?>
                <div id="qr_code_image" class="qr-code-image-preview">
                    <img src="<?php echo esc_url($qr_code_image_url); ?>" alt="QR Code" style="max-width:100%;" />
                </div>
                <p>
                    <button type="button" id="delete_qr_code" class="button button-secondary full-width-btn" data-post-id="<?php echo $post->ID; ?>">Видалити QR-код</button>
                </p>
            <?php endif; ?>
        </div>
        <?php
    }

    // Збереження URL
    public function save($post_id) {
        if (isset($_POST['worker_qr_url'])) {
            update_post_meta($post_id, '_worker_qr_url', esc_url($_POST['worker_qr_url']));
        }
    }
}

// Збереження URL при збереженні посту
add_action('save_post', [new MetaBoxQRCode(), 'save']);
