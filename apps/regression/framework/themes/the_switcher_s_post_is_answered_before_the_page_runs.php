<?php

  // The post of a {pad:themes/} switcher - in the frame's bar of every PAD application - is
  // answered before the application runs (inits/themes.php): a 303 back to the address it was
  // posted to, the query kept, and the page's own PHP never reached. Taken by the page, it
  // ran a page that acts on any POST - a cart, a guestbook, a logout - as this one would. The
  // theme posted is none, so no session changes.

  if ( padRequestIs ( 'POST' ) ) {
    $answer = 'the page ran with the post';
    return;
  }

  $back = padCurl ( [
    'url'     => $padHost . 'regression/framework/?themes/the_switcher_s_post_is_answered_before_the_page_runs&x=1',
    'post'    => [ 'padForm' => 'padThemes', 'padTheme' => 'no-such-theme' ],
    'options' => [ 'FOLLOWLOCATION' => FALSE ] ] );

  $answer = $back ['result'] . ' ' . ( isset ( $back ['headers'] ['Location'] )
                                        ? str_replace ( $padHost, '', $back ['headers'] ['Location'] )
                                        : trim ( strip_tags ( $back ['data'] ) ) );

?>
