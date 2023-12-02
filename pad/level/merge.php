<?php

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse [$pad], $padTagContent, $padHit [$pad] );

  if ( padTagParm ('content') ) {
    $padContentOption = padTagParm('content');
    $padContentData   = $padContentStore [$padContentOption];
    padContentMerge ( $padContent, $padFalse [$pad], $padContentData, $padHit [$pad] );
  }

?>