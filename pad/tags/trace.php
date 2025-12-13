<?php

  if ( $padWalk [$pad] == 'start' ) { 

    include PAD . 'info/start/tag.php';
    include PAD . 'config/info/trace.php';
    include PAD . 'info/types/trace/start.php';
    
    $padWalk [$pad] = 'end';

  } else {

    include PAD . 'info/types/trace/end.php';
    include PAD . 'info/end/tag.php';

  }

  return TRUE;

?>