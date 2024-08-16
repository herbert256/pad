<?php

  if ( $padInfTraceFlags )
   padTrace ( 'level', 'flags', 
      ' hit='     . $padHit   [$pad] . 
      ' else='    . $padElse  [$pad] . 
      ' null='    . $padNull  [$pad] . 
      ' array='   . $padArray [$pad] . 
      ' default=' . $padDefault  [$pad] . 
      ' count='   . count ( $padData [$pad] )
    );

  if ( $padNull [$pad]  )padTrace ( 'flag', 'null',  $padNull [$pad]  );
  if ( $padElse [$pad]  )padTrace ( 'flag', 'else',  $padElse [$pad]  );
  if ( $padHit [$pad]   )padTrace ( 'flag', 'hit',   $padHit [$pad]   );
  if ( $padArray [$pad] )padTrace ( 'flag', 'array', $padArray [$pad] );

?>