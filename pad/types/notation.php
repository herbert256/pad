<?php

  // notation:kind selects a registered kind from its subsystem. The type handler
  // runs the subsystem with the kind in the tag name and its own parameters unchanged.


  $padNotationKind  = $padTag [$pad];
  $padNotationGiven = (bool) array_filter ( $padParms [$pad], fn ( $one ) => $one ['padPrmKind'] == 'parm' and $one ['padPrmName'] == 1 );

  $padTagGo = PAD . 'subs/notations/notation';

  return include PAD . 'types/_go/tag.php';

?>
