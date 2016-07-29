<?php 
/*
Plugin Name: Student List Table
Plugin URI: localhost
Description: WP List Table for student
Version: 0.1
Author: Moretti Georgiev
*/


if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
 

class Student_List_Table extends WP_List_Table {
	private $student_data;

	function get_columns() {
	   return $columns= array(
	      'student_id'=>__( 'ID' ),
	      'student_name'=>__( 'Name' ),
	      'student_year'=>__( 'Year' ),
	      'student_section'=>__( 'Section' ),
	      'student_address'=>__( 'Address' )
	   );
	}

	function prepare_items() {
		$columns = $this->get_columns();
		$this->_column_headers = array($columns);

		$args = array(
			'sort_by' => 'ID',
			'post_type' => 'student',
			'post_status' => 'publish',
			'posts_per_page' => -1
		);

		$loop = new WP_Query( $args );
		while ( $loop->have_posts() ) : $loop->the_post();
			$this->student_data[] = array(
				'id' => get_the_ID(),
				'name' => get_the_title(),
				'year' => get_post_meta(get_the_ID(), 'student_year', true),
				'section' => get_post_meta(get_the_ID(), 'student_section', true),
				'address' => get_post_meta(get_the_ID(), 'student_address', true),
			);
		endwhile;
		$this->items = $this->student_data;
	}

	public function display_rows() {
		foreach ($this->items as $item) {
		?>
			<tr>
				<td class="student_id column-student_id"> <?php echo $item['id'] ?> </td>
				<td class="student_name column-student_name"> <?php echo $item['name'] ?> </td>
				<td class="student_year column-student_year"> <?php echo $item['year'] ?> </td>
				<td class="student_section column-student_section"> <?php echo $item['section'] ?> </td>
				<td class="student_address column-student_address"> <?php echo $item['address'] ?> </td>
			</tr>
		<?php
		}
	}
}

function student_menu_items() {
    add_menu_page( 'Student List', 'Student List', 'activate_plugins', 'student_list_page', 'render_student_list_page' );
}

add_action( 'admin_menu', 'student_menu_items' );

function render_student_list_page() {
	$student_list_table = new Student_List_Table();
	echo '<div class="wrap"><h2>Student List</h2>'; 
		$student_list_table->prepare_items(); 
		$student_list_table->display();
	echo '</div>'; 
}
