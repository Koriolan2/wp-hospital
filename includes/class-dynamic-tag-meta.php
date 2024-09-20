<?php
class Custom_Meta_Field_Tag_Names extends \Elementor\Core\DynamicTags\Tag {

    public function get_name() {
        return 'custom_meta_field_with_names';
    }

    public function get_title() {
        return 'Мета поле з назвами';
    }

    public function get_group() {
        // Використовуємо строкове значення для групи мета-полів
        return 'post-meta';
    }

    public function get_categories() {
        return [ \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY ];
    }

    protected function register_controls() {
        $this->add_control(
            'meta_key',
            [
                'label' => 'Виберіть поле',
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_meta_fields_with_names(),
            ]
        );
    }

    // Функція для отримання списку мета-полів з назвами
    private function get_meta_fields_with_names() {
        return [
            '_worker_education' => 'Освіта',
            '_worker_specialty' => 'Спеціальність',
            '_worker_category' => 'Категорія',
            '_worker_qr_url' => 'URL для QR-коду',
            '_worker_qr_image_id' => 'QR-код (ID зображення)',
        ];
    }

    public function render() {
        $meta_key = $this->get_settings('meta_key');
        if ( empty( $meta_key ) ) {
            return;
        }

        $meta_value = get_post_meta( get_the_ID(), $meta_key, true );

        if ( is_array( $meta_value ) ) {
            $meta_value = implode( ', ', $meta_value );
        }

        echo wp_kses_post( $meta_value );
    }
}

// Реєстрація динамічного тегу
function register_custom_meta_field_with_names_tag( $dynamic_tags ) {
    $dynamic_tags->register( new Custom_Meta_Field_Tag_Names() );
}
add_action( 'elementor/dynamic_tags/register', 'register_custom_meta_field_with_names_tag' );
