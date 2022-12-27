<?php


/*
 * Class to register widget
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


function map_register_widget() {
	register_widget( 'Map_Widget' );
}

add_action( 'widgets_init', 'map_register_widget' );


/*
* Class to register with functionality
*/


class Map_Widget extends WP_Widget {
//Insert functions here


	function __construct() {
		parent::__construct(
// widget ID
			'map_widget',
// widget name
			__( 'Map  Widget', 'map_widget_domain' ),
			__( 'Map Display Widget', 'map_widget_domain' ),
// widget description
			array(
				'description' => __( 'Map   Widget',
					'map_widget_domain' ),
			)
		);
	}

	public function widget( $args, $instance ) {
		$title = apply_filters( 'widget_title', $instance['title'] );
		//	echo $args['before widget'];
//if title is present
		if ( ! empty ( $title ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
//output


		echo __( 'Greetings from Bishal GC!', 'map_widget_domain' );
		echo $args['after_widget'];

		echo '<br>';


		$args      = array(
			'post_type'      => 'map',
			'posts_per_page' => - 1
		);
		$the_query = new WP_Query( $args ); ?>

		<?php if ( $the_query->have_posts() ) : ?>

			<?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                <h2><?php the_title(); ?></h2>
                <h3><?php echo get_the_ID(); ?></h3>
				<?php
				$map_locations = get_post_meta( get_the_ID(), 'map_meta',
					false );
				var_dump( $map_locations[0]['latitude'] );
				var_dump( $map_locations[0]['longitute'] );
				$short_code = '   [map_shortcode id=' . ' ' . get_the_ID()
				              . ' lat =' . $map_locations['0']['latitude']
				              . ' log =' . $map_locations[0]['longitute'] . ']';

				// echo $short_code;


				echo do_shortcode( $short_code );
				?>

			<?php endwhile; ?>

			<?php wp_reset_postdata(); ?>

		<?php endif; ?>


		<?php

		echo do_shortcode( '[map_shortcode id=15 lat =28.15222475308911 log =82.31890660389732]' );


		//   var_dump($widget_data);


	}

	public function form( $instance ) {

		?>
        <span class="map-person-icon dashicons dashicons-admin-users"></span>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		$instance          = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) )
			? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

}