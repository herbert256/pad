<?php

  if ( ! $padInfTraceResultLvl )
    return;

  if ( $padInfTraceDouble and $padInfTraceContent and $padBase [$pad] == $padResult [$pad] )
    return;

 padTrace ( 'level', 'result',  $padResult [$pad] ); 

  return;
  
  if     ( $padTagOrg === NULL        )padTrace ( 'result', 'null'     );
  elseif ( $padTagOrg === FALSE       )padTrace ( 'result', 'false'    );
  elseif ( $padTagOrg === TRUE        )padTrace ( 'result', 'true'     );
  elseif ( $padTagOrg === INF         )padTrace ( 'result', 'inf'      );
  elseif ( $padTagOrg === NAN         )padTrace ( 'result', 'nan'      );
  elseif ( is_array    ( $padTagOrg ) )padTrace ( 'result', 'array'    );
  elseif ( is_object   ( $padTagOrg ) )padTrace ( 'result', 'object'   );
  elseif ( is_resource ( $padTagOrg ) )padTrace ( 'result', 'resource' );
  else                                 padTrace ( 'result', 'string'   );

?>