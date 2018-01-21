<?php

if( !class_exists( 'SoundlushDynamic') )
{

  class SoundlushDynamic extends SoundlushCustomPostType
  {

    function add_dynamic_custom_fields( $custom_array )
    {
      if( ! empty( $custom_array ) )
      {
        // We need to know the Post Type name again
        $post_type_name = $this->post_type_name; // question

        global $fields;

        // Meta variables
        $box_id         = SoundlushHelpers::uglify( $custom_array['id'] ); //TODO FALLBACK
        $box_title      = SoundlushHelpers::beautify( $custom_array['title'] ); //TODO FALLBACK
        $box_context    = $custom_array['context'] ? $custom_array['context'] : 'normal';
        $box_priority   = $custom_array['priority'] ? $custom_array['priority'] : 'default';
        $fields         = $custom_array['fields'];
      }

      add_action( 'add_meta_boxes',
        function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields )
        {
          add_meta_box(
            $box_id,
            $box_title,
            function( $post, $data ) use( $fields )
            {
              global $post;

              wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' ); ?>

              <div id="meta_inner">

                <?php
                //get the saved meta as an array
                $answers = get_post_meta( $post->ID, 'answers', false );
                var_dump($answers);
                $c = 0;
                if ( count( $answers ) > 0 ) {
                    foreach( $answers as $answer ) {
                        if ( isset( $answer['content'] ) || isset( $answer['correct'] ) ) {
                            printf( '<div><label for="answers[%1$s][content]">Answer: </label><input type="text" name="answers[%1$s][content]" value="%2$s" /><label for="answers[%1$s][correct]"><input type="checkbox" name="answers[%1$s][correct]" "%3$s" />Correct</label><button class="remove">%4$s</button></div>', $c, $answer['content'], $answer['correct'] ? 'checked' : '', __( 'Remove Answer' ) );
                            $c = $c +1;
                        }
                    }
                }

                ?>
            <span id="here"></span>
            <button class="add"><?php _e('Add Answers'); ?></button>
            <script>
                var $ =jQuery.noConflict();
                $(document).ready(function() {
                    var count = <?php echo $c; ?>;
                    $(".add").click(function() {
                        count = count + 1;
                        if( count < 6 ){
                        $('#here').append('<div><label for="answers['+count+'][content]">Answer: </label><input type="text" name="answers['+count+'][content]" value="" /> <label for ="answers['+count+'][correct]"><input type="checkbox" name="answers['+count+'][correct]" />Correct</label><button class="remove">Remove Answer</button></div>' );
                        return false;
                        }
                    });
                    $(".remove").live('click', function() {
                        $(this).parent().remove();
                    });
                });
                </script>
            </div>
            <?php
            },
            $post_type_name,
            $box_context,
            $box_priority
            //array( $fields )
          );
        }
      );
    }
  }
}
