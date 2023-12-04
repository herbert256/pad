<?php

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad], $padTag [$pad] );

  if ( padTagParm ('content') ) {
    $padContentOption = padTagParm('content');
    $padContentData   = $padContentStore [$padContentOption];
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad], $padContentOption );
  }

?>