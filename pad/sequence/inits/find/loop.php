<?php

  // Scans the tag's options for a sequence name, in two passes so a stored sequence always
  // beats a type of the same name however they were written.
  //
  // The first pass takes the first option naming an entry in $pqStore as the pull. The
  // second, only if nothing was pulled and no type is known yet, takes the first option
  // naming a directory in types/ as $pqSeq with its value as $pqParm - this is what makes
  // {sequence prime, rows=10} work - and records the name in $pqDone so the later plays and
  // actions passes do not claim it a second time.

  foreach ( $padParms [$pad] as $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqPull and isset ( $pqStore [$padPrmName] ) )
      $pqPull = $padPrmName;

  }

  // The option taken is remembered by its place, $pqSeqAt, so the plays pass skips that one
  // option and no other of the same name - a later square in {sequence square, make, square}
  // is the play it is written as, where every option of the name was skipped and the run gave
  // the squares themselves.

  foreach ( $padParms [$pad] as $pqParmsAt => $padParmsOne )  {

    extract ( $padParmsOne );

    if ( ! $pqPull and ! $pqSeq and pqSeq ( $padPrmName ) ) {

      $pqDone [] = $padPrmName;
      $pqSeqAt   = $pqParmsAt;
      $pqSeq     = $padPrmName;
      $pqParm    = $padPrmValue;

    }

  }

?>
