<?php

  if ( file_exists ( PAD . "sequence/actions/types/$name.php" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/action.php';

  } elseif ( file_exists ( PAD . "sequence/types/$name" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include PQ . 'start/eval/sequence.php';

  }

?>
