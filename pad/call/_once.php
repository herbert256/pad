<?php

  global $padInfo;

  include PAD . 'call/_init.php';

  if ( file_exists ( $padCall ) ) {

    if ( $padInfo )
      include PAD . 'events/callStart.php';

    $padTry = 'call/_tryOnce';
    $padCallPHP = include PAD . 'try/try.php';

    if ( $padInfo )
      include PAD . 'events/callEnd.php';

  }

  include PAD . 'call/_exit.php';

 ?>
