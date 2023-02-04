<?php

  $padBase  [$pad] = '{after}' . $padAfter [$pad] . '{/after}';
  $padData  [$pad] = padDefaultData ();

  $padAfter [$pad] = '';

  reset ( $padData [$pad] );
  include PAD . 'occurrence/start.php';

?>