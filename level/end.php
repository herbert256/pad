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

  if ( $padWalk [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/exit.php' ;

  if ( $padAfter [$pad] )
    return include 'split/after2.php';

  if ( $padBefore [$pad] == 2 ) 
    include 'split/before3.php';

  include PAD . "options/go/end.php";

  include 'trace/end.php';

  if ($padParse)
    include PAD . 'parse/levelEnd.php';

  $pad--;

  if ( $pad >= 0 and $padBefore [$pad] == 1 ) 
    return include 'split/before2.php';

  if ( $pad >= 0 )
    padHtml ( $padResult[$pad+1] );

?>