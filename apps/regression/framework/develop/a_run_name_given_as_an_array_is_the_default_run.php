<?php

  // ?page&padCoverage=run and ?page&padRecord=store name the run a local request is kept
  // in. Written as an array - padCoverage[]=1 - the name went through string functions as
  // it came and the request answered 500. Asked here directly: a request that asked would
  // be recorded in the default run, every suite run.

  $answer = 'named ' . padCoverageName ( [ 'x' ] ) . ', ' . padCoverageName ( 'my run!' );

?>
