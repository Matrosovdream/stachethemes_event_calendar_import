<?php
/*
    Plugin Name: Stachethemes Event Calendar Import data
    Plugin URI: 
    Description: Addon
    Author: Not your business
    Author URI: 
    Version: 1.0.0
*/

require_once( 'classes/import.php' );
require_once( 'inc/events.php' );
require_once( 'inc/custom_data.php' );


add_action( 'wp_enqueue_scripts', 'theme_name_scripts' );
// add_action('wp_print_styles', 'theme_name_scripts'); // можно использовать этот хук он более поздний
function theme_name_scripts() {
	wp_enqueue_style( 'style-name', plugins_url('style.css?t='.time(), __FILE__) );
	//wp_enqueue_script( 'newscript1', 'https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js' );
	wp_enqueue_script( 'newscript2', plugins_url('js/scripts.js?t='.time(), __FILE__) );
}









