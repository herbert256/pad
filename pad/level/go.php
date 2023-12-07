<?php

  $padContent    = $padBase [$pad];
  $padTagContent = '';

  ob_start();
  $padTagResult = include pad . "types/" . $padType [$pad] . ".php";
  $padTagContent .= ob_get_clean();

  if ( padInfo ) 
    include pad . 'info/events/go.php';

  if ( padSingleValue ( $padTagResult ) ) {
    $padTagContent .= $padTagResult;
    $padTagResult = TRUE;
  }

  include pad . 'level/flags.php';
  include pad . 'level/merge.php';

  $padBase [$pad] = $padContent;
  
?>