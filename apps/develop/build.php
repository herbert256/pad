<?php

  include APP . 'clean.php';

  padDeleteDataDir ( DAT . 'reference'  );
  padDeleteDataDir ( DAT . 'regression' );
  padDeleteDataDir ( DAT . 'dumps'      );
  padDeleteDataDir ( DAT . 'temp'       );
  padDeleteDataDir ( DAT . 'examples'   );

  getRegression ( '&padExamples&padReference' );

  padRestart ( 'errors2' );

?>