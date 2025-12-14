<?php


  /**
   * Splits token array by pipe operators.
   *
   * Separates the token array into segments divided by pipe tokens.
   * Each segment becomes an element in the output pipes array.
   *
   * @param array $result The token array to split.
   * @param array &$pipes Output array of pipe segments.
   *
   * @return void
   */
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