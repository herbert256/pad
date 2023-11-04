<?php

  if ( $padOptions == 'start' ) {

    $padOptionsCallback [$pad] [] = 'trace';

    include pad . 'trace/entry/option.php';

  }

  if ( $padOptions == 'callback' ) {

    include pad . 'trace/exit/option.php';

  }

  return TRUE;
  
?>