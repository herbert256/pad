<?php

  function padEvalPipes ( $result, &$pipes ) {

    $pipe  = 0;
    $pipes = [];

    foreach ( $result as $key => $val )
      if ( $val [1] == 'pipe' )
        $pipe++;
      else
        $pipes [$pipe] [$key] = $val;

  }

?>
