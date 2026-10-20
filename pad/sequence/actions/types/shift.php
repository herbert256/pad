<?php

  // shift, shift=N - destructive: hands back the first N entries as the result and at the
  // same time removes them from the stored sequence named by $pqPull, setting
  // $pqStoreUpdated so exits/store/set.php writes the shortened store back. pop delegates
  // here and works from the other end. If the store holds no more than N it is emptied
  // while the whole result is still returned; with nothing pulled only the result is cut.

  // Lengths and offsets are counted from the front, as for last: a pop of 0 multiplied out
  // to -0, which took every term and emptied the store.

  if ( count($pqResult) > $pqActionCnt )
    if ( $pqAction == 'shift')
      $pqResult = array_slice ( $pqResult, 0, $pqActionCnt );
    else
      $pqResult = array_slice ( $pqResult, count ( $pqResult ) - $pqActionCnt );

  // What leaves the store is what the tag shows: the store entries of the terms taken. The
  // store lost its own first or last N whatever the run made of it before, so {pull:a reverse,
  // shift=2} showed 6 5 and took 1 and 2 out, and {pull:a keep='even', shift=2} showed 2 4 and
  // took 1 and 2. A term the build made from an entry still has the 'x' key actions/assoc.php
  // gave it, and its entry is the store position the build recorded, $pqResultAt; when an
  // action before rebuilt every key - combine, merge - each value takes the first entry
  // holding it that is still in the store. A term from elsewhere, a store append= added,
  // takes nothing. Several destructive actions in one run take from the store in turn,
  // $pqStoreTakenAt.

  // A store that was never pushed has nothing to take out: the pull build has said so under
  // the strict check, and under the lenient walk the shift read the undefined key and ended
  // the request - only the result is cut then, as with nothing pulled.

  if ( ! $pqPull or ! isset ( $pqStore [$pqPull] ) )
    return;

  $pqStoreUpdated = TRUE;
  $pqStoreTakenAt = $pqStoreTakenAt ?? [];

  $pqShiftKeyed = FALSE;

  foreach ( $pqResult as $padK => $padV )
    if ( is_string ( $padK ) and str_starts_with ( $padK, 'x' ) )
      $pqShiftKeyed = TRUE;

  foreach ( $pqResult as $padK => $padV ) {

    if ( $pqShiftKeyed ) {

      if ( ! is_string ( $padK ) or ! str_starts_with ( $padK, 'x' ) )
        continue;

      $pqShiftAt = $pqResultAt [ (int) substr ( $padK, 1 ) ] ?? NULL;

      if ( $pqShiftAt !== NULL )
        $pqStoreTakenAt [$pqShiftAt] = TRUE;

      continue;

    }

    foreach ( $pqPulled as $pqShiftAt => $pqShiftValue )
      if ( ! isset ( $pqStoreTakenAt [$pqShiftAt] ) and pqValueKey ( $pqShiftValue ) === pqValueKey ( $padV ) ) {
        $pqStoreTakenAt [$pqShiftAt] = TRUE;
        break;
      }

  }

  $pqStore [$pqPull] = [];

  foreach ( $pqPulled as $pqShiftAt => $pqShiftValue )
    if ( ! isset ( $pqStoreTakenAt [$pqShiftAt] ) )
      $pqStore [$pqPull] [] = $pqShiftValue;

?>
