<?php

  if ( count ( $padData [$pad] ) )
    include pad . 'occurrence/end.php';

  if ( next($padData [$pad]) !== FALSE )
    return include pad . 'occurrence/start.php';

  if ( $padWalk [$pad] == 'next' ) {
    include pad . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include pad . 'occurrence/start.php';
  }

  $padOccur [$pad] = 0;

  if ( $padWalk [$pad] == 'end' )
    include pad . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/exit.php' ;

  if ( $padAfter [$pad] )
    return include 'split/after2.php';

  if ( $padBefore [$pad] == 2 ) 
    include 'split/before3.php';

  include pad . "options/go/end.php";

  $pad--;

  if ( $pad >= 0 and $padBefore [$pad] == 1 ) 
    return include 'split/before2.php';

  if ( $pad >= 0 )
    padHtml ( $padResult[$pad+1] );

?>