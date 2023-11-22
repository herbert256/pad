<?php

  if ( $padWalk [$pad] == 'start') {

    $padWalk [$pad] = 'end';
    
    include pad . 'tail/types/trace/entry/tag.php';
    
  } else {

    include pad . 'tail/types/trace/exit/tag.php';

  }

  return TRUE;
  
?>