<?php

  $padContent    = $padBase [$pad];
  $padTagContent = '';

  ob_start();

  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";
  
  if ( padXref )
    include pad . 'info/types/xref/items/result.php';

  $padTagContent .= ob_get_clean();

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  include pad . 'level/flags.php';

  if ( padXref )
    include pad . 'info/types/xref/items/flags.php';

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad], $padTag [$pad] );

  if ( padTagParm ('content') ) {
    $padContentOption = padTagParm('content');
    $padContentData   = $padContentStore [$padContentOption];
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad], $padContentOption );
  }

?>