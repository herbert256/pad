<?php

  // With $padCsrf on, a flushed page without a form starts no session: the first flush
  // started one for every visitor, and its cookie kept each of them out of the page cache.
  // The page fetches itself as a visitor would and reads the cookies of the answer.

  $padCsrf = TRUE;

  if ( isset ( $_GET ['inner'] ) )
    $verdict = 'inner';
  else {
    $inner   = padCurl ( $padHost . 'regression/pages/?flush/nosession&inner&padInclude' );
    $verdict = isset ( $inner ['cookies'] [ session_name () ] ) ? 'a session was started' : 'no session';
  }

?>
