<?php
/*
  Plugin Name: Map-Plugin
  Plugin URI:  http://localhost/
  Description: A plugin to build any type of forms
  Version:     1.0.0
  Author:     Me
  Author URI:  http://localhost
  License:     GPL2
  License URI: https://www.gnu.org/licenses/gpl-2.0.html
  Domain Path: /languages
  Text Domain: Map-Plugin
 */

if ( ! class_exists( 'Map-Plugin' ) ) {
	class Map_Plugin {
		function __construct() {
			add_action( 'init', array( $this, 'plugin_initialization' ) );
			add_action( 'admin_menu', array( $this, 'menu' ) );
			add_filter( 'manage_map_posts_columns',
				array( $this, 'custome_column' ) );
			add_action( 'manage_map_posts_custom_column',
				array( $this, 'custome_column_content' ), 10, 2 );
			add_shortcode( 'map_shortcode',
				array( $this, 'shortcode_generator' ) );
			add_action( 'add_meta_boxes', array( $this, 'map_display' ) );
			add_action( 'save_post_map', array( $this, 'save_post_data' ) );
			add_action( 'plugins_loaded', array( $this, 'widget_class' ) );
		}

		function plugin_initialization() {
			$args = array(
				'public'             => true,
				'label'              => _( 'Map' ),
				'description'        => _( 'This is Map post type' ),
				'menu-icon'          => 'dashicons-location',
				'publicly_queryable' => true,
				'show_ui'            => true,
				'show_in_menu'       => true,
				'query_var'          => true,
				'rewrite'            => array( 'slug' => 'map' ),
				'capability_type'    => 'post',
				'has_archive'        => true,
				'hierarchical'       => false,
				'menu_position'      => null,
				'supports'           => array( 'title' ),
			);
			register_post_type( 'map', $args );
		}

		function sanitize_array( $array = array(), $sanitize_rule = array() ) {
			if ( ! is_array( $array ) || count( $array ) == 0 ) {
				return array();
			}

			foreach ( $array as $k => $v ) {
				if ( ! is_array( $v ) ) {

					$default_sanitize_rule = ( is_numeric( $k ) ) ? 'text'
						: 'html';
					$sanitize_type         = isset( $sanitize_rule[ $k ] )
						? $sanitize_rule[ $k ] : $default_sanitize_rule;
					$array[ $k ]           = $this->sanitize_value( $v,
						$sanitize_type );
				}
				if ( is_array( $v ) ) {
					$array[ $k ] = $this->sanitize_array( $v, $sanitize_rule );
				}
			}

			return $array;
		}

		function sanitize_value( $value = '', $sanitize_type = 'text' ) {
			switch ( $sanitize_type ) {
				case 'html':
					$allowed_html = wp_kses_allowed_html( 'post' );

					return wp_kses( $value, $allowed_html );
					break;
				default:
					return sanitize_text_field( $value );
					break;
			}
		}

		function map_display() {
			add_meta_box( 'map_display_section', _( 'Map Section' ),
				array( $this, 'map_display_setting' ) );
		}

		function map_display_setting() {
			$file_path = plugin_dir_path( __FIlE__ );
			// var_dump($file_path);die();
			include( $file_path . 'map-metabox-setting.php' );

		}

		function save_post_data( $post_id ) {
			if ( isset( $_POST['map_meta'] ) ) {
				global $post;
				if ( $post->post_type != 'map' ) {
					return;
				}
				update_post_meta( $post_id, 'map_meta',
					$this->sanitize_array( $_POST['map_meta'] ) );//santization is not form in $_POST have to write sanitize function.
			}

		}

		function custome_column( $defaults ) {
			unset( $default['title'] );
			unset( $default['date'] );
			$defaults['shortcode'] = 'Shortcode';

			return $defaults;
		}

		function custome_column_content( $columns, $post_id ) {
			$a = get_post_meta( $post_id, 'map_meta', false );
			//  var_dump($columns,$post_id);
			if ( $columns = 'shortcode' ) {
				$id = $post_id;
				// echo("<pre");
				// print_r($a);
				// echo("</pre>");
				include( plugin_dir_path( __FIlE__ )
				         . 'custome-column-content.php' );
			}
		}

		function shortcode_generator( $atts ) {
			if ( isset( $atts ) ) {
				$args = array(
					'post_type'      => 'map',
					'post_status'    => 'publish',
					'posts_per_page' => - 1,
					'p'              => $atts['id']
				);
				// var_dump($atts);
				// $v = new WP_Query($args);
				// var_dump($v);
				ob_start();
				include( plugin_dir_path( __FILE__ )
				         . 'map-frontend-content.php' );
				$data = ob_get_contents();
				ob_end_clean();

				return $data;
			}

		}

		function menu() {
			add_menu_page( 'Map', 'Map', 'manage_options', 'Map',
				array( $this, 'Map' ) );
		}

		function Map() {
			echo "hello";
		}

		public function widget_class() {


			include( plugin_dir_path( __FIlE__ )
			         . 'class-widget-maps.php' );
		}
	}

	$map_plugin_obj = new Map_Plugin();
}
