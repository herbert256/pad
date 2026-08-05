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

        if ( ! count ( $padData [$pad] )                        ) continue;
    elseif ( $padTagSeq [$pad]                                  ) continue;
    elseif ( $padPrmKind != 'option'                            ) continue;
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