<?php

  include 'inits.php';

  $padOpts = array_merge ( $padDataDefaultStart, $padOpts );
  $padOpts = array_merge ( $padOpts, $padDataDefaultEnd   );

  $padVal = padVarOpts ($padVal, $padOpts);

  return include 'exits.php';

?>