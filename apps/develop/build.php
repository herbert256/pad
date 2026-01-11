<?php

  padDeleteDataDir ( DAT . 'reference'  );
  padDeleteDataDir ( DAT . 'regression' );
  padDeleteDataDir ( DAT . 'dumps'      );
  padDeleteDataDir ( DAT . 'examples'   );

  include APP . 'clean.php';
  include APP . 'regression.php';
  include APP . 'reference.php';
  include APP . 'examples.php';

  padRedirect ( 'errors' );

?>