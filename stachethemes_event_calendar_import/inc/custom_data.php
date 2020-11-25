<?php
function cptui_register_my_cpts_calendar_import() {

	/**
	 * Post Type: Calendar Import.
	 */

	$labels = [
		"name" => __( "Calendar Import", "eduexpert" ),
		"singular_name" => __( "Calendar Import", "eduexpert" ),
	];

	$args = [
		"label" => __( "Calendar Import", "eduexpert" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "calendar_import", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
	];

	register_post_type( "calendar_import", $args );
}

add_action( 'init', 'cptui_register_my_cpts_calendar_import' );

?>