<?php

  if ( $padWalk [$pad] == 'start' ) {
    include pad . 'isolate/start.php';
    $padWalk [$pad] = 'end';
  }
  else 
    include pad . 'isolate/end.php';

  return TRUE;

?>