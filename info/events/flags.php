<?php

  if ( $GLOBALS ['padInfoTrace'] ) 
    if ( $padInfoTraceFlags )
      padInfoTrace ( 'level', 'flags', 
      ' hit='     . $padHit   [$pad] . 
      ' else='    . $padElse  [$pad] . 
      ' null='    . $padNull  [$pad] . 
      ' array='   . $padArray [$pad] . 
      ' default=' . $padDefault  [$pad] . 
      ' count='   . count ( $padData [$pad] )
    );

  if ( $padNull [$pad]  ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'flag', 'null',  $padNull [$pad]  );
  if ( $padElse [$pad]  ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'flag', 'else',  $padElse [$pad]  );
  if ( $padHit [$pad]   ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'flag', 'hit',   $padHit [$pad]   );
  if ( $padArray [$pad] ) if ( $GLOBALS ['padInfoTrace'] ) padInfoTrace ( 'flag', 'array', $padArray [$pad] );

?>