<?php

  include APP . 'clean.php';

  padDeleteDataDir ( DATA . 'reference'  );
  padDeleteDataDir ( DATA . 'regression' );
  padDeleteDataDir ( DATA . 'dumps'      );
  padDeleteDataDir ( DATA . 'temp'       );
  padDeleteDataDir ( DATA . 'examples'   );
  padDeleteDataDir ( DATA . 'suites'     );

  getRegression ( '&padExamples&padReference' );

  padRestart ( 'errors2' );

?>