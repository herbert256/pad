<?php

  $GLOBALS ['catch'] .= '-L';

  include pad . 'error/catch/_catch.php';
  
  $pad--;

  padResetLvl ($pad+1);

  if ( $pad >= 0 )
    padPad ( '' );

?>