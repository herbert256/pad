<?php

  $padSeqFlagGo = FALSE;
  
  return;
  
  if ( ! $padSeqFlag ) 
    return;
    
  foreach ( $padSeqPlays as $padSeqPlay )
    if ( $padSeqPlay ['padSeqPlay'] == 'flag' )
      return;
         
  if ( in_array ( $padSeqBuild, ['start','store','pull'] ) )
    include 'sequence/inits/flag/play.php';
  else
    include 'sequence/inits/flag/check.php';

?>