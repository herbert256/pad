<?php

  if ( $padXref ) 
    include pad . 'xref/items/setup.php';

  $padBetween   = 'PAD';
  $padWords     = [];
  $padWords [0] = 'PAD';

  $padTypeCheck  = 'PAD';
  $padTypeResult = 'internal';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padTrueSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';

?>