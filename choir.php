<?php

/**
 * @package Choir
 * @version 1.0.1
 */
/*
Plugin Name: Choir for WordPress
Plugin URI: http://wordpress.org/plugins/choir/
Description: Choir for WordPress allows you to <strong>listen to the activities like page view, new comments and new posts on your WordPress site</strong>. Sound gives you ambient awareness without effort. You don't need to constantly switch contexts to see what's going on.  To get started: 1) Click the "Activate" link to the left of this description; 2) <a href="https://choir.io">Go to choir.io to get an API key</a>; 3) Go to your Choir configuration page, and save your API key. 
Author: Choir
Version: 1.0.1
Author URI: http://choir.io/
License: MIT
*/


function choir_send($label, $text) {
  $url = 'https://hooks.choir.io/' . get_option('choir-apikey');
  wp_remote_post($url, array(
      'body' => array('label' => $label, 'text' => $text)
  ));
}

/* New post published. */
function choir_publish_post($post_id) {
  if( ( $_POST['post_status'] == 'publish' ) && ( $_POST['original_post_status'] != 'publish' ) ) {
    $site = bloginfo('name');
    $post = get_post($post_id);
    $author = get_userdata($post->post_author);
    ob_start(); ?>
    
      <?php echo $author->user_firstname ?> just published new post 
        <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?></a>
        on <?php bloginfo( 'name' ) ?>
        
    <?php
    $text = ob_get_contents();
    ob_end_clean();
    
    choir_send('publish_post', $text);
  }
}
add_action('publish_post', 'choir_publish_post');

/* New comment added. */
function choir_comment_post($comment_id) {
  $comment = get_comment($comment_id);
  if( ( $comment->comment_approved == 'spam') ) return;
  
  $post = get_post($comment->comment_post_ID);
  ob_start(); ?>
  
    <?php echo $comment->comment_author ?> from <?php echo $comment->comment_author_IP ?> added a comment on post 
      <a href="<?php echo get_permalink($post->ID) ?>"><?php echo $post->post_title ?></a>: <br/>
      <?php echo $comment->comment_content ?>
      
  <?php
  $text = ob_get_contents();
  ob_end_clean();
  
  choir_send('comment_post', $text);
}
add_action('comment_post','choir_comment_post');

/* Every page view. */
function choir_page_view($wp) {
  if(is_admin()) return;
  
  ob_start(); ?>
  
  New visit: <a href="<?php echo get_permalink($post->ID) ?>"><?php the_title_attribute() ?> - <?php bloginfo( 'name' ) ?></a> 
  
  (
  IP: <?php echo $_SERVER[REMOTE_ADDR] ?>
  <?php if(isset($_SERVER['HTTP_REFERER'])) ?>Referral: <?php echo $_SERVER['HTTP_REFERER'] ?>
  )
  
  <?php
  $text = ob_get_contents();
  ob_end_clean();
  
  choir_send('page_view', $text);
}
add_action('wp','choir_page_view');


include_once dirname( __FILE__ ) . '/choir_settings.php';
?>
