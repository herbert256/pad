<?php

  if ( $padWalk [$pad] == 'start' ) { 

    include '/pad/info/tag/start.php';
    include '/pad/config/info/trace.php';
    include '/pad/info/trace/start.php';
    
    $padWalk [$pad] = 'end';

  } else {

    include '/pad/info/trace/end.php';
    include '/pad/info/tag/end.php';

  }

  return TRUE;

?>