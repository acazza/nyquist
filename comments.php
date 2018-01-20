<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package com.soundlush.theme.v1
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */

if( post_password_required() ):
  return;
endif;
?>

<section id="comments" class="article-comments">

  <?php
    if( have_comments() ):
  ?>

    <h4 class="comment-title">
      <?php
        printf(
          esc_html( _nx( 'One comment on &ldquo;%2$s&rdquo;', '%1$s comments on &ldquo;%2$s&rdquo;', get_comments_number(), 'comments title', 'soundlush' ) ),
          number_format_i18n( get_comments_number() ),
          '<span>' . get_the_title() . '</span>'
        );
      ?>
    <h4>

    <?php soundlush_get_comment_navigation(); ?>

    <ol class="comment-list">
      <?php
        $args = array(
          'walker'            => null,
          //'max-depth'         => '',
          'style'             => 'ol',
          'callback'          => null,
          'end-callback'      => null,
          'type'              => 'all',
          'reply_text'        => 'Reply',
          //'page'              => '',
          //'per_page'          => 2,
          'avatar_size'       => 64,
          'reverse_top_level' => null,
          'reverse_children'  => '',
          'format'            => 'html5',
          'short_ping'        => false,
          'echo'              => true
        );

        wp_list_comments( $args );

      ?>
    </ol>

    <?php soundlush_get_comment_navigation(); ?>

    <?php
      if( !comments_open() && get_comments_number() ):
    ?>
      <p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'soundlush' ) ?>
    <?php
      endif;
    ?>

  <?php
    endif;
  ?>
  <?php

    $commenter = wp_get_current_commenter();
    $fields = array(
      'author' =>
        '<div class="soundlush-form-group comment-form-author"><label for="author">' .
        __( 'Name', 'soundlush' ) . '</label> ' .
        '<span class="required">*</span>' .
        '<input id="author" name="author" type="text" class="soundlush-input-field" required="required" value="' .
        esc_attr( $commenter['comment_author'] ) .
        '" placeholder="' .
        __('Enter your name', 'soundlush') . '" /></div>',

      'email' =>
        '<div class="soundlush-form-group comment-form-email"><label for="email">' .
        __( 'Email', 'soundlush' ) . '</label> ' .
        '<span class="required">*</span>' .
        '<input id="email" name="email" type="text" class="soundlush-input-field" required="required" value="' .
        esc_attr(  $commenter['comment_author_email'] ) .
        '"  placeholder="' .
        __('Enter your email', 'soundlush') . '" /></div>',

      'url' =>
        '<div class="soundlush-form-group comment-form-url"><label for="url">' .
        __( 'Website', 'soundlush' ) . '</label>' .
        '<input id="url" name="url" type="text" class="soundlush-input-field" value="' .
        esc_attr( $commenter['comment_author_url'] ) .
        '"  placeholder="' .
        __('Enter your URL', 'soundlush') .
        '" /></div>',
    );

    $args = array(
      'class_submit'  => 'btn btn-submit btn-primary',
      'label_submit'  => 'Submit Comment',
      'comment_field' => '<div class="soundlush-form-group"><label for="comment">' . _x( 'Comment', 'soundlush' ) .
      '</label><textarea id="comment" class="soundlush-input-field" name="comment" rows="4" required="required" placeholder="' .
      __( 'Enter your comment', 'soundlush' ) . '"></textarea></div>',
      'fields'        => apply_filters( 'comment_form_default_fields', $fields  )
    );
    comment_form($args);
  ?>

</section>
