<?php

  if ( $padOptions == 'start' ) {

    $padOptionsCallback [$pad] [] = 'trace';

    include pad . 'trace/trace/entry/option.php';

  }

  if ( $padOptions == 'callback' ) {

    include pad . 'trace/trace/exit/option.php';

  }

  return TRUE;
  
?>