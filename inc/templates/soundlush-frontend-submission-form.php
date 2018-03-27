
<form id="soundlush_exercise" method="post" enctype="multipart/form-data" data-url="<?php echo admin_url('admin-ajax.php') ?>" data-user="<?php echo get_current_user_id() ?>" data-id="<?php echo get_the_ID() ?>">

 <?php wp_nonce_field( 'frontend_submission_action', 'frontend_submission_nonce' ); ?>

  <div class="form-control">
    <label for="soundlush_exercise_submitted_file">Upload your file:</label>
    <input type="text" name="_soundlush_exercise_submitted_file" id="soundlush_exercise_submitted_file" >
    <small class="text-danger form-control-msg">Your file is required</small>
  </div>

  <div class="form-control">
    <label for="soundlush_exercise_submitted_comments">Comment Notes:</label>
    <textarea name="_soundlush_exercise_submitted_comments" id="soundlush_exercise_submitted_comments"></textarea>
  </div>

  <button type="submit" class="btn btn-primary">Submit Exercise Files</button>
  <small class="text-info form-control-msg js-form-submission">Submission in progress. Please wait...</small>
  <small class="text-success form-control-msg js-form-success">Your exercise was successfully submitted. Thank you!</small>
  <small class="text-danger form-control-msg js-form-error">There was a problem with your submission. Please try again.</small>

</form>
