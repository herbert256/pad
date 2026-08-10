<?php

  // Shared body of the else, null and notOk options; the caller puts the option name in
  // $padReset.
  //
  // Resolves that option's value to text through get/content.php into $padContent, then turns
  // the failed level back into an ordinary hit: clears $padNull, $padElse and $padArray, sets
  // $padHit, restores default data so nothing is iterated, and drops any content option so it
  // is not merged on top of the replacement. Returns TRUE, which the callers pass on.

  $padGetName = padTagParm ( $padReset );
  $padContent = include PAD . 'get/content.php';

  $padTagResult  = TRUE;
  $padTagContent = '';

  $padNull  [$pad] = FALSE;
  $padElse  [$pad] = FALSE;
  $padHit   [$pad] = TRUE;
  $padArray [$pad] = FALSE;

  $padData  [$pad] = padDefaultData ();

  if ( isset ( $padPrm [$pad] ['content']) )
    unset ( $padPrm [$pad] ['content'] );

  return TRUE;

?>