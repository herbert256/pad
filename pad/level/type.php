<?php

  // Resolves the tag name to a type, that is, which types/<type>.php will run it.
  //
  // A bare name is looked up in priority order by padTypeTag() - app tag, _common tag,
  // built-in tag, one of the stores, custom function. A prefixed name such as app:x or
  // data:x is checked against that one type by padTypeTagCheck() and, failing that, tried
  // as a sequence type/action combination by padTypeSeq(). Leaves $padTypeCheck (the bare
  // name), $padTypePrefix, $padTypeGiven, $padTypeSeq and $padTypeResult; a FALSE
  // $padTypeResult means the tag is unknown and level/no.php deals with it.

  $padTypeExplode = padExplode ( $padTagCheck, ':' ) ;
  $padTypeSeq     = '';

  if ( count ($padTypeExplode) == 1 ) {

    $padTypeGiven  = FALSE;
    $padTypePrefix = '';
    $padTypeCheck  = $padTagCheck;
    $padTypeResult = padTypeTag ( $padTypeCheck );

  } else {

    $padTypeGiven  = TRUE;

    $padTypePrefix = $padTypeExplode [0];
    $padTypeCheck  = $padTypeExplode [1];

    $padTypeResult = padTypeTagCheck ( $padTypePrefix, $padTypeCheck );

    if ( ! $padTypeResult )
      $padTypeResult = padTypeSeq ( $padTypePrefix, $padTypeCheck );

  }

?>