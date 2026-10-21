<?php

  // Negative selection by value: keeps the entries of the pre-action snapshot
  // $pqActionStart the action's result $pqActionEnd does not account for. Used when the
  // action rebuilt the array, so its keys can no longer be matched.
  //
  // A value in the result accounts for as many entries of the snapshot as it stands there
  // times, the first ones of them: a value found anywhere in the result kept back every entry
  // of it, so a duplicate the action left out was not in the negative, and the result was
  // searched once for every entry - 20,000 terms took a tenth of a second, 40,000 four.

  $pqNegativeLeft = [];

  foreach ( $pqActionEnd as $padV ) {
    $pqNegativeKey = is_scalar ( $padV ) ? pqValueKey ( $padV ) : 'a' . serialize ( $padV );
    $pqNegativeLeft [$pqNegativeKey] = ( $pqNegativeLeft [$pqNegativeKey] ?? 0 ) + 1;
  }

  foreach ( $pqActionStart as $padK => $padV ) {
    $pqNegativeKey = is_scalar ( $padV ) ? pqValueKey ( $padV ) : 'a' . serialize ( $padV );
    if ( ( $pqNegativeLeft [$pqNegativeKey] ?? 0 ) > 0 )
      $pqNegativeLeft [$pqNegativeKey]--;
    else
      $pqResult [$padK] = $padV;
  }

?>
