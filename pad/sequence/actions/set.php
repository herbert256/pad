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

  // A number is a value to act on, a list of one: no store is named by a number, and 5 |
  // sequence:sum ended on "there is no stored sequence named '5'".

  if ( is_int ( $pqFirst ) or is_float ( $pqFirst ) or ( is_string ( $pqFirst ) and is_numeric ( $pqFirst ) ) )
    $pqFirst = [ $pqFirst ];

   if ( is_array ( $pqFirst )  ) {

    // The values acted on are values: a list among them ended the request on "Array to
    // string conversion" in dedup, distinct, merge and the rest.

    foreach ( $pqFirst as $pqFirstOne )
      if ( ! is_scalar ( $pqFirstOne ) ) {
        padError ( "$pqAction() acts on a list of values, not a list of lists" );
        $pqFirst = array_filter ( $pqFirst, 'is_scalar' );
        break;
      }

    $pqBuild = 'given';
    $pqFixed = $pqFirst;

   } else {

    $pqBuild = 'pull';
    $pqPull  = $pqFirst;

  }

  foreach ( $pqSetParms as $pqV )

    if ( is_array ( $pqV ) ) {

      $pqT = pqActionList ( $pqAction, $pqV );

      if ( $pqT !== NULL )
        $pqActionParm .= $pqT . '|';

    } else

      $pqActionParm .= $pqV . '|';

?>
