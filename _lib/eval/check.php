<?php


  function padEvalCheck ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    global $padEval, $padEvalCnt;

    $first = array_key_first ($padEval [$padEvalCnt]);
    $last  = array_key_last  ($padEval [$padEvalCnt]);

    if ( count($padEval [$padEvalCnt]) == 1 and $padEval [$padEvalCnt][$first][1] == 'OPR' ) {
      $b = $first;
      return include '/pad/eval/actions/singleRight.php';
    }

    if ( count($padEval [$padEvalCnt]) == 2 ) {

      if ( $padEval [$padEvalCnt][$first][1] == 'OPR' and $padEval [$padEvalCnt][$last][1] == 'VAL' ) {
        $b = $first;
        $k = $last;
        return include '/pad/eval/actions/doubleLeft.php';
      }

      if ( $padEval [$padEvalCnt][$first][1] == 'VAL' and $padEval [$padEvalCnt][$last][1] == 'OPR' ) {
        $b = $last;
        $f = $first;
        return include '/pad/eval/actions/doubleRight.php';
      }

    } 

  }


?>