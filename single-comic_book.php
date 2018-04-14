<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package com.soundlush.theme.v1
 */
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id-"main" class="site-main" role="main">
    <div class="container">
      <?php
      if( is_user_logged_in()):
        if( have_posts() ):
          while( have_posts() ): the_post();

            soundlush_save_post_views( get_the_ID() );
            get_template_part( 'template-parts/single', get_post_format() ); ?>

            <?php soundlush_mark_as_viewed(); ?>

            <aside class="tree-structure">
              <?php soundlush_get_tree_structure()?>
            </aside>

            <section class="article-navigation">
              <?php
              $post_type = get_post_type();
              $nav = new SoundlushCustomPostNav();
              $nav->get_custom_post_nav( $post_type );
              ?>

            </section>
            <?php if( comments_open() ):
                      comments_template();
                  endif;
          endwhile;
        endif;
      else:
        echo 'You have to be logged in to access this content';
      endif;
      ?>
    </div> <!-- .container -->



  <main>
<div> <!-- #primary -->

<?php get_footer(); ?>
