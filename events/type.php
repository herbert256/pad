<?php

  if ( $GLOBALS ['padInfoXref'] ) {
    if     ( $padTagResult === NULL        ) padInfoXref ( 'result', 'null'     );
    elseif ( $padTagResult === FALSE       ) padInfoXref ( 'result', 'false'    );
    elseif ( $padTagResult === TRUE        ) padInfoXref ( 'result', 'true'     );
    elseif ( $padTagResult === INF         ) padInfoXref ( 'result', 'inf'      );
    elseif ( $padTagResult === NAN         ) padInfoXref ( 'result', 'nan'      );
    elseif ( is_array    ( $padTagResult ) ) padInfoXref ( 'result', 'array'    );
    elseif ( is_object   ( $padTagResult ) ) padInfoXref ( 'result', 'object'   );
    elseif ( is_resource ( $padTagResult ) ) padInfoXref ( 'result', 'resource' );
    else                                     padInfoXref ( 'result', 'string'   );
    }

  if ( ! $padInfoTrace or ! $padInfoTraceResultLvl )
    return;

  if ( $padInfoTraceDouble and $padInfoTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

  padInfoTrace ( 'level', 'result',  $padResult [$pad] ); 
  
?>