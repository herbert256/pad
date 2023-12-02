<?php

if ( 1 <> 1 ) {

  if ( $padTagContent )
    padContentMerge ( $padContent, $padFalse, $padTagContent, $padHit [$pad] );

} else {

  padBeforeAfter ( $padTagContent, $padTagTrue, $padTagFalse, '@else@' );

  if ( $padTagContent )
    if ( $padHit [$pad] ) $padContent = padContent ( $padContent,      $padTagTrue  );
    else                  $padFalse   = padContent ( $padFalse, $padTagFalse );

}

  if ( padTagParm ('content') ) {
    $padContentOption = padTagParm('content');
    $padContentData   = $padContentStore [$padContentOption];
    padContentMerge ( $padContent, $padFalse, $padContentData, $padHit [$pad] );
  }


?>