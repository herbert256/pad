<?php

  if ( $padOptions == 'start' ) {

    $padOptionsCallback [$pad] [] = 'trace';

    include pad . 'tail/types/trace/entry/option.php';

  }

  if ( $padOptions == 'callback' ) {

    include pad . 'tail/types/trace/exit/option.php';

  }

  return TRUE;
  
?>