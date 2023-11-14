<?php

  if ( isset ( $padOccurStart [$pad] ) )
    if ( isset ( $padOccurStart [$pad] [$padOccur[$pad]] ) )
      include pad . 'occurrence/end.php';

  if ( next($padData [$pad]) !== FALSE ) {
    $padOccurTypeSet = 'loop';  
    return include pad . 'occurrence/start.php';
  }

  if ( $padWalk [$pad] == 'next' ) {
    include pad . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' ) {
      $padOccurTypeSet = 'next';  
      return include pad . 'occurrence/start.php';
    }
  }

  if ( $padBeforeBase [$pad] ) 
    return include pad . 'level/split/before2.php';

  if ( $padAfterBase [$pad] )
    return include pad . 'level/split/after2.php';

  $padOccur [$pad] = 99999;

  if ( $padWalk [$pad] == 'end' )
    include pad . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/exit.php' ;

  include pad . 'options/end.php';

  if ( count ( $padOptionsCallback [$pad] ) )
    include pad . 'options/callback.php';
 
  if ( $padTraceActive ) include pad . 'trace/level/end.php';    
  if ( $padBuildXml    ) include pad . 'xml/level/end.php';  
  
  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padPad ( $padResult[$pad+1] );

?>