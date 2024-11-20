<?php

  if ( $padWalk [$pad] == 'start' ) { 

    include 'info/start/tag.php';
    include 'config/info/trace.php';
    include 'info/types/trace/start.php';
    
    $padWalk [$pad] = 'end';

  } else {

    include 'info/types/trace/end.php';
    include 'info/end/tag.php';

  }

  return TRUE;

?>