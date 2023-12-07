<?php

  if ( padXref ) 
    include pad . 'info/types/xref/items/setup.php';

  $padBetween   = 'PAD';
  $padWords     = [];
  $padWords [0] = 'PAD';

  $padTypeCheck  = 'PAD';
  $padTypeResult = 'internal';
  $padTypeGiven  = FALSE;
  $padPairSet    = FALSE;
  $padBaseSet    = '';
  $padPrmTypeSet = 'none';

  include pad . 'level/setup.php';

?>