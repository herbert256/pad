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

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'pad/callback/exit.php' ;

  if ( $padAfter [$pad] )
    return include 'split/after2.php';

  if ( $padBefore [$pad] == 2 ) 
    include 'split/before3.php';

  include PAD . "pad/options/go/end.php";

  include 'trace/end.php';

  $pad--;

  if ( $pad >= 0 and $padBefore [$pad] == 1 ) 
    return include 'split/before2.php';

  if ( $pad >= 0 )
    padHtml ( $padResult[$pad+1] );

?>