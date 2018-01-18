<nav class="comments-navigation" role="navigation">
  <h4 class="hide"><?php esc_html_e( 'Comment navigation', 'nyquist' ) ?></h4>
  <div class="comment-nav-link">
    <div class="comment-previous-link">
      <?php
        echo nyquist_print_svg('angle-left');
        previous_comments_link( esc_html__( 'Older Comments', 'nyquist' ) );
        //previous_comments_link( '« Older Comments' );
      ?>
    </div>
    <div class="comment-next-link">
      <?php
        //next_comments_link( '« Newer Comments' );
        next_comments_link( esc_html__( 'Newer Comments', 'nyquist' ) );
        echo nyquist_print_svg('angle-right');
      ?>
    </div>
  </div>
</nav>
