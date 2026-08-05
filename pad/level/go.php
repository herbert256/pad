<?php

  // Runs the tag itself: includes types/<type>.php and collects what it produces.
  //
  // Reached through try/try.php ($padTry = 'level/go') from level/start.php and
  // walk/next.php. The handler sees $padParm (first parameter), $padContent (the text
  // between the tags) and $padGetName; its return value lands in $padTagResult and anything
  // it echoes in $padTagContent. Afterwards level/flags.php turns the result into this
  // level's hit/null/else/array flags, a leading '?' in the option text runs
  // level/ternary.php, and the dump and content= options are applied.

  $padParm       = $padOpt [$pad] [1] ?? '';
  $padContent    = $padBase [$pad];
  $padTagContent = '';

  ob_start();
  $padGetName     = $padTag [$pad];
  $padTagResult   = include PAD . "types/" . $padType [$pad] . ".php";
  $padTagContent .= ob_get_clean();

  if ( $padNextPadLevel )
    return;

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  include PAD . 'level/flags.php';

  if ( str_starts_with ( $padOpt [$pad] [0], '?' ) )
    include PAD . 'level/ternary.php';

  if ( $padInfo )
    include PAD . 'events/type.php';

  if ( $padInfo )
    include PAD . 'events/go.php';

  if ( padTagParm ('dump') )
    include PAD . 'options/dump.php';

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad] );

  if ( padTagParm ('content') ) {
    $padContentData = include PAD . 'options/content.php';
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad] );
  }

?>