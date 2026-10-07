<?php

  // The fixture of partials: a page that answers its fragment alone to a script asking for a
  // part of it - the headers jQuery, Turbo and Unpoly send - and the whole page otherwise.

  if ( isset ( $_SERVER ['HTTP_X_REQUESTED_WITH'] ) or isset ( $_SERVER ['HTTP_TURBO_FRAME'] ) or isset ( $_SERVER ['HTTP_X_UP_TARGET'] ) )
    $padFragmentOnly = 'part';

?>
