<?php
 
  if ( $padWalk [$pad] == 'start' ) {

    padBuildSet ();

    include pad . 'page/start.php';
    include pad . 'level/setup.php';
    include pad . 'build/build.php';    

    $pad--;

    include pad . 'page/end.php'; 

    $padWalk [$pad] = 'end';

    return $padBase [$pad+1];

  } else {

    padBuildReset ();

    return TRUE;

  }

?>