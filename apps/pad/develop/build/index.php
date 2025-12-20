<?php

  $build = 1;

  fileDeleteDir ( 'regression' );
  fileDeleteDir ( 'reference'  );

  $sequence = 0; include APP . 'regression/regression.php';
  $sequence = 1; include APP . 'regression/regression.php';

  padRedirect ( "regression" );

?>
