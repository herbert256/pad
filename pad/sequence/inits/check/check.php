<?php

  // Works out which play mode - make, keep, remove or flag - the tag is asking for, and turns
  // each one found into a build strategy.
  //
  // A mode can arrive four ways: as an option ({sequence prime, keep}), as the resolved type,
  // as the prefix, or as the tag name ({keep:prime}, {prime keep}, {keep prime}). All four
  // are read here, then each of the four modes is offered in turn to inits/check/go.php,
  // which ignores the ones that were not asked for.

  $pqMake   = $padPrm [$pad] ['make']     ?? '';
  $pqFlag   = $padPrm [$pad] ['flag']     ?? '';
  $pqKeep   = $padPrm [$pad] ['keep']     ?? '';
  $pqRemove = $padPrm [$pad] ['remove']   ?? '';

  // The four mode words are read right here when the tag carries them as options -
  // marked read, for the strict unread-option sweep.

  foreach ( [ 'make', 'keep', 'remove', 'flag' ] as $pqCheckOne )
    if ( isset ( $padPrm [$pad] [$pqCheckOne] ) )
      padDone ( $pqCheckOne );

  if ( $pqType == 'make'   or $pqPrefix == 'make'   or $pqTag == 'make'   ) $pqMake   = TRUE;
  if ( $pqType == 'keep'   or $pqPrefix == 'keep'   or $pqTag == 'keep'   ) $pqKeep   = TRUE;
  if ( $pqType == 'remove' or $pqPrefix == 'remove' or $pqTag == 'remove' ) $pqRemove = TRUE;
  if ( $pqType == 'flag'   or $pqPrefix == 'flag'   or $pqTag == 'flag'   ) $pqFlag   = TRUE;

  $pqCheck = 'make';   include PQ . "inits/check/go.php";
  $pqCheck = 'keep';   include PQ . "inits/check/go.php";
  $pqCheck = 'remove'; include PQ . "inits/check/go.php";
  $pqCheck = 'flag';   include PQ . "inits/check/go.php";

?>