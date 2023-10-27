<?php

  if ( $padWalk [$pad] == 'start') {

    $padWalk [$pad] = 'end';
    
    include pad 'trace/start/tag.php';
    
  } else {

    include pad 'trace/end/tag.php';

  }

  return TRUE;
  
?>