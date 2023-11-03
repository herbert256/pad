<?php

  if ( $padWalk [$pad] == 'start') {

    $padWalk [$pad] = 'end';
    
    include pad . 'trace/entry/tag.php';
    
  } else {

    include pad . 'trace/lib/exits.php';

  }

  return TRUE;
  
?>