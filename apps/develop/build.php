<?php

  include APP . 'clean.php';

  padDeleteDataDir ( DATA . 'reference'  );
  padDeleteDataDir ( DATA . 'regression' );
  padDeleteDataDir ( DATA . 'dumps'      );
  padDeleteDataDir ( DATA . 'temp'       );
  padDeleteDataDir ( DATA . 'examples'   );

  getRegression ( '&padExamples&padReference' );

  padRestart ( 'errors2' );

?>