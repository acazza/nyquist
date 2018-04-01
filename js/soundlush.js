jQuery( document ).ready( function($){


  /*
   * === === === === ===
   * Tree Structure
   * === === === === ===
   */
  var accordion = (function(){
    var $accordion = $('.js-accordion');
    var $accordion_header = $accordion.find('.js-accordion-header');
    var $accordion_item = $('.js-accordion-item');

    // default settings
    var settings = {
       speed: 400,     // animation speed
       oneOpen: false  // close all other accordion items if true
    };

    return {
       // pass configurable object literal
      init: function($settings) {

          $accordion_header.on('click', function() {
              accordion.toggle($(this));
          });

          $.extend(settings, $settings);

          // ensure only one accordion is active if oneOpen is true
          if(settings.oneOpen && $('.js-accordion-item.active').length > 1) {
            $('.js-accordion-item.active:not(:first)').removeClass('active');
          }

          // ensures that current post accordion body is open when page loads
          $('.current-post').parents('.js-accordion-item').toggleClass('active');

          // reveal the active accordion bodies
          $('.js-accordion-item.active').find('> .js-accordion-body').show();

      },

      toggle: function($this) {

          if(settings.oneOpen && $this[0] != $this.closest('.js-accordion').find('> .js-accordion-item.active > .js-accordion-header')[0]) {
              $this.closest('.js-accordion')
                .find('> .js-accordion-item')
                .removeClass('active')
                .find('.js-accordion-body')
                .slideUp()
          }

         // show/hide the clicked accordion item
         $this.closest('.js-accordion-item').toggleClass('active');
         $this.next().stop().slideToggle(settings.speed);
      }
    }
  })();

  $(document).ready(function(){
      accordion.init({ speed: 300, oneOpen: false });
  });


  //  var acc = document.getElementsByClassName("accordion");
  //  var i;
   //
  //  for (i = 0; i < acc.length; i++) {
  //    acc[i].addEventListener("click", function() {
  //      this.classList.toggle("active");
  //      var panel = this.nextElementSibling;
  //      if (panel.style.maxHeight){
  //        panel.style.maxHeight = null;
  //      } else {
  //        panel.style.maxHeight = panel.scrollHeight + "px";
  //      }
  //    });
  //  }

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
       //url: ajax_soundlush.ajax_url, //tells the Ajax call where to send the request
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
       //url: ajax_soundlush.ajax_url, //tells the Ajax call where to send the request
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
    //url: ajax_soundlush.ajax_url, //tells the Ajax call where to send the request
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


  $('#soundlush_markcomplete_btn').on('click', function(e){

      var user = $(this).data('user'),
          post = $(this).data('id');

      $.ajax({
        url: ajax_soundlush.ajax_url,
        type : 'post',
        data: {
          user: user,
          post: post,
          nonce: ajax_soundlush.ajax_nonce,
          action: 'mark_as_completed'
        },
        error : function( jqXHR, textStatus, errorThrown ){
         console.log(textStatus);
        },
        success : function( data, textStatus, jqXHR ){
         // Go to the next lesson
         console.log(textStatus);
        }
      });


  });


  /*
   * === === === === ===
   * FRONTEND USER SUBMISSION FORM
   * === === === === ===
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
          file     = form.find('#soundlush_exercise_submitted_file');
          comments = form.find('#soundlush_exercise_submitted_comments').val(),
          user     = form.data('user'),
          exercise = form.data('id'),
          nonce    = form.find("#_frontend_submission_nonce").val();

          ajaxurl  = form.data('url'),
          form_data = new FormData();


      // check if required fields are filled in
      // TODO check if file is from the accepted type
      if( file.val() == '' ){
        file.parents('.form-control').addClass('has-error');
        console.log('Required inputs are empty');
        return;
      }


      // append all submitted values to formdata variable
      form_data.append("user", user);
      form_data.append("exercise", exercise);
      form_data.append("file", file[0].files[0]);
      form_data.append("comments", comments);
      form_data.append("nonce", nonce);
      form_data.append("action", "save_frontend_submission" );


      // disable submit button during ajax call to avoid multiple submissions
      form.find('input, button, textarea').attr('disabled', 'disabled');
      $('.js-form-submission').addClass('js-show-form-feedback');


      // submit data
      $.ajax({
        url: ajaxurl,
        type : 'post',
        data: form_data,
        cache: false,
        dataType: 'json',
        processData: false,   // Don't process the files
        contentType: false,   // Set content type to false as jQuery will tell the server its a query string request
        error : function( response ){

          $('.js-form-submission').removeClass('js-show-form-feedback');
          $('.js-form-error').addClass('js-show-form-feedback');
          form.find('input, button, textarea').removeAttr('disabled');

        },
  		  success : function( response ){

            //check how to deal with response array
            if( response.response == "ERROR" ){

              setTimeout(function(){
                $('.js-form-submission').removeClass('js-show-form-feedback');
                $('.js-form-error-detail').html( response.error );
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
