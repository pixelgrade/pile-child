<?php
/**
 * Pile Child functions and definitions
 *
 * Bellow you will find several ways to tackle the enqueue of static resources/files
 * It depends on the amount of customization you want to do
 * If you either wish to simply overwrite/add some CSS rules or JS code
 * Or if you want to replace certain files from the parent with your own (like style.css or main.js)
 *
 * @package PileChild
 */




/**
 * Setup Pile Child Theme's textdomain.
 *
 * Declare textdomain for this child theme.
 * Translations can be filed in the /languages/ directory.
 */
function pile_child_theme_setup() {
	load_child_theme_textdomain( 'pile-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'pile_child_theme_setup' );





/**
 *
 * 1. Add a Child Theme "style.css" file
 * ----------------------------------------------------------------------------
 *
 * If you want to add static resources files from the child theme, use the
 * example function written below.
 *
 */

function pile_child_enqueue_styles() {
	$theme = wp_get_theme();
	// use the parent version for cachebusting
	$parent = $theme->parent();

	/*
	 * First we need to enqueue the parent style since it won't be automatically by the child theme
	 */

	//we need the same logic for the dependencies as in the parent
	$main_style_deps = array( 'wp-mediaelement' );
	//only enqueue the de default font if Customify is not present
	if ( ! class_exists( 'PixCustomifyPlugin' ) ) {
		wp_enqueue_style( 'pile-fonts-trueno', get_template_directory_uri() . '/assets/fonts/trueno/stylesheet.css' );
		$main_style_deps[] = 'pile-fonts-trueno';
	} else {
		// we will load the Trueno font only if it is selected in one of the Customify's fields
		$fonts = array( 'google_titles_font', 'google_descriptions_font', 'google_nav_font', 'google_body_font' );
		foreach ( $fonts as $font ) {
			$val = pile_option( $font );
			if ( ! empty( $val ) ) {

				if ( is_string( $val ) ) {
					$val = json_decode(  wp_unslash( PixCustomifyPlugin::decodeURIComponent($val) ), true );
				}

				if ( ! empty( $val ) && is_array( $val ) && in_array( 'Trueno', $val ) ) {
					wp_enqueue_style( 'pile-fonts-trueno', get_template_directory_uri() . '/assets/fonts/trueno/stylesheet.css' );
					break;
				}
			}
		}
	}

	if ( !is_rtl() ) {
		wp_enqueue_style( 'pile-main-style', get_template_directory_uri() .'/style.css', $main_style_deps, $parent->get( 'Version' ) );
	} else {
		wp_enqueue_style( 'pile-main-style', get_template_directory_uri() . '/rtl.css', $main_style_deps, $parent->get( 'Version' ) );
	}

	/*
	 * Now for the child theme styles.
	 * Here we are adding the child style.css while still retaining
	 * all of the parents assets (style.css, JS files, etc)
	 */
	wp_enqueue_style( 'pile-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array('pile-main-style') //make sure the the child's style.css comes after the parents so you can overwrite rules
	);
}
add_action( 'wp_enqueue_scripts', 'pile_child_enqueue_styles' );





/**
 *
 * 2. Overwrite Static Resources (eg. style.css or main.js)
 * ----------------------------------------------------------------------------
 *
 * If you want to overwrite static resources files from the parent theme
 * and use only the ones from the Child Theme, this is the way to do it.
 *
 */


/*

function pile_child_overwrite_files() {

	// 1. The "main.js" file
	//
	// Let's assume you want to completely overwrite the "main.js" file from the parent

	// First you will have to make sure the parent's file is not loaded
	// See the parent's function.php -> the pile_scripts_styles() function
	// for details like resources names

		wp_dequeue_script( 'pile-main-scripts' );


	// We will add the main.js from the child theme (located in assets/js/main.js)
	// with the same dependecies as the main.js in the parent
	// This is not required, but I assume you are not modifying that much :)

		wp_enqueue_script( 'pile-child-scripts',
			get_stylesheet_directory_uri() . '/assets/js/main.js',
			array( 'wp-mediaelement', 'masonry' ),
			'1.0.0', true );



	// 2. The "style.css" file
	//
	// First, remove the parent style files
	// see the parent's function.php -> the hive_scripts_styles() function for details like resources names

		wp_dequeue_style( 'pile-main-style' );


	// Now you can add your own, modified version of the "style.css" file

		wp_enqueue_style( 'pile-child-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'wp-mediaelement' ),
			'1.0.0'
		);
}

// Load the files from the function mentioned above:

	add_action( 'wp_enqueue_scripts', 'pile_child_overwrite_files', 11 );

// Notes:
// The 11 priority parameter is need so we do this after the function in the parent (higher number means latter execution).
// This way there is something to dequeue
// The default priority of any action is 10

*/