<?php
add_action('init', 'init_event_calendar');
function init_event_calendar() {
	
	$IC = new ImportCalendar;

	if( $_GET['action'] == 'update_calendars' ) {
		
		$IC->removeAllEvents();
		$IC->UpdateAllCalendars();
		
		die();
		
	}

}


add_action('save_post', 'save_event_calendar');
function save_event_calendar( $post_id ) {
	
	$post = get_post( $post_id );
	
	if( $post->post_type == 'calendar_import' ) {
		
		$IC = new ImportCalendar;
		
		$IC->UpdateCalendarByID( $post->ID );
		
	}
	
	//print_r($post);
	//die();
	
}


// it inserts the entry in the admin menu
/*
add_action('admin_menu', 'empty_plugin_create_menu_entry');

// creating the menu entries
function empty_plugin_create_menu_entry() {
	
	// icon image path that will appear in the menu
	$icon = plugins_url('/images/empy-plugin-icon-16.png', __FILE__);
	
	
	// adding the main manu entry
	add_menu_page('Empty Plugin', 'Empty Plugin', 'edit_posts', 'main-page-empty-plugin', 'empty_plugin_show_main_page', $icon);
	// adding the sub menu entry
	add_submenu_page( 'main-page-empty-plugin', 'Add New', 'Add New', 'edit_posts', 'add-edit-empty-plugin', 'empty_plugin_add_another_page' );
}

// function triggered in add_menu_page
function empty_plugin_show_main_page() {
	include('main-page-empty-plugin.php');
}

// function triggered in add_submenu_page
function empty_plugin_add_another_page() {
	include('another-page-empty-plugin.php');
}
*/
?>