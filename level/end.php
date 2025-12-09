<?php

  if ( isset ( $padOccurStart [$pad] ) )
    if ( isset ( $padOccurStart [$pad] [$padOccur[$pad]] ) )
      include PAD . 'occurrence/end.php';

  if ( next ($padData [$pad]) !== FALSE )
    return include PAD . 'occurrence/occurrence.php';

  if ( $padWalk [$pad] == 'next' ) {
    include PAD . 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include PAD . 'occurrence/occurrence.php';
  }

  if ( $padStartBase [$pad] ) return include PAD . 'level/start_end/start2.php';
  if ( $padEndBase   [$pad] ) return include PAD . 'level/start_end/end2.php';

  $padOccur [$pad] = 99999;

  if ( $padWalk [$pad] == 'end' )
    include PAD . 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include PAD . 'callback/exit.php' ;

  include PAD . 'options/go/end.php';
  include PAD . 'level/pipes/after.php';  

  if ( $padInfo ) 
    include PAD . 'events/levelEnd.php';    
  
  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padLevel ( $padResult [$pad+1] );

?>