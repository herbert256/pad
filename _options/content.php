<?php

  $padContentGo = padTagParm('content');
  
  if ( isset ( $padContentStore [$padContentGo] ) )
    return $padContentStore [$padContentGo];

  $padIncPage = padInclFileName ( $padContentGo );

  if ( $padIncPage ) {
    $padIncPage = str_replace (padApp, '',  $padIncPage);
    return include pad . '_tags/go/include.php';
  }

  return padMakeContent ( $padContentGo );

?>