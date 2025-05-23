<?php

  $padParm       = $padOpt [$pad] [1] ?? '';
  $padContent    = $padBase [$pad];
  $padTagContent = '';

  ob_start();
  $padTagResult   = include "types/" . $padType [$pad] . ".php";
  $padTagContent .= ob_get_clean();

  if ( $padInfo )
    include 'events/type.php';

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  include 'level/flags.php';

  if ( $padInfo )
    include 'events/go.php';

  if ( padTagParm ('dump') )
    include 'options/dump.php';

  if ( $padTagContent ) 
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad] );

  if ( padTagParm ('content') ) {
    $padContentData = include 'options/content.php';
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad] );
  }


?>