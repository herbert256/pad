<?php

  if ( count ($padData [$pad] ) )
    include PAD . 'occurrence/end.php';

  if ( next($padData [$pad]) !== FALSE )
    return include PAD . 'occurrence/start.php';

  if ( $padWalk [$pad] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include PAD . 'occurrence/start.php';
  }

  $padOccur [$pad] = 0;

  if ( $padWalk [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($padPrmsTag [$pad] ['callback']) and ! isset($padPrmsTag [$pad] ['before']) )
    include PAD . 'callback/exit.php' ;

  if ( $pad )
    include PAD . "options/go/end.php";

  include 'trace/end.php';

  $pad--;
  
  if ( $pad >= 0 )
    pHtml ( $padResult[$pad+1] );
  
?>