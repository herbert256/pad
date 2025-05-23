<?php

  set_time_limit ( 300 );

  include APP . 'develop/clean.php';
  include APP . 'develop/build/sequence.php';
  include APP . 'develop/build/regression.php';

  padRedirect ( 'develop/regression' );

?>