<?php

function theme_enqueue_styles() {
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'avada-stylesheet' ) );
}
add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );

function avada_lang_setup() {
	$lang = get_stylesheet_directory() . '/languages';
	load_child_theme_textdomain( 'Avada', $lang );
}
add_action( 'after_setup_theme', 'avada_lang_setup' );

function custom_footer_logo() {

  register_sidebar( array(
    'name'          => 'Footer Logo',
    'id'            => 'footer_logo',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );

}
add_action( 'widgets_init', 'custom_footer_logo' );

function custom_footer_quote() {

  register_sidebar( array(
    'name'          => 'Footer Quote',
    'id'            => 'footer_quote',
    'before_widget' => '<section class="fusion-footer-widget-column widget widget_text">',
    'after_widget'  => '</section>',
    'before_title'  => '<h4 class="widget-title">',
    'after_title'   => '</h4>',
  ) );

}
add_action( 'widgets_init', 'custom_footer_quote' );