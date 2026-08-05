<?php

  // Splits the level's template text on an @else@ marker belonging to this tag.
  //
  // Text before the marker stays in $padBase [$pad] as the true branch; text after it goes
  // to $padFalse, which level/base.php uses when the tag turns out null or empty. A marker
  // is only this level's if every open/close tag pair before it - the pairs found in the
  // text plus this tag itself - is balanced, so @else@ inside a nested pair is skipped.

  $padOpenClose = padOpenCloseList ( $padBase [$pad] ) ;

  if ( $padGiven [$pad] )
    $padOpenClose [ $padType [$pad] . ':' . $padTag [$pad] ] = TRUE;
  else
    $padOpenClose [ $padTag [$pad] ] = TRUE;

  $padPos = strpos ( $padBase [$pad], '@else@');

  while ( $padPos !== FALSE) {

    if  ( padOpenCloseCount ( substr ( $padBase [$pad], 0, $padPos ), $padOpenClose) ) {
      $padFalse = substr ( $padBase [$pad], $padPos+6  );
      $padBase  [$pad] = substr ( $padBase [$pad], 0, $padPos );
      if ( $padInfo )
        include PAD . 'events/else.php';
      return;
    }

    $padPos = strpos ( $padBase [$pad], '@else@', $padPos+1);

  }

?>