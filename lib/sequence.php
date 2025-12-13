<?php

  function pqActionArray ( &$parms ) {

    $pqFirst = array_shift ( $parms );

    if ( ! is_array ( $pqFirst ) ) {

      return $pqFirst;
    
    } elseif ( is_array ( $pqFirst ) ) {

      $pqFirst = array_values ( $pqFirst );

      if ( count ( $pqFirst ) == 1 and is_array ( $pqFirst [0] ) )
        $pqFirst = $pqFirst [0];

    }

    return $pqFirst;

  }
   
   
  function pqRandomParm ( &$parm ) {

    if ( str_contains ( $parm, '..' ) ) {

      padSplit ( '..', $parm, $from, $to );

      if ( is_numeric ( $from ) and is_numeric ( $to ) )
        $parm = mt_rand ( $from, $to );

    }

  }


  function pqRandomParm3 ( $parm ) {

    padSplit ( '...', $parm, $from, $to );

    if ( is_numeric ( $from ) and is_numeric ( $to ) )
      return mt_rand ( $from, $to );
    else
      return $parm;

  }


  function pqShuffle ( &$array ) {

    $shuffled = [];
    $keys     = array_keys ( $array );

    shuffle ( $keys );

    foreach ( $keys as $key )
      $shuffled [$key] = $array [$key];

    $array = $shuffled;

  }


  function pqSeq ( $seq  ) {

    if ( $seq and file_exists ( PT . "$seq" ) )
      return TRUE;
    else
      return FALSE;

  }



  function pqArray ( $sequence, $parm='', $options='') {

    if ( $parm)
      if ( $parm === TRUE )
        $parm = '';
      else
        $parm = ( $parm ) ? "=$parm" : '';

    if ( $options )
      $options = ", $options";

    return explode ( ',', padCode ( "{sequence $sequence$parm$options}{\$sequence},{/sequence}" ) );

  }


  function pqAction ( $action  ) {

    if ( $action and file_exists ( PQ . "actions/types/$action.php" ) )
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

    return in_array ( $check, ['pull','fixed','build','given'] );

  }
  
  
  function pqPlay ( $check ) {

    return in_array ( $check, ['make','keep','remove','flag'] );

  }


  function pqRandom ( $array, $count=1, $order=0, $dups=0, $once=0 ) {

    if  ( ! is_array ( $array ) or ! count ( $array ) )
      return [];

    if ( ! $count or $count === TRUE )
      $count = count ( $array );
    
    if ( $dups or $count > count ( $array ) or $once ) 
      return pqRandomDups ( $array, $count, $order, $once );
    else
      return pqRandomKeys ( $array, $count, $order );
      
  }


  function pqRandomKeys ( $array, $count, $order ) {

    if ( $count == 1 )
      $keys = [ 0 => array_rand ( $array ) ];
    else 
      $keys = array_rand ( $array, $count );

    if ( ! $order  )
      shuffle ( $keys );

    foreach ( $keys as $k )
      $out [$k] = $array [$k];

    return $out;

  }
    

  function pqRandomDups ( $array, $count, $order, $once ) {

    if ( $once ) {
      $keys = array_keys ( $array );
      $count = $count - count ( $array );
    }

    for ( $i=1; $i <= $count; $i++ )
      $keys [] = array_rand ( $array ) ;

    if ( ! $order ) 

      shuffle ( $keys );

    else {

      $dups = array_count_values ( $keys ); 
      $keys = []; 

      foreach ( $array as $k => $v )
        if ( isset ( $dups [$k] ) )
          for ($i=0; $i < $dups [$k]; $i++) 
            $keys [] = $k;  

    }

    foreach ( $keys as $k )
      if ( isset ( $out [$k] ) )
        $out [] = $array [$k];
      else
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
      return 'check';

    if ( file_exists ( PT . "$check/$for.php" ) ) 
      return $for;
    
    if     ( file_exists ( PT . "$check/loop.php")      ) return 'loop';
    elseif ( file_exists ( PT . "$check/make.php")      ) return 'make';
    elseif ( file_exists ( PT . "$check/function.php")  ) return 'function';
    elseif ( file_exists ( PT . "$check/bool.php")      ) return 'bool';
    elseif ( file_exists ( PT . "$check/order.php")     ) return 'order';
    elseif ( file_exists ( PT . "$check/build.php")     ) return 'build';
    elseif ( file_exists ( PT . "$check/fixed.php")     ) return 'fixed';
    elseif ( file_exists ( PT . "$check/generated.php") ) return 'generated';
    else                                                            return 'unknown';

  }


  function padTypeReverse ( $x ) {

   $rev = 0;

    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int) ($x / 10);
    }
   
    return $rev;

  }


  function pqTruncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }


?>