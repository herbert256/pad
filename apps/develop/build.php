<?php

  include APP . 'clean.php';

  padDeleteDataDir ( DATA . 'reference'  );
  padDeleteDataDir ( DATA . 'regression' );
  padDeleteDataDir ( DATA . 'dumps'      );
  padDeleteDataDir ( DATA . 'temp'       );
  padDeleteDataDir ( DATA . 'examples'   );
  padDeleteDataDir ( DATA . 'suites'     );

  padCurl ( $padHost . "regression/?build&go=1" );

  padRestart ( 'errors2' );

?>