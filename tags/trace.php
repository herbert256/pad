<?php

  if ( $padWalk [$pad] == 'start' ) { 

    include '/pad/info/start/tag.php';
    include '/pad/config/info/trace.php';
    include '/pad/info/types/trace/start.php';
    
    $padWalk [$pad] = 'end';

  } else {

    include '/pad/info/types/trace/end.php';
    include '/pad/info/end/tag.php';

  }

  return TRUE;

?>