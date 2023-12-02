<?php

  $padContent = $padBase [$pad];

  $padTagContent = '';

  ob_start();
  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";
  $padTagOb     = ob_get_clean();

  if ( padTail ) 
    include pad . 'tail/events/go.php';

  if     ( is_object   ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( is_resource ( $padTagResult ) ) $padTagResult = padToArray( $padTagResult );
  elseif ( $padTagResult === INF         ) $padTagResult = NULL;
  elseif ( $padTagResult === NAN         ) $padTagResult = NULL;

  if ( $padTagOb ) {
    $padTagContent .= $padTagOb;
    if ( padSingleValue ( $padTagResult ) )
      $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  if ( $padTagResult === NULL )
    return;

  include pad . 'level/flags.php';
  include pad . 'level/merge.php';

  $padBase [$pad] = $padContent;
  
?>