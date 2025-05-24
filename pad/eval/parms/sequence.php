<?php
 
  if ( file_exists ( "sequence/actions/types/$name.php" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include 'sequence/start/direct/action.php';

  } elseif ( file_exists ( "sequence/types/$name" ) ) {

    $pqSetAction = $name;
    $pqSetParms  = $parm;

    return include 'sequence/start/direct/sequence.php';

  }

?>