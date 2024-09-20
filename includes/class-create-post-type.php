<?php

class CreatePostType {
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
            'supports' => ['title', 'editor', 'thumbnail'],
            'show_in_rest' => true, // Важливо для REST API
            'rest_base' => 'workers', // База для REST API
            'menu_icon' => 'dashicons-businessman',
            'has_archive' => true, // Увімкнення архівів
            'rewrite' => ['slug' => 'workers'], // URL-структура
        ];

        register_post_type('worker', $args);
    }
}

add_action('init', [new CreatePostType(), 'register']);
