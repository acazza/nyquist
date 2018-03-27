jQuery( document ).ready( function($){


  /*
   * === === === === ===
   * Walker Mega Menu
   * === === === === ===
   */

  /* When the user clicks on the button,
  toggle between hiding and showing the dropdown content */

  $( '.dropdown-toggle' ).on( 'click', function( e ){
    $( this ).siblings( '.dropdown-menu' ).toggleClass( 'show' );
  });

  /* Close the dropdown menu if the user clicks outside of it */

  window.onclick = function(event) {
    if( !event.target.matches( '.dropdown-toggle' )) {
      $( '.dropdown-menu' ).removeClass( 'show' );
    }
  }

  /*
   * === === === === ===
   * Quiz
   * === === === === ===
   */


   $( '#retake_quiz' ).on( 'click', function( e ){

     console.log('retake');
     var data = "action=retake_user_quiz";

     $.ajax({
       //url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
       type : 'post',
       data : data,
       success : function( response ) {
           if( response.success = 1 ){
             alert( 'Your quiz will start now' );
           } else {
             alert( 'Oops... something went wrong' );
           }
       }
     })
   });


   $( '#start_quiz' ).on( 'click', function( e ){


     var data = "action=start_user_quiz";

     $.ajax({
       //url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
       type : 'post',
       data : data,
       success : function( response ) {
           if( response.success = 1 ){
             alert( 'Your quiz will start now' );
           } else {
             alert( 'Oops... something went wrong' );
           }
       }
     })
   });




  $( '#save_quiz' ).on( 'click', function( e ){

    // get all checked radio buttons dynamically
    var data = "action=save_user_quiz&"+$.map($("input:radio:checked"), function(elem, idx) {
       return "&"+$(elem).attr("name") + "="+ $(elem).val();
    }).join('');

    $.ajax({
    //url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
      type : 'post',
      data : data,
		  success : function( response ) {
          if( response.success = 1 ){
            alert( 'Your quiz was submitted successfully' );
          } else {
            alert( 'Oops... something went wrong' );
          }
		  }

    })
  });

  /**
   *  FRONTEND USER SUBMISSION FORM
   */

  $('#soundlush_exercise').on('submit', function(e)
  {
      // stop default submit behavior
      e.preventDefault();

      // remove all previous error/feedback messages
      $('.has-error').removeClass('has-error');
      $('.js-show-form-feedback').removeClass('js-show-form-feedback');

      // get form data
      var form     = $(this),
          file     = form.find('#soundlush_exercise_submitted_file').val(),
          comments = form.find('#soundlush_exercise_submitted_comments').val(),
          user     = form.data('user'),
          exercise = form.data('id'),
          ajaxurl  = form.data('url');


      // check if required fields are filled in
      if( file == '' ){
        $('#soundlush_exercise_submitted_file').parents('.form-control').addClass('has-error');
        console.log('Required inputs are empty');
        return;
      } // TODO check if file is from the accepted type

      // disable submit button during ajax call to avoid multiple submissions
      form.find('input, button, textarea').attr('disabled', 'disabled');
      $('.js-form-submission').addClass('js-show-form-feedback');

      // submit data
      $.ajax({
        url: ajaxurl,
        type : 'post',
        data : {
            user : user,
            exercise : exercise,
            file : file,
            comments : comments,
            action: 'save_frontend_submission'
        },
        error : function( response ){

          $('.js-form-submission').removeClass('js-show-form-feedback');
          $('.js-form-error').addClass('js-show-form-feedback');
          form.find('input, button, textarea').removeAttr('disabled');

        },
  		  success : function( response ){

            if( response.success == 0 ) {

              setTimeout(function(){
                $('.js-form-submission').removeClass('js-show-form-feedback');
                $('.js-form-error').addClass('js-show-form-feedback');
                form.find('input, button, textarea').removeAttr('disabled');
              }, 2000);

            } else {

              setTimeout(function(){
                $('.js-form-submission').removeClass('js-show-form-feedback');
                $('.js-form-success').addClass('js-show-form-feedback');
                form.find('input, button, textarea').removeAttr('disabled').val('');
              }, 2000);

            }
  		  }
      })

  })


});
