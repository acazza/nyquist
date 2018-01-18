<nav class="posts-navigation" role="navigation">
  <h4 class="hide"><?php esc_html_e( 'Post navigation', 'nyquist' ) ?></h4>
  <div class="post-nav-link">
    <div class="post-previous-link">
      <?php
        echo nyquist_print_svg('angle-left');
        previous_post_link();
      ?>
    </div>
    <div class="post-next-link">
      <?php
        next_post_link();
        //next_comments_link( esc_html__( 'Newer Comments', 'nyquist' ) );
        echo nyquist_print_svg('angle-right');
      ?>
    </div>
  </div>
</nav>
