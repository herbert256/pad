<?php

  global $padInfoTrace, $padInfoTraceCurl;

  if ( ! $padInfoTrace )
    return;

  if ( ! $padInfoTraceCurl )
    return;

  padInfoTrace ( 'curl', 'file', $url );

?>
