<?php

function enqueue_font_awesome() {
    wp_enqueue_style('font-awesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');
}
add_action('wp_enqueue_scripts', 'enqueue_font_awesome');



function my_custom_theme_setup() {
    // Register menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'my-custom-theme'),
    ));
}
add_action('after_setup_theme', 'my_custom_theme_setup');

function my_custom_theme_assets() {
    // Enqueue styles
    wp_enqueue_style('style', get_stylesheet_uri());
}
add_action('wp_enqueue_scripts', 'my_custom_theme_assets');



// Add custom class to menu items with submenus
function add_menu_item_classes($classes, $item, $args, $depth) {
    if (in_array('menu-item-has-children', $classes)) {
        $classes[] = 'has-children';
    }
    return $classes;
}
add_filter('nav_menu_css_class', 'add_menu_item_classes', 10, 4);

class Walker_Nav_Menu_Custom extends Walker_Nav_Menu {
    // Start each menu item
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args, $depth));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';

        $output .= '<li' . $class_names . '>';

        $attributes = !empty($item->url) ? ' href="' . esc_url($item->url) . '"' : '';

        $arrow = '';
        if (in_array('menu-item-has-children', $classes)) {
            $arrow = $depth === 0 ? ' ▼' : ' ▶'; // Dropdown or right arrow
        }

        $item_output = $args->before;
        $item_output .= '<a' . $attributes . '>';
        $item_output .= $args->link_before . apply_filters('the_title', $item->title, $item->ID) . $args->link_after;
        $item_output .= $arrow;
        $item_output .= '</a>';
        $item_output .= $args->after;

        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
}
