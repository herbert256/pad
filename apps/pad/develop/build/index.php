<?php

  $build = 1;

  fileDeleteDir ( 'regression' );
  fileDeleteDir ( 'reference'  );
  fileDeleteDir ( 'examples'  );

  include APP . 'develop/build/regression.php';

  padRedirect ( "regression" );

?>