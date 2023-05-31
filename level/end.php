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
    return include pad . 'level/split/after2.php';

  if ( $padBefore [$pad] == 2 ) 
    include pad . 'level/split/before3.php';

  include pad . "options/end.php";

  if ( isset ( $padPrm [$pad] ['isolate'] ) )    
    include pad . 'isolate/end.php';

  padResetLvl ($pad);
  
  $pad--;

  if ( $pad >= 0 and $padBefore [$pad] == 1 ) 
    return include pad . 'level/split/before2.php';

  if ( $pad >= 0 )
    padHtml ( $padResult[$pad+1] );

?>