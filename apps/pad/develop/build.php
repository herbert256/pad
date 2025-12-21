<?php

  $build = 1;

  fileDeleteDir ( 'regression' );
  fileDeleteDir ( 'reference'  );

  include APP . 'develop/clean/regression.php';
  include APP . 'develop/regression.php';

  padRedirect ( "regression" );

?>