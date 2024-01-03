<?php

  $padContent    = $padBase [$pad];
  $padTagContent = '';

  ob_start();
  $padParm        = $padOpt [$pad] [1] ?? '';
  $padTagResult   = include pad . "types/" . $padType [$pad] . ".php";
  $padTagOrg      = $padTagResult;
  $padTagContent .= ob_get_clean();

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  include pad . 'level/flags.php';

  if ( padInfo )
    include pad . 'info/events/go.php';

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad], $padTag [$pad] );

  if ( padTagParm ('content') ) {
    $padContentOption = padTagParm('content');
    $padContentData   = $padContentStore [$padContentOption];
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad], $padContentOption );
  }

?>