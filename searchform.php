<form class="nyquist-form-group" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <input type="search" class="nyquist-input-field widefat" placeholder="Search" value="<?php echo get_search_query(); ?>" name="s" title="Search" />
  <label>
    <input type="submit" class="search"/>
    <?php echo nyquist_print_svg( 'search' ); ?>
  </label>
</form>
