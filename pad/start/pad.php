<?php

  if ( $GLOBALS ['padGlobals'] )
    foreach ( $GLOBALS as $padLoopK => $padLoopV )
      global $$padLoopK;

  include pad . 'start/backup.php';

  if ( $padIsolate )
    include pad . 'start/isolate.php';
 
  include pad . 'inits/level.php'; 

  if ( $padBuild <> 'page' ) {
    $padBase [$pad] = $padCode;    
    include pad . 'occurrence/start.php'; 
  } else
    include pad . 'build/build.php'; 

  include pad . 'start/level.php'; 
  include pad . 'start/restore.php';
  
  return $padPad [$pad+1];

?>