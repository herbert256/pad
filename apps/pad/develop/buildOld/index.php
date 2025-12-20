<?php

  set_time_limit ( 300 );

  include APP . 'build/clean.php';
  include APP . 'build/sequence.php';
  include APP . 'build/regression.php';

  padRedirect ( 'regression' );

?>
