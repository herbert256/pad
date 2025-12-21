<?php

  $build = 1;

  fileDeleteDir ( 'regression' );
  fileDeleteDir ( 'reference'  );

  include APP . 'develop/build/regression.php';

  padRedirect ( "regression" );

?>