<form class="soundlush-form-group" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
  <input type="search" class="soundlush-input-field widefat" placeholder="Search" value="<?php echo get_search_query(); ?>" name="s" title="Search" />
  <label>
    <input type="submit" class="search"/>
    <?php echo soundlush_print_svg( 'search' ); ?>
  </label>
</form>
