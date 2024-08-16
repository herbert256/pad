<?php

  if ( ! $GLOBALS ['padInfoTrace'] )
    return;

  if ( ! $padInfoTrace or ! $padInfoTraceResultLvl )
    return;

  if ( $padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

  if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'level', 'result',  $padResult [$pad] ); 
  
  if     ( $padTagOrg === NULL        ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'null'     );
  elseif ( $padTagOrg === FALSE       ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'false'    );
  elseif ( $padTagOrg === TRUE        ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'true'     );
  elseif ( $padTagOrg === INF         ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'inf'      );
  elseif ( $padTagOrg === NAN         ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'nan'      );
  elseif ( is_array    ( $padTagOrg ) ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'array'    );
  elseif ( is_object   ( $padTagOrg ) ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'object'   );
  elseif ( is_resource ( $padTagOrg ) ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'resource' );
  else                                  if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'result', 'string'   );

?>