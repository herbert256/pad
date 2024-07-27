<?php

  if ( isset ( $padOccurStart [$pad] ) )
    if ( isset ( $padOccurStart [$pad] [$padOccur[$pad]] ) )
      include pad . 'occurrence/end.php';

  if ( next ($padData [$pad]) !== FALSE )
    return include pad . 'occurrence/start.php';

  if ( $padWalk [$pad] == 'next' ) {
    include pad . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include pad . 'occurrence/start.php';
  }

  if ( $padStartBase [$pad] ) return include pad . 'level/start_end/start2.php';
  if ( $padEndBase [$pad]   ) return include pad . 'level/start_end/end2.php';

  $padOccur [$pad] = 99999;

  if ( $padWalk [$pad] == 'end' )
    include pad . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include pad . 'callback/exit.php' ;

  include pad . 'options/go/end.php';
 
  if ( padInfo ) 
    include pad . 'info/events/levelEnd.php';    
  
  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padPad ( $padResult[$pad+1] );

?>