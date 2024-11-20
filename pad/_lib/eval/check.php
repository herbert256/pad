<?php


  function padEvalCheck ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    $first = array_key_first ($result);
    $last  = array_key_last  ($result);

    if ( count($result) == 1 and $result[$first][1] == 'OPR' ) {
      $b = $first;
      return include 'eval/actions/singleRight.php';
    }

    if ( count($result) == 2 ) {

      if ( $result[$first][1] == 'OPR' and $result[$last][1] == 'VAL' ) {
        $b = $first;
        $k = $last;
        return include 'eval/actions/doubleLeft.php';
      }

      if ( $result[$first][1] == 'VAL' and $result[$last][1] == 'OPR' ) {
        $b = $last;
        $f = $first;
        return include 'eval/actions/doubleRight.php';
      }

    } 

  }


?>