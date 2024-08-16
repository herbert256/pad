<?php
 
  global $padLog;
  
  if ( ! $GLOBALS ['padInfoTrack'] and ! $GLOBALS ['padInfoTrace'] ) 
    padInfoFile ( 
      "stats/$padLog.json", 
          [  'session' => $GLOBALS ['padSesID']         ?? '',
             'request' => $GLOBALS ['padReqID']         ?? '',
             'parent'  => $GLOBALS ['padRefID']         ?? '',
             'page'    => $GLOBALS ['padPage']          ?? '',
             'stop'    => $GLOBALS ['padStop']          ?? '',
             'stats'   => $GLOBALS ['padInfoStatsInfo'] ?? []
          ] );

  if ( $GLOBALS ['padInfoTrace'] and function_exists ( 'padInfoTrace') ) {
    padInfoTrace ( 'stats', 'system',   $GLOBALS ['padInfoStatsInfo'] ['user']     );
    padInfoTrace ( 'stats', 'user',     $GLOBALS ['padInfoStatsInfo'] ['system']   );
    padInfoTrace ( 'stats', 'duration', $GLOBALS ['padInfoStatsInfo'] ['duration'] );
  } 

?>