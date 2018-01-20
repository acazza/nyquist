<nav class="comments-navigation" role="navigation">
  <h4 class="hide"><?php esc_html_e( 'Comment navigation', 'soundlush' ) ?></h4>
  <div class="comment-nav-link">
    <div class="comment-previous-link">
      <?php
        echo soundlush_print_svg('angle-left');
        previous_comments_link( esc_html__( 'Older Comments', 'soundlush' ) );
        //previous_comments_link( '« Older Comments' );
      ?>
    </div>
    <div class="comment-next-link">
      <?php
        //next_comments_link( '« Newer Comments' );
        next_comments_link( esc_html__( 'Newer Comments', 'soundlush' ) );
        echo soundlush_print_svg('angle-right');
      ?>
    </div>
  </div>
</nav>
