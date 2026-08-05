<?php

  // Sets up an action invoked from an expression rather than from a tag:
  // eval/parms/sequence.php parks the call in $pqSetAction/$pqSetParms and start/eval/
  // arrives here.
  //
  // The first parameter decides where the data comes from - an array is taken as the
  // sequence itself ($pqBuild 'given', $pqFixed), anything else names a stored sequence
  // to pull ($pqBuild 'pull', $pqPull). The remaining parameters are joined into the '|'
  // string the action handlers expect; array parameters are first parked in $pqStore
  // under a generated __action__<random> key so they can be referred to by name.

  $pqAction     = $pqSetAction;
  $pqActionParm = '';
  $pqParms      = [];
  $pqFirst      = pqActionArray ( $pqSetParms );

   if ( is_array ( $pqFirst )  ) {

    $pqBuild = 'given';
    $pqFixed = $pqFirst;

   } else {

    $pqBuild = 'pull';
    $pqPull  = $pqFirst;

  }

  foreach ( $pqSetParms as $pqV )

    if ( is_array ( $pqV ) ) {

      $pqT            = "__action__" . padRandomString ();
      $pqStore [$pqT] = array_values ( $pqV );
      $pqActionParm  .= $pqT . '|';

    } else

      $pqActionParm .= $pqV . '|';

?>