<?php

/**
 * ==============================
 *  SHORTCODE: Exercise Submission Frontend Form
 * ==============================
 */

add_shortcode( 'exercise', 'create_exercise_submission_form' );

function create_exercise_submission_form( $atts, $content = null ){

  // Get the attributes
  $atts = shortcode_atts(
    array(
      //'mime'      => '',
    ),
    $atts,
    'exercise'
  );

  if(is_user_logged_in()){ // and user has product and user have not upload it yet
    // Return html
    ob_start();
    include(dirname(__FILE__) . "/../templates/soundlush-frontend-submission-form.php");
    return ob_get_clean();
  } else {
    return 'You have to be logged in to upload your exercise';
  }
}


add_action( 'wp_ajax_nopriv_save_frontend_submission', 'save_frontend_submission' );
add_action( 'wp_ajax_save_frontend_submission', 'save_frontend_submission' );

function save_frontend_submission()
{
    // sanitize
    $user_id  = wp_strip_all_tags( $_POST['user'] );
    $exercise = wp_strip_all_tags( $_POST['exercise'] );
    $comments = wp_strip_all_tags( $_POST['comments'] );
    $file     = $_POST['file'];

    $user = get_user_by( 'id', $user_id );

    // insert post into database
    $args = array(
      'post_title'   => 'Exercise: '.get_the_title($exercise),
      'post_content' => '',                 // RESERVED for Feedback Content
      'post_author'  => 1,                  // TODO check if subscriber can be author
      'post_type'    => 'submission',
      'post_status'  => 'publish',
      'meta_input'   => array(
        '_soundlush_exercise_user_id'            => $user_id,        // User ID
        '_soundlush_exercise_id'                 => $exercise,    // Exercise ID
        '_soundlush_exercise_submitted_comments' => $comments,    // User Comments
        '_soundlush_exercise_submitted_file'     => $file,        // User Exercise File
      )
    );

    $post_id = wp_insert_post( $args );


    // notify submission via email
    if( $post_id !== 0 )
    {

      // notify course instructor
      $to       =  get_bloginfo('admin_email');
      $subject  = 'Soundlush: New Submission Awaiting Feedback';
      $message  = 'User    : '.$user->display_name.'</br>';
      $message .= 'Course  : '.get_the_title( get_post_ancestors( $exercise ) ).'</br>';
      $message .= 'Exercise: '.get_the_title( $exercise ).'</br>';   ;
      $message .= 'Comments: '.$comments;

      // 'From: Soundlush <admin@soundlush.com>'
      // 'Reply-To: User <user@domain.com>'
      // 'Content-Type: text/html: charset=UFT-8'

      $header[] = 'From: '.get_bloginfo('name').' <'.$to.'>';
      $header[] = 'Reply-To: '.$user->display_name.' <'.$user->user_email.'>';
      $header[] = 'Content-Type: text/html: charset=UFT-8';

      wp_mail( $to, $subject, $message, $header );


      // notify student
      $to       =  $user->user_email;
      $subject  = 'Soundlush: You have submitted your exercise for '.get_the_title( $exercise );
      $message  = 'Thank you for your submission! You will receive feedback on your exercise in the next 72 hours!';

      // 'From: Soundlush <admin@soundlush.com>'
      // 'Reply-To: Soundlush <no-reply@soundlush.com>'
      // 'Content-Type: text/html: charset=UFT-8'

      $header[] = 'From: '.get_bloginfo('name').' <'.$to.'>';
      $header[] = 'Reply-To: <no-reply@soundlush.com>';
      $header[] = 'Content-Type: text/html: charset=UFT-8';

      wp_mail( $to, $subject, $message, $header );

    }

    // return result to AJAX call
    echo $post_id;

    die();
}



function upload_frontend_submission()
{
    $file_errors = array(
    		0 => "File uploaded with success",
    		1 => "Error: The uploaded file exceeds the upload_max_files in server settings",
    		2 => "Error: The uploaded file exceeds the MAX_FILE_SIZE from html form",
    		3 => "Error: The file was only partially uploaded",
    		4 => "Error: No file was uploaded",
    		6 => "Error: Missing a temporary folder",
    		7 => "Error: Failed to write file to disk",
    		8 => "Error: A PHP extension stopped the file upload"
    );

    $posted_data =  isset( $_POST ) ? $_POST : array();
    $file_data = isset( $_FILES ) ? $_FILES : array();

    $data = array_merge( $posted_data, $file_data );


     check_ajax_referer( 'frontend_submission_action', 'frontend_submission_nonce' );
	  $response = array();

    $upload_dir  = wp_upload_dir();
	  $upload_path = trailingslashit( $upload_dir["basedir"] ).'soundlush_uploads/submissions/';
	  $upload_url  = trailingslashit( $upload_dir["baseurl"] ).'soundlush_uploads/submissions/';
		 wp_mkdir_p($upload_path);

		$filename   = $data["_soundlush_exercise_submitted_file"]["name"];
		$filename_changed = str_replace(" ", "_", $filename);

		$temp_name  = $data["_soundlush_exercise_submitted_file"]["tmp_name"];
		$file_size  = $data["_soundlush_exercise_submitted_file"]["size"];
		$file_error = $data["_soundlush_exercise_submitted_file"]["error"];
		$size_limit = 2 * 1024 * 1024; //2MB

		$response["filename"]  = $filename;
		$response["file_size"] = $file_size;

    if( $file_error > 0 )
    {
			$response["response"] = "ERROR";
      $response["error"] = $file_errors[ $file_error ];

		} elseif( file_exists($upload_path . $filename_changed ) )
    {
			$response["response"] = "ERROR";
	    $response["error"] = "File already exists.";

		} elseif( $file_size > $mb )
    {
      $response["response"] = "ERROR";
  		$response["error"]= "File is too large. Max file size is ". ($size_limit/1048576) . " MegaBytes.";

    } elseif( move_uploaded_file( $temp_name, $upload_path . $filename_changed ) )
    {
  		$response["response"] = "SUCCESS";
  		$response["url"] =  $upload_url . $filename_changed;
  		$file = pathinfo( $upload_path . $filename_changed );

  		if( $file && isset( $file["extension"] ) ){
  			$type = $file["extension"];
  			if( $type == "jpeg" || $type == "jpg" || $type == "png"|| $type == "gif" ) {
					$type = "image/" . $type;
				}
  			$response["type"] = $type;
  		}

    } else
    {
  		$response["response"] = "ERROR";
  		$response["error"]= "Upload Failed.";
  	}

    echo json_encode( $response );
  	die();
}



/**
 * ==============================
 *  POSTTYPE: Exercise Submission
 * ==============================
 */

$options = array(
    'hierarchical' => true,
    //'rewrite'      => array( 'slug'=> 'anthology' ), // set parent slug
    'supports'     => array( 'title', 'editor', 'author', 'page-attributes' )
);

$submission = new SoundlushPostType('submission', $options);

// set Submission menu icon
$submission->icon('dashicons-admin-settings');

//$submission->setAsParent('anthology');

// hide the date and author columns
$submission->columns()->hide(['author', 'date']);

// add a price and rating column
$submission->columns()->add([
    'username'        => __('Username'),
    'exercise'        => __('Exercise'),
    'submission_date' => __('Submission Date'),
    'feedback_date'   => __('Feedback Date')
]);

// populate the custom column
$submission->columns()->populate('username', function($column, $post_id) {
    $postmeta = get_post_meta($post_id, '_soundlush_exercise_user_id', true);
    $user = get_user_by( 'id', $postmeta );
    echo isset( $user ) ? $user->first_name.' '.$user->last_name : '';
});

$submission->columns()->populate('exercise', function($column, $post_id) {
    $postmeta = get_post_meta($post_id, '_soundlush_exercise_id', true);
    echo isset( $postmeta ) ? get_the_title($postmeta) : '';
});

$submission->columns()->populate('submission_date', function($column, $post_id) {
    echo get_the_time( 'Y/m/d g:i A (T)', $post_id );
});

$submission->columns()->populate('feedback_date', function($column, $post_id) {
    $postmeta = get_post_meta($post_id, '_soundlush_exercise_feedback_date', true);
    echo isset( $postmeta ) ? $postmeta : '';
});

// set sortable columns
$submission->columns()->sortable([
    'username'        => [ '_soundlush_exercise_user_id', true ],
    'exercise'        => [ '_soundlush_exercise_id', true],
    'submission_date' => [ 'date', true ],
    'feedback_date'   => [ '_soundlush_exercise_feedback_date', true ],
]);

// set custom field metabox for Submission
$submission->customfields()->add( array(
  'id'        => 'feedback',
  'title'     => __( 'Publish Feedback' ),
  'fields'    => array(
    array(
        'label'     => 'Feedback available to user',
        'id'        => '_soundlush_exercise_feedback_available',
        'type'      => 'checkbox',
    ),
    array(
        'label'     => 'Date',
        'desc'      => 'This is the feedback.',
        'std'       => '', //TODO get system date
        'id'        => '_soundlush_exercise_feedback_date',
        'type'      => 'now',
    )
  ),
  'context'   => 'side',
  'priority'  => 'high'
));

// set custom field metabox for Submission
$submission->customfields()->add( array(
  'id'        => 'file_submission',
  'title'     => __( 'File Submission' ),
  'fields'    => array(
    array(
        'label'     => 'User ID',
        'desc'      => 'This is the User ID',
        'std'       => '',
        'id'        => '_soundlush_exercise_user_id',
        'type'      => 'text',
        'readonly'  => true
    ),
    array(
        'label'     => 'Exercise',
        'desc'      => 'This is the Exercise ID',
        'std'       => '',
        'id'        => '_soundlush_exercise_id',
        'type'      => 'text',
        'readonly'  => true
    ),
    array(
        'label'     => 'File',
        'desc'      => 'This is the Exercise File.',
        'std'       => '',
        'id'        => '_soundlush_exercise_submitted_file',
        'type'      => 'text',
        'readonly'  => true
    ),
    array(
        'label'     => 'Comment notes',
        'desc'      => 'These are the User Notes about the Exercise.',
        'std'       => '',
        'id'        => '_soundlush_exercise_submitted_comments',
        'type'      => 'textarea',
        'readonly'  => true
    )
  )
));


$submission->register();
