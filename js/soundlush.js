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
       url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
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
       url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
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
      url: soundlushquiz.ajax_url, //tells the Ajax call where to send the request
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


});
