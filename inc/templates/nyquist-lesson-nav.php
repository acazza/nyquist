<nav class="posts-navigation" role="navigation">
  <h4 class="hide"><?php esc_html_e( 'Post navigation', 'nyquist' ) ?></h4>
  <div class="post-nav-link">
    <div class="post-previous-link">
      <?php
        $format = '&laquo; %link';
        $link = '%title';
        previous_post_link( $format, $link, $in_same_term = true, $excluded_terms = '', $taxonomy = 'module' );
      ?>
    </div>
    <div class="post-next-link">
      <?php
        $format = '%link &raquo;';
        $link = '%title';
        next_post_link( $format, $link, $in_same_term = true, $excluded_terms = '', $taxonomy = 'module' );
        //next_comments_link( esc_html__( 'Newer Comments', 'nyquist' ) );
      ?>
    </div>
  </div>
</nav>
