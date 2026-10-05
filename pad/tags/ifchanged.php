<?php

  // {ifchanged $customer} ... {/ifchanged}: renders its content when the value differs from
  // what it was the previous time this tag ran in the enclosing loop - a heading per group
  // in a sorted list. The first time it always renders; when the value is the same, the
  // @else@ branch renders instead, if there is one. Several values - {ifchanged $year,
  // $month} - change when any of them does.
  //
  // The memory belongs to one run of the enclosing loop - the nearest level that is not an
  // if, a case or another ifchanged - so a loop that runs again starts afresh, and to this
  // tag's place in it: the n-th ifchanged an occurrence meets is compared with the n-th of
  // the occurrence before.

  if ( ! $padPair [$pad] and $padCheckSyntax )
    padError ( "the pair {ifchanged} never closes" );

  if ( trim ( $padOpt [$pad] [0] ?? '' ) === '' and $padCheckSyntax )
    padError ( "the {ifchanged} needs a value - {ifchanged \$customer}" );

  $padIfChangedLoop = $pad - 1;

  while ( $padIfChangedLoop > 0 and in_array ( $padTag [$padIfChangedLoop], [ 'if', 'case', 'ifchanged' ] ) )
    $padIfChangedLoop--;

  $padIfChangedRun  = ( $padLevelId [$padIfChangedLoop] ?? 0 ) . '/' . ( $padOccur [$padIfChangedLoop] ?? 0 );
  $padIfChangedLvl  = $padLevelId [$padIfChangedLoop] ?? 0;

  $padIfChangedSeq [$padIfChangedRun] = ( $padIfChangedSeq [$padIfChangedRun] ?? 0 ) + 1;

  $padIfChangedKey  = $padIfChangedLvl . '/' . $padIfChangedSeq [$padIfChangedRun];
  $padIfChangedNow  = serialize ( array_slice ( $padOpt [$pad], 1 ) );

  $padIfChangedSame = ( isset ( $padIfChangedLast [$padIfChangedKey] )
                        and $padIfChangedLast [$padIfChangedKey] === $padIfChangedNow );

  $padIfChangedLast [$padIfChangedKey] = $padIfChangedNow;

  return ! $padIfChangedSame;

?>
