<?php

  // Fallback when nothing named a sequence type or a store: plain 'loop', which just counts
  // from $pqFrom, so a bare {sequence rows=5} still produces something.
  //
  // That is the right answer only when nothing was named at all. A name that was given and
  // matched no store, no type and no action is a mistake - a typo, or a store that was never
  // pushed - and counting 1, 2, 3 in its place looks enough like a sequence to be taken for
  // one, so it is reported rather than quietly answered.
  //
  // Three places can carry such a name: the first parameter, if nothing claimed it; the tag
  // itself, when it is not one of the entry points in start/tags/; and a bare option, which
  // is how {sequence prime} arrives and so how {sequence prmie} arrives too. An option only
  // counts as a name when nothing else has a use for it - the check is the one
  // exits/store/check.php makes of a store name, run the other way round.
  //
  // A run that found an action but no sequence is not a mistake: {sum from=31, to=40} means
  // the action over the counting sequence this file falls back to, so nothing is reported.
  //
  // Only reached when the run resolved to nothing at all, so a tag that named a real
  // sequence never comes past here. The fallback is applied afterwards either way, so an
  // application whose error action lets the request carry on gets the old behaviour rather
  // than a run with no sequence at all.

  $pqDefaultName = '';

  if     ( $pqAction                                                    ) $pqDefaultName = '';
  elseif ( $pqFindParm                                                  ) $pqDefaultName = $pqFindParm;
  elseif ( $pqTag and ! file_exists ( PQ . "start/tags/$pqTag.php" )     ) $pqDefaultName = $pqTag;

  else foreach ( $padParms [$pad] as $pqDefaultOne ) {

    extract ( $pqDefaultOne );

        if ( $padPrmKind != 'option'                                    ) continue;
    elseif ( in_array    ( $padPrmName, $pqDone )                       ) continue;
    elseif ( pqPlay      ( $padPrmName )                                ) continue;
    elseif ( pqAction    ( $padPrmName )                                ) continue;
    elseif ( file_exists ( PQ  . "options/types/$padPrmName.php" )      ) continue;
    elseif ( file_exists ( PAD . "options/$padPrmName.php" )            ) continue;
    elseif ( file_exists ( PAD . "handling/types/$padPrmName.php" )     ) continue;

    $pqDefaultName = $padPrmName;
    break;

  }

  if ( $pqDefaultName )
    padError ( "Sequence '$pqDefaultName' is not a sequence type, a store or an action" );

  $pqSeq  = 'loop';
  $pqParm = '';

?>