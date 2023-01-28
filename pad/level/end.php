<?php

  if ( count ($padData [$pad] ) )
    include PAD . 'pad/occurrence/end.php';

  if ( next($padData [$pad]) !== FALSE )
    return include PAD . 'pad/occurrence/start.php';

  if ( $padWalk [$pad] == 'next' ) {
    include PAD . 'pad/walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include PAD . 'pad/occurrence/start.php';
  }

  if ( $padWalk [$pad] == 'end' )
    include PAD . 'pad/walk/end.php';

  if ( isset($padPrmsTag [$pad] ['callback']) and ! isset($padPrmsTag [$pad] ['before']) )
    include PAD . 'pad/callback/exit.php' ;

  include PAD . "pad/options/go/end.php";

  include 'trace/end.php';

  $pad--;
  
  if ( $pad >= 0 )
    padHtml ( $padResult[$pad+1] );
  
?>