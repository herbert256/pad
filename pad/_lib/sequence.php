<?php


  function pqShuffle ( &$array ) {

    $shuffled = [];
    $keys     = array_keys ( $array );

    shuffle ( $keys );

    foreach ( $keys as $key )
      $shuffled [$key] = $array [$key];

    $array = $shuffled;

  }


  function pqSeq ( $seq  ) {

    if ( $seq and file_exists ( "sequence/types/$seq" ) )
      return TRUE;
    else
      return FALSE;

  }

  function pqAction ( $action  ) {

    if ( $action and file_exists ( "sequence/actions/types/$action.php" ) )
      return TRUE;
    else
      return FALSE;

  }


  function pqDone ( $option, &$array ) {

    $key = array_search ( $option, $array );

    if ( $key === FALSE ) 
      return FALSE;

    unset ( $array [$key] );

    return TRUE;

  }


  function pqStore ( $check ) {

    return in_array ( $check, ['pull','fixed'] );

  }
  
  
  function pqPlay ( $check ) {

    return in_array ( $check, ['make','keep','remove','flag'] );

  }


  function pqRandom ( $array, $count=1, $order=0, $dups=0 ) {

    if  ( ! is_array ( $array ) or ! count ( $array ) )
      return [];

    $out  = [];
    $keys = [];

    if ( $dups or $count > count ( $array ) ) {
      
      for ( $i=1; $i <= $count; $i++ )
        $keys [] = array_rand ( $array ) ;

      if ( $order ) {

        $dups = array_count_values ( $keys ); 
        $keys = []; 

        foreach ( $array as $k => $v )
          if ( isset ( $dups [$k] ) )
            $keys = array_merge ( $keys, array_fill ( 0, $dups [$k], $k ) );

      }

    } else {

      if ( $count == 1 )
        $keys = [ 0 => array_rand ( $array ) ];
      else 
        $keys = array_rand ( $array, $count );

      if ( ! $order  )
        pqShuffle ( $keys );

    }

    foreach ( $keys as $k )
      $out [$k] = $array [$k];
    
    return $out;

  }


  function pqCorrectParms (&$pqPrm1, &$pqPrm2, &$pqPrm3) {

    if ( str_contains ( $pqPrm1, '|' ) and ! $pqPrm2 ) {

      $padTmp = padExplode ( $pqPrm1, '|', 2 );

      $pqPrm1 = $padTmp [0];
      $pqPrm2 = $padTmp [1];

    }

    if ( str_contains ( $pqPrm1, '|' ) and ! $pqPrm3 ) {

      $padTmp = padExplode ( $pqPrm1, '|', 2 );

      $pqPrm1 = $padTmp [0];
      $pqPrm3 = $padTmp [1];

    }

    if ( str_contains ( $pqPrm2, '|' ) and ! $pqPrm3 ) {
      $padTmp = padExplode ( $pqPrm2, '|', 2 );
      $pqPrm2 = $padTmp [0];
      $pqPrm3 = $padTmp [1];

    }    

  }


  function pqRandomLy ( $for, $start, $end, $inc ) {

    if ( is_array ( $for ) and count ( $for ) )
      return $for [array_rand($for)];

    $loop = rand ( $start, $end );

    if ( $inc <> 1 ) {
      $done = $loop - $start;
      $loop = $start + round ( $done / $inc ) * $inc;
      if ( $loop > $end ) 
        $loop = $end;
    }

    return $loop;

  }


  function pqBuild ( $check, $for='' ) {

    if ( $check == 'pull' )
      return 'fixed';

    if ( $for == 'keep' or $for == 'remove' or $for == 'flag' )
      return 'bool';

    if ( file_exists ( "sequence/types/$check/$for.php" ) ) 
      return $for;
    
    if     ( file_exists ( "sequence/types/$check/loop.php")     ) return 'loop';
    elseif ( file_exists ( "sequence/types/$check/make.php")     ) return 'make';
    elseif ( file_exists ( "sequence/types/$check/function.php") ) return 'function';
    elseif ( file_exists ( "sequence/types/$check/bool.php")     ) return 'bool';
    elseif ( file_exists ( "sequence/types/$check/order.php")    ) return 'order';
    elseif ( file_exists ( "sequence/types/$check/fixed.php")    ) return 'fixed';
    else                                                           return 'bool';

  }


  function pqEvalAction ( $seq1, $action, $seq2 ) {

    $pqResult          = $seq1;
    $pqActionList [0]  = $action;
    $pqActionName      = $action;
    $pqStore [$action] = $seq2;

    return include "sequence/actions/types/$action.php";  

  }


  function padTypeReverse ( $x ) {

   $rev = 0;
    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int)($x / 10);
    }
    return $rev;

  }


  function pqTruncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }


  function padHandTruncate ( $array, $side, $count ) {

    return pqTruncate ( $array, $side, $count );

  }


?>