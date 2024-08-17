<?php

  if ( $GLOBALS ['padInfoTrace'] and $padInfoTraceFlags )
    padInfoTrace ( 'level', 'flags', 
      ' hit='     . $padHit   [$pad] . 
      ' else='    . $padElse  [$pad] . 
      ' null='    . $padNull  [$pad] . 
      ' array='   . $padArray [$pad] . 
      ' count='   . count ( $padData [$pad] )
    );

  if ( $GLOBALS ['padInfoXref'] ) {
    if ( $padNull [$pad]  ) padInfoXref ( 'flag', 'null',  $padNull [$pad]  );
    if ( $padElse [$pad]  ) padInfoXref ( 'flag', 'else',  $padElse [$pad]  );
    if ( $padHit [$pad]   ) padInfoXref ( 'flag', 'hit',   $padHit [$pad]   );
    if ( $padArray [$pad] ) padInfoXref ( 'flag', 'array', $padArray [$pad] );
  }

?>