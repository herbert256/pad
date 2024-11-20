<?php
  
  
  function padEvalOpr ( &$result, $myself, $start=0, $end=PHP_INT_MAX ) {

    padEvalType ( $result, $myself, $start, $end );

    foreach ( padEval_precedence as $now ) {

      $f = $b = -1;
      
      foreach ( $result as $k => $t ) { 

        if ( $k < $start ) continue;
        if ( $k > $end   ) break;

        if ( $b >= $start and $result[$b][1] == 'OPR' and $result[$b][0] == $now )
          if ( in_array ( $result[$b][0], padEval_one ) and $result[$k][1] == 'VAL' )
            return include 'eval/actions/single.php';
          elseif ( in_array ( $result[$b][0], padEval_one ) and $result[$k][1] == 'OPR' )
            return include 'eval/actions/singleRight.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $result[$k][1] == 'VAL' )
            return include 'eval/actions/double.php';
          elseif ( ( $f == -1 or $result[$f][1] <> 'VAL' ) and $result[$k][1] == 'VAL' )
            return include 'eval/actions/doubleLeft.php';
          elseif ( $f >= $start and $result[$f][1] == 'VAL' and $result[$k][1] == 'OPR' )
            return include 'eval/actions/doubleRight.php';

        $f = $b;
        $b = $k;
  
      }

    }

  }
 
 
?>