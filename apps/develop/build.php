<?php

  $build = 1;

  fileDeleteDataDir ( APPS . 'reference/DATA/'  );
  fileDeleteDataDir ( APPS . 'regression/DATA/' );

  include APP . 'clean.php';
  include APP . 'regression.php';

  padRedirect ( "regression" );

?>