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

  if ( $padBeforeBase [$pad] ) 
    return include pad . 'level/split/before2.php';

  if ( $padAfterBase [$pad] )
    return include pad . 'level/split/after2.php';

  $padOccur [$pad] = 0;

  if ( $padTraceActive )
    include pad . 'trace/level/exits.php'; 

  if ( $padWalk [$pad] == 'end' )
    include pad . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/exit.php' ;

  include pad . 'options/end.php';

  padResetLvl ($pad);

  if ( count ( $padOptionsCallback [$pad] ) )
    include pad . 'options/callback.php';
 
  if ( $padTraceActive ) 
    include pad . 'trace/level/end.php';    
  
  $pad--;

  if ( $pad >= 0 and $padBefore [$pad] == 1 ) 
    return include pad . 'level/split/before2.php';

  if ( $pad >= 0 )
    padPad ( $padResult[$pad+1] );

?>