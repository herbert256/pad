<?php

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
