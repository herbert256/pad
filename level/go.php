<?php

  $padTagCnt [$pad]++;

  $padTagContent = '';
  $padTagResult  = include pad . "types/" . $padType [$pad] . ".php";

  if     ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( $padTagResult === INF         ) $padTagResult = NULL;
  elseif ( $padTagResult === NAN         ) $padTagResult = NULL;

  if ( $padTagResult === NULL )
    return;

  if ( ! is_array ($padTagResult) and $padTagResult !== TRUE and $padTagResult !== FALSE ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  if ( $padTagContent )
    include pad . 'level/result.php';

?>