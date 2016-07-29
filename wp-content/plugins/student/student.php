<?php
/*
Plugin Name: Student
Plugin URI: localhost
Version: 0.1
Author: Moretti Georgiev
Plugin for student
*/


define( 'STUDENT_PATH_INCLUDES', dirname( __FILE__ ) . '/inc' );


class Student {
	public function __construct(){
		add_action( 'init', array( $this, 'register_student_cpt' ) );
		add_action( 'init', array( $this, 'add_student_shortcode' ) );
		add_action( 'add_meta_boxes', array( $this, 'student_meta_boxes_callback' ) );
		add_action( 'save_post', array( $this, 'save_meta_box_student') );
		add_action( 'widgets_init', array( $this, 'student_widget' ) );
	}

	public function student_widget(){
		include_once (STUDENT_PATH_INCLUDES . '/widgets/student-widget.class.php');
	}

	public function student_meta_boxes_callback(){

		add_meta_box( 
			'meta_box_student',
			 __( 'Student' ),
			array( $this, 'bottom_meta_box' )
		);

	}

	public function bottom_meta_box($post){

		$year = get_post_meta( $post->ID, 'student_year', true );
		$section = get_post_meta( $post->ID, 'student_section', true);
		$address = get_post_meta( $post->ID, 'student_address', true);
		wp_nonce_field(basename(__FILE__), "meta-box-nonce");
		?>
		<div class="form-group" >
			<label for='student_year'> Year </label>
			<input type="text" name="student_year"3 id="student_year" value="<?php echo $year ?>" />

			<label for='student_section'> Section </label>
			<input type="text" name="student_section" id="student_section" value="<?php echo $section ?>" />

			<label for='student_address'> Address </label>
			<input type="text" name="student_address" id="student_address" value="<?php echo $address ?>" />
		</div>
		<?php

	}

	private function save_student_meta_field ( $post_id, $param ) {
		if ( ! empty ( $_POST['student_year'] ) ){
			$student_param = esc_attr( $_POST[$param] );
        	update_post_meta( $post_id, $param, $student_param );
		} else {
       		delete_post_meta( $post_id, $param );
		}
	}

	public function save_meta_box_student( $post_id ){

 	    if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ){
 	    	return;	
 	    }
     
	    if( empty( $_POST['meta-box-nonce'] ) || !wp_verify_nonce( $_POST['meta-box-nonce'], basename(__FILE__) ) ){
	     	return;
	     }

	    if( ! current_user_can( 'edit_post' ) ){
	    	return;	
	    }

	    $student_meta_fields = array( 'student_year', 'student_section', 'student_address' );
	    foreach ( $student_meta_fields as $meta_field ) {
	    	$this->save_student_meta_field( $post_id, $meta_field );
	    }

	} 

	public function add_student_shortcode() {
		add_shortcode( 'display_students', array( $this, 'display_students') );
	}  		

	public function display_students( $attrs, $content = '' ){
		return include( STUDENT_PATH_INCLUDES . '/shortcodes/display-students.php');
	}

	public function register_student_cpt(){

		$labels = array(
		        'name' => __( 'Students', 'std' ),
		        'singular_name' => __( 'Slide', 'std' ),
		        'add_new' => _x( 'Add New', 'pluginbase', 'std' ),
		        'add_new_item' => __( 'Add New Student', 'std' ),
		        'edit_item' => __( 'Edit Student', 'std' ),
		        'new_item' => __( 'New Student', 'std' ),
		        'view_item' => __( 'View Student', 'std' ),
		        'search_items' => __( 'Search Student', 'std' ),
		        'not_found' => __( 'No students found', 'std' ),
		        'not_found_in_trash' => __( 'No students found in trash', 'std' ),
	      	);

		$args = array(
		      'labels' => $labels,
		      'description' => __( 'Students', 'std' ),
		      'public' => true,
		      'publicly__queryable' => true,
		      'query_var' => true,
		      'rewrite' => true,
		      'exclude_from_search' => true,
		      'show_ui' => true,
		      'show_ui_menu' => true,
	      	  'show_in_rest' => true,
		      'rest_base' => 'student',
  			  'rest_controller_class' => 'Student_REST_Controller',
		      'menu_position' => 40,
		      'supports' => array( 'title',	),
	    	);

		register_post_type( 'student', $args );	

	}

}

$student = new Student();


class Student_REST_Controller extends WP_REST_Controller {

	public function register_routes() {
		$version = '2';
		$namespace = 'wp/v' . $version;
		$base = 'student';
		register_rest_route( $namespace, '/' . $base, array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_items' ),
				'permission_callback' => array( $this, 'get_items_permissions_check' ),
				'args'            => array(),
			),
			array(
				'methods'         => WP_REST_Server::CREATABLE,
				'callback'        => array( $this, 'create_item' ),
				'permission_callback' => array( $this, 'create_item_permissions_check' ),
				'args' => array(
					'student_name' => array( 'required' => true),		
					'student_year' => array( 'required' => true),		
					'student_section' => array( 'required' => true),			
					'student_address' => array( 'required' => true),			
				)
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/(?P<id>[\d]+)', array(
			array(
				'methods'         => WP_REST_Server::READABLE,
				'callback'        => array( $this, 'get_item' ),
				'permission_callback' => array( $this, 'get_item_permissions_check' ),
				'args'            => array(
					'context'          => array(
						'default'      => 'view',
					),
				),
			),
			array(
				'methods'         => WP_REST_Server::EDITABLE,
				'callback'        => array( $this, 'update_item' ),
				'permission_callback' => array( $this, 'update_item_permissions_check' ),
				'args' => array(
					'student_name' => array( 'required' => true),		
					'student_year' => array( 'required' => true),		
					'student_section' => array( 'required' => true),			
					'student_address' => array( 'required' => true),			
				)
			
			),
			array(
				'methods'  => WP_REST_Server::DELETABLE,
				'callback' => array( $this, 'delete_item' ),
				'permission_callback' => array( $this, 'delete_item_permissions_check' ),
				'args'     => array(
					'force'    => array(
						'default'      => false,
					),
				),
			),
		) );
		register_rest_route( $namespace, '/' . $base . '/schema', array(
			'methods'         => WP_REST_Server::READABLE,
			'callback'        => array( $this, 'get_public_item_schema' ),
		) );
	}

	private function meta_data( $post_id, $field, $value = '' ){
		if( ! empty( $value ) ){
	    	update_post_meta( $post_id, $field, $value );
		} else {
			delete_post_meta( $post_id, $field );
		}
	}

	public function get_items( $request ) {

		$args = array(
			'order' => 'DESC',
			'orderby' => 'title',
			'post_type' => 'student',
			'post_status' => 'publish',
		);

		$items = get_posts( $args );

		$data = array();
		foreach( $items as $item ) {
			$itemdata = $this->prepare_item_for_response( $item, $request );
			$data[] = $this->prepare_response_for_collection( $itemdata );
		}

		return new WP_REST_Response( $data, 200 );
	}

	public function get_item( $request ) {
		$params = $request->get_params();
		$args = array(
			'p' => $request['id'],
			'post_type' => 'student',
			'post_status' => 'publish',
		);

		$item = get_posts( $args );

		if ( ! empty( $item ) ) {
			$itemdata = $this->prepare_item_for_response( $item[0], $request );
			$data[] = $this->prepare_response_for_collection( $itemdata );
			return new WP_REST_Response( $data, 200 );
		} else {	
			return new WP_Error(
					'rest_no_route',
					__( 'No route was found matching the URL and request method' ),
					array( 'status' => 404 )
					);
		}
	}

	public function create_item( $request ) {

		$params = $this->prepare_item_for_database( $request );
		$user_id = get_current_user_id();

		$item = array(
			'post_title' => $params['student_name'],
			'post_author' => $user_id,
			'post_type' => 'student',
			'post_status' => 'publish',
		);
		$post_id = 	wp_insert_post($item);

		if( ! empty( $post_id ) ) {
			foreach( $params as $field => $value ) {
				$this->meta_data( $post_id, $field, $value);
			}
			return new WP_REST_Response( __('Successfully created'), 200 );
		}

		return new WP_Error( 'cant-create', __( "Can't create student" ), array( 'status' => 500 ) );

	}

	public function update_item( $request ) {

		$params = $this->prepare_item_for_database( $request );

		$item = array(
			'ID' => $params['id'],
			'post_title' => $params['student_name'],
			'post_type' => 'student',
			'post_status' => 'publish',
		);

		$post_id = wp_update_post( $item );

		if ( ! empty($post_id) ) {
			foreach ($params as $field => $value) {
				$this->meta_data($post_id, $field, $value);
			}
			return new WP_REST_Response( __('Successfully updated'), array('status' => 200));
		}

		return new WP_Error( 'cant-update', __( "Can't create student" ), array( 'status' => 500 ) );

	}

	public function delete_item( $request ) {
		$params = $this->prepare_item_for_database( $request );

		$meta_data = array(
			'student_year',
			'student_section',
			'student_address',
		);
		$deleted_post = wp_delete_post($params['id'], true);
		if ( !empty( $deleted_post ) ) {
			for ($i=0; $i < count($meta_data); $i++) {
				$this->meta_data($params['id'], $meta_data[$i]);
			}
			return new WP_REST_Response( true, 200 );
		}

		return new WP_Error( 'cant-delete', __( "Can't delete student" ), array( 'status' => 500 ) );
	}

	public function get_items_permissions_check( $request ) {
		return true;
	}

	public function get_item_permissions_check( $request ) {
		return true;
	}


	public function create_item_permissions_check( $request ) {
		return is_user_logged_in();
	}

	public function update_item_permissions_check( $request ) {
		$params = $request->get_params();
		$response = current_user_can( 'edit_post', $params['id'] );
		return $response;
	}

	public function delete_item_permissions_check( $request ) {
		$params = $request->get_params();
		$response = current_user_can('delete_post', $params['id'] );
		return $response;
	}


	protected function prepare_item_for_database( $request ) {
		$params = $request->get_params();

		foreach( $params as $field => $value) {
			if ( 'id' != $field ){
				$params[$field] = sanitize_text_field( $value );
			}
		}

		return $params;
	}

	public function prepare_item_for_response( $item, $request ) {
		$data = array(
			'id' => $item->ID,
			'name' => $item->post_title,
			'year' => get_post_meta( $item->ID, 'student_year', true ),
			'section' => get_post_meta( $item->ID, 'student_section', true ),
			'address' => get_post_meta( $item->ID, 'student_address', true ),
		);
		$data = rest_ensure_response( $data );
		return $data;
	}

	/**
	 * Get the query params for collections
	 *
	 * @return array
	 */
	// public function get_collection_params() {
	// 	return array(
	// 		'page'                   => array(
	// 			'description'        => 'Current page of the collection.',
	// 			'type'               => 'integer',
	// 			'default'            => 1,
	// 			'sanitize_callback'  => 'absint',
	// 		),
	// 		'per_page'               => array(
	// 			'description'        => 'Maximum number of items to be returned in result set.',
	// 			'type'               => 'integer',
	// 			'default'            => 10,
	// 			'sanitize_callback'  => 'absint',
	// 		),
	// 		'search'                 => array(
	// 			'description'        => 'Limit results to those matching a string.',
	// 			'type'               => 'string',
	// 			'sanitize_callback'  => 'sanitize_text_field',
	// 		),
	// 	);
	// }
}

