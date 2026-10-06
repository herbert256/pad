<?php

  // Applies a tag's data handling options - sort, first, page, dedup, group, ... - to
  // $padData [$pad], in the order in which they were written on the tag.
  //
  // Included by level/start.php once the level's data is known. Every parsed option
  // (padPrmKind 'option') that has a matching handling/types/<name>.php file runs that
  // file; levels without data, and sequence tags, which do their own handling, are
  // skipped. Each handler is handed $padHandName, $padHandParm (the raw option value) and
  // $padHandCnt (that value as a count, 1 when it is not a plain number - an evaluated
  // first=$n hands over an integer, so the test casts it to a string first). With the
  // negative option the handler runs between handling/negative/inits.php and exits.php,
  // which turns its selection inside out. A level the handling emptied shows its @else@.

  $padHandNegative = $padPrm [$pad] ['negative'] ?? 0 ;
  $padHandBefore   = count ( $padData [$pad] );
  $padHandPaged    = FALSE;

  // A level whose tag answered nothing has no rows to handle: its one occurrence is the
  // stand-in level/data.php gives it to render the @else@ branch once. Handled, a where read
  // fields no row has - strict mode failed {orders where='$status eq 1'}...@else@ on an empty
  // list with "there is no field named '$status'" - and a group folded the stand-in into a
  // group of one, so the @else@ branch read a count of 1.

  foreach ( ( $padElse [$pad] ? [] : $padParms [$pad] ) as $padHand ) {

    extract ( $padHand );

    // The modifiers are parameters of the handlers, not handlers of their own: negative is
    // read once above, and the others are read by the handler they belong to. Their files
    // under handling/types/ exist for the reference, and running one here - an empty
    // handler - would answer with an unchanged selection, which negative then inverts to
    // nothing at all.

        if ( ! count ( $padData [$pad] )                        ) continue;
    elseif ( $padPrmKind != 'option'                            ) continue;
    elseif ( in_array ( $padPrmName, [ 'negative', 'orderly', 'duplicates', 'atLeastOnce', 'left', 'right', 'both' ] ) ) {

      // A modifier never runs as a handler, but it was asked for, so it goes on the xref
      // record - before the sequence skip, because sequence tags read these same names.

      if ( $padInfo ) {
        $padHandName = $padPrmName;
        include PAD . 'events/handling.php';
      }

      continue;

    }
    elseif ( in_array ( $padPrmName, [ 'sum', 'avg', 'min', 'max' ] ) ) {

      // The aggregates of the group option are read by its handler. They go on the xref
      // record only beside a group: a sequence tag reads a min and a max of its own.

      if ( $padInfo and isset ( $padPrm [$pad] ['group'] ) and ! $padTagSeq [$pad] ) {
        $padHandName = $padPrmName;
        include PAD . 'events/handling.php';
      }

      continue;

    }
    elseif ( $padTagSeq [$pad]                                  ) continue;
    elseif ( ! file_exists ( PAD . "handling/types/$padPrmName.php" ) ) continue;

    $padHandName = $padPrmName;
    $padHandParm = $padPrmValue;
    $padHandCnt  = ( $padHandParm === TRUE or ! ctype_digit ( (string) $padHandParm ) ) ? 1 : $padHandParm;

    if ( $padInfo )
      include PAD . 'events/handling.php';

    // negative turns a selection inside out. group selects nothing - it folds the rows into
    // groups - and inverted it gave back the rows ungrouped, so a where=..., negative beside
    // it read fields the groups were to have; negative leaves it alone. sort, reverse and
    // shuffle select nothing either - they put the same rows in another order - and inverted,
    // every row they kept was dropped: {xs where='$n gt 2', sort='n', negative} rendered no
    // row at all, and a shuffle came back in its old order.

    $padHandInvert = ( $padHandNegative and ! in_array ( $padHandName, [ 'group', 'sort', 'reverse', 'shuffle' ] ) );

    // rows beside page, start or end, and end beside start, only size the window that other
    // handler cuts - their own files leave the rows alone then. Inverted, that unchanged
    // selection became no row at all: {xs page=2, rows=2, negative} and {xs start=2, end=4,
    // negative} rendered nothing, where the rows outside the window were asked for.

    if ( $padHandName == 'rows' and ( isset ( $padPrm [$pad] ['page'] ) or isset ( $padPrm [$pad] ['start'] )
                                      or isset ( $padPrm [$pad] ['end'] ) ) )
      $padHandInvert = FALSE;

    if ( $padHandName == 'end' and isset ( $padPrm [$pad] ['start'] ) )
      $padHandInvert = FALSE;

    if ( $padHandInvert )
      include PAD . "handling/negative/inits.php";

    include PAD . "handling/types/$padHandName.php";

    if ( $padHandInvert )
      include PAD . "handling/negative/exits.php";

  }

  // A paged level whose page handler never ran - it had no rows, or none were left when the
  // page option came to its turn - still books its pager: there is nothing to page through,
  // or, for a Select table cut by its SQL limit, a count to ask for.

  if ( isset ( $padPrm [$pad] ['page'] ) and ! $padHandPaged and ! $padTagSeq [$pad] )
    padPagerKeep ( 0 );

  // Rows the tag found, and the handling left none of them - a where nothing passed, a
  // first=0: the level has nothing to show, so it is treated as a tag that came back empty.
  // A notOk=, error= or else= option stands in with its content, as level/flags.php has it
  // for an empty tag; otherwise the @else@ branch shows; without either the level renders
  // nothing, as before. demand is an error unless an option stood in. level/flags.php ran
  // before the handling, while the rows were still there, so the documented
  // {users where='$premium eq 1' else="noPremiumUsers"} rendered nothing, a notOk= the same,
  // and a demanded tag whose where emptied it passed in silence.

  if ( $padHandBefore and ! count ( $padData [$pad] ) and ! $padTagSeq [$pad] ) {

    $padHandRecover = padTagParm ( 'notOk' ) ? 'notOk' : ( padTagParm ( 'error' ) ? 'error' : ( padTagParm ( 'else' ) ? 'else' : '' ) );

    if ( $padHandRecover ) {

      include PAD . "options/$padHandRecover.php";

      $padBase [$pad] = $padContent;

    } elseif ( $padFalse !== '' ) {

      $padElse [$pad] = TRUE;
      $padHit  [$pad] = FALSE;
      $padBase [$pad] = $padFalse;
      $padData [$pad] = padDefaultData ();

    }

    if ( ! $padHandRecover and padTagParm ( 'demand' ) )
      padError ( "Tag '" . $padTag [$pad] . "' carries demand and produced nothing" );

  }

?>
