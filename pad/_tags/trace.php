<?php

  if ( $padWalk [$pad] == 'start') {

    $padWalk [$pad] = 'end';
    
    include pad . 'trace/trace/entry/tag.php';
    
  } else {

    include pad . 'trace/trace/exit/tag.php';

  }

  return TRUE;
  
?>