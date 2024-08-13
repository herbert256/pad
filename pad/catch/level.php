<?php

  include pad . 'catch/catch.php';
  
  $pad--;

  padResetLvl ($pad+1);

  if ( $pad >= 0 )
    padPad ( '' );

?>