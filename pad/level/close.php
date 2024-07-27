<?php

  $padClosePad = padFunction ( $padOpt [$pad] [0] );

  if ( $padClosePad == $padOpt [$pad] [0] )
    return;

  $padBetween = $padTag [$pad] . ' ' . padFunction ( $padClosePad );

  include pad . 'level/between.php';

?>