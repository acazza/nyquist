<?php
/**
 * The template for displaying the footer
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<footer class="site-footer">
  <div class="container">
    <?php
    if( is_active_sidebar( 'footer-col-1' ) || is_active_sidebar( 'footer-col-2' ) || is_active_sidebar( 'footer-col-2' ) ):
      echo '<div class="footer-column-binder">';
      if ( is_active_sidebar( 'footer-col-1' ) ):
        echo '<div class="footer-column">';
		      dynamic_sidebar('footer-col-1');
        echo '</div>';
      endif;
      if ( is_active_sidebar( 'footer-col-2' ) ):
        echo '<div class="footer-column">';
		      dynamic_sidebar('footer-col-2');
        echo '</div>';
      endif;
      if ( is_active_sidebar( 'footer-col-3' ) ):
        echo '<div class="footer-column">';
		      dynamic_sidebar('footer-col-3');
        echo '</div>';
      endif;
    endif;
    echo '</div>';
    echo '</div> <!-- .container -->';

    if ( is_active_sidebar( 'footer-bottom' ) ):
      echo '<div class="footer-row">';
        echo '<div class="container">';
          dynamic_sidebar('footer-bottom');
          echo '</div> <!-- .container -->';
      echo '</div>';
    endif;
    ?>

</footer>
<?php wp_footer(); ?>
</body>
</html>
