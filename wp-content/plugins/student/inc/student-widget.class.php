<?php 

class Student_Widget extends WP_Widget{
	
	public function __construct(){
		parent::__construct(
			'student_widget' ,
			__( 'Student Widget', 'std' ),
			array( 'classname' => 'student_widget', 'description' => __( 'Display studnet list' ), 'std' )
		);
	}

	public function widget ( $args, $instance ) {
        extract( $args );
        $student_id = apply_filters( 'widget_student_id', $instance['student_id'] );
        $out = '<h4><strong>Students List:</strong></h4>';
    	echo $before_widget;
    	?>
			<div>
				<?php echo $out; ?>
				<?php echo do_shortcode( '[display_students id='. $student_id . ']'); ?>
			</div>

    	<?php
		echo $after_widget;
    }

    private function get_students_list(){
    	$args = array(
			'post_type' => 'student',
			'orderby' => 'title',
			'post_status' => 'publish',
			'order' => 'DESC',
		);

		return new WP_Query( $args );
    }

    public function form( $instance ) {

    	global $post;
    	$instance_defaults = array(
    		'student_id' => '',
		);
		$instance = wp_parse_args( $instance, $instance_defaults );
		$student_id = esc_attr( $instance['student_id'] );

		$students_list = $this->get_students_list();
	?>

		<p>
			<label for="<?php echo $this->get_field_id('student_id'); ?>"><?php _e( "Student:", 'std'); ?></label> 
			<?php if( $students_list -> have_posts() ) :?>
				
				<select name="<?php echo $this->get_field_name('student_id'); ?>" id="<?php echo $this->get_field_id('student_id'); ?>" class="widefat">
					<option value=''> All </option>

					<?php while( $students_list -> have_posts() ) : 
						$students_list -> the_post();
					?>
						<option value="<?php echo $post->ID ?>"<?php selected( $instance['student_id'], $post->ID ); ?>><?php _e( $post->post_title, 'std' ); ?></option>
					
					<?php endwhile; ?>
				</select>

			<?php endif; ?>
		</p>

	<?php
    }

}

register_widget('Student_Widget');