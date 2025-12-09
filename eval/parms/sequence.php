<?php
 
  if ( file_exists ( PQ . "actions/types/$name.php" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/action.php';

  } elseif ( file_exists ( PT . "$name" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/sequence.php';

  }

?>