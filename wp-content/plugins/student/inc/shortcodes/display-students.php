<?php 


$args = array(
	'post_type' => 'student',
	'orderby' => 'title',
	'post_status' => 'publish',
	'order' => 'DESC',
);

if( ! empty( $attrs ) && ! empty( $attrs['id'] ) ){
		$id = (int) $attrs['id'];
		$args['p'] = $id;
}

$students_list = new WP_Query( $args );
global $post;
if ( $students_list->have_posts() ) : 
	while ( $students_list->have_posts() ) : 
		$students_list->the_post();
		$id = $post->ID;
		$meta = get_post_meta( $post->ID );
		?>
			<hr>
			<h5><strong><?php the_title() ?></strong></h5>
			<hr>
			<h5><strong>Year:</strong> <?php echo $meta['student_year'][0]; ?></h5>
			<h5><strong>Section:</strong> <?php echo $meta['student_section'][0]; ?></h5>
			<h5><strong>Address:</strong> <?php echo $meta['student_address'][0]; ?></h5>

		<?php
	endwhile;
endif;