<?php

  $padStartType = 'pad';

  if ( $GLOBALS ['padXref'] ?? '' ) 
    padXref ( 'start', $padStartType );

                      include pad . 'inits/inits.php';
  while ( $pad >= 0 ) include pad . 'level/level.php'; 
                      include pad . 'exits/exits.php';

?>