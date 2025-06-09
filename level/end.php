<?php

  if ( isset ( $padOccurStart [$pad] ) )
    if ( isset ( $padOccurStart [$pad] [$padOccur[$pad]] ) )
      include 'occurrence/end.php';

  if ( next ($padData [$pad]) !== FALSE )
    return include 'occurrence/occurence.php';

  if ( $padWalk [$pad] == 'next' ) {
    include 'walk/next.php';
    if ( $padWalk [$pad] == 'next' )
      return include 'occurrence/occurence.php';
  }

  if ( $padStartBase [$pad] ) return include 'level/start_end/start2.php';
  if ( $padEndBase   [$pad] ) return include 'level/start_end/end2.php';

  $padOccur [$pad] = 99999;

  if ( $padWalk [$pad] == 'end' )
    include 'walk/end.php';

  if ( isset($padPrm [$pad] ['callback']) and ! isset($padPrm [$pad] ['before']) )
    include 'callback/exit.php' ;

  include 'options/go/end.php';
 
  if ( $padInfo ) 
    include 'events/levelEnd.php';    
  
  padResetLvl ($pad);

  $pad--;

  if ( $pad >= 0 )
    padLevel ( $padResult [$pad+1] );

?>