<?php

  // Applies a tag's data handling options - sort, first, page, dedup, slice, ... - to
  // $padData [$pad], in the order in which they were written on the tag.
  //
  // Included by level/start.php once the level's data is known. Every parsed option
  // (padPrmKind 'option') that has a matching handling/types/<name>.php file runs that
  // file; levels without data, and sequence tags, which do their own handling, are
  // skipped. Each handler is handed $padHandName, $padHandParm (the raw option value) and
  // $padHandCnt (that value as a count, 1 when it is not a plain number). With the
  // negative option the handler runs between handling/negative/inits.php and exits.php,
  // which turns its selection inside out.

  $padHandNegative = $padPrm [$pad] ['negative'] ?? 0 ;

  foreach ( $padParms [$pad] as $padHand ) {

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
    elseif ( $padTagSeq [$pad]                                  ) continue;
    elseif ( ! file_exists ( PAD . "handling/types/$padPrmName.php" ) ) continue;

    $padHandName = $padPrmName;
    $padHandParm = $padPrmValue;
    $padHandCnt  = ( $padHandParm === TRUE or ! ctype_digit ( $padHandParm ) ) ? 1 : $padHandParm;

    if ( $padInfo )
      include PAD . 'events/handling.php';

    if ( $padHandNegative )
      include PAD . "handling/negative/inits.php";

    include PAD . "handling/types/$padHandName.php";

    if ( $padHandNegative )
      include PAD . "handling/negative/exits.php";

  }

?>