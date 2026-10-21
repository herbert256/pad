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

  // The one empty row is a stand-in, not a row the tag found: handling/handling.php leaves it
  // alone. It knew a stand-in by $padElse, which is cleared just above, so a where, sort or
  // group beside else=, notOk=, error= or null= ran on it - and under the strict check failed
  // {e where='$n gt 1', else='nobody'} on an empty list with "there is no field named '$n'".
  // Kept as the level's id: a level that later takes this depth is another one.

  $padHandStoodIn [$pad] = $padLevelId [$pad];

  if ( isset ( $padPrm [$pad] ['content']) )
    unset ( $padPrm [$pad] ['content'] );

  return TRUE;

?>
