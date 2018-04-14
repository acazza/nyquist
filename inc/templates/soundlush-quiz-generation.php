<?php $lastquiz = get_last_quiz();
      //var_dump( $lastquiz );
?>
<?php if( $lastquiz !== false ): ?>

      <div class="quiz-page">
        <h3> You have already taken this quiz!</h3>
        <h4> Quiz Results: </h4>

        <!-- Show Previous/Last Quiz Results Here -->
        <?php
        $postmeta = get_post_meta( $lastquiz[0]->ID, '_repeater', false );
        //var_dump( $postmeta );
        ?>

        <p> Would you like to take it again and try to improve your score?</br>
            You will have <strong><?php echo $atts['duration'] ?> minutes</strong> to complete your quiz after clicking the <strong>"Retake Quiz"</strong> button</p>
        <p class="text-danger"> IMPORTANT: Your previous results for this quiz will be erased and replaced</p>

        <button id="soundlush_generate_quiz" class="btn btn-accent" data-l1="<?php $atts['easy'] ?>" data-l2="<?php $atts['normal'] ?>" data-l3="<?php $atts['hard'] ?>" data-pool="<?php echo $atts['pool'] ?>">Retake Quiz</button>
      </div>
      <div id="the-quiz"></div>

<?php else: ?>

      <div class="quiz-page">
        <h3> Your quiz is about to start!</h3>
        <p> You will have <strong><?php echo $atts['duration'] ?> minutes</strong> to complete your quiz after clicking the <strong>"Take Quiz"</strong> button</p>

        <button id="soundlush_generate_quiz" class="btn btn-accent" data-l1="<?php echo $atts['easy'] ?>" data-l2="<?php echo $atts['normal'] ?>" data-l3="<?php echo $atts['hard'] ?>" data-pool="<?php echo $atts['pool'] ?>">Take Quiz</button>
      </div>
      <div id="the-quiz"></div>

<?php endif ?>
