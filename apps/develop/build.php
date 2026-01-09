<?php

  padDeleteDataDir ( DAT . 'reference'  );
  padDeleteDataDir ( DAT . 'regression' );
  padDeleteDataDir ( DAT . 'examples'   );

  include APP . 'clean.php';
  include APP . 'regression.php';

?>