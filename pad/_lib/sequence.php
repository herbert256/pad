<?php


  function padSeqDone ( $option, &$array ) {

    $key = array_search ( $option, $array );

    if ( $key === FALSE ) 
      return FALSE;

    unset ( $array [$key] );

    return TRUE;

  }


  function padSeqParm ( $option ) {

    if ( $option and $option !== TRUE and ! $GLOBALS ['padSeqParm'] ) 
      return $option;
    else
      return $GLOBALS ['padSeqParm'];

  }


  function padSeqSequence ( &$name, &$parm ) {

    global $padSeqStore, $padSeqDone, $padSeqPrefix, $padSeqTag, $padSeqParm;
    global $padSeqFirst, $padSeqSecond, $padSeqFirstParm, $padSeqSecondParm;

    if ( $padSeqPrefix and file_exists ( "sequence/types/$padSeqPrefix" ) ) {

      $name = $padSeqPrefix;
    
    } elseif ( file_exists ( "sequence//types/$padSeqTag" ) ) {

      $name = $padSeqTag;
    
    } elseif ( $padSeqFirst and file_exists ( "sequence/types/$padSeqFirst" ) ) {

      $padSeqDone [] = $padSeqFirst; 

      $name = $padSeqFirst;
      $parm = $padSeqFirstParm;

    } elseif ( $padSeqSecond and file_exists ( "sequence/types/$padSeqSecond" ) ) {

      $padSeqDone [] = $padSeqSecond; 

      $name = $padSeqSecond;
      $parm = $padSeqSecondParm;
    
    }

  }


  function padSeqAction ( &$name, &$parm ) {

    global $padSeqStore, $padSeqDone, $padSeqPrefix, $padSeqTag, $padSeqParm;
    global $padSeqFirst, $padSeqSecond, $padSeqFirstParm, $padSeqSecondParm;

    if ( $padSeqPrefix and file_exists ( "sequence/actions/types/$padSeqPrefix.php" ) ) {

      $name = $padSeqPrefix;
    
    } elseif ( file_exists ( "sequence/actions/types/$padSeqTag.php" ) ) {

      $name = $padSeqTag;
    
    } elseif ( $padSeqFirst and file_exists ( "sequence/actions/types/$padSeqFirst.php" ) ) {

      $padSeqDone [] = $padSeqFirst; 

      $name = $padSeqFirst;
      $parm = $padSeqFirstParm;

    } elseif ( $padSeqSecond and file_exists ( "sequence/actions/types/$padSeqSecond.php" ) ) {

      $padSeqDone [] = $padSeqSecond; 

      $name = $padSeqSecond;
      $parm = $padSeqSecondParm;
    
    } 

  }

  function padSeqPull ( &$pull ) {

    global $padSeqPullName, $padLastPush, $padSeqStore, $padSeqFirst, $padSeqSecond, $padSeqDone, $padSeqPrefix, $padSeqTag;

    if ( $padSeqFirst and isset ( $padSeqStore [ $padSeqFirst ] ) ) {

      $padSeqDone [] = $padSeqFirst; 
      $pull = $padSeqFirst;

    } elseif ( $padSeqSecond and isset ( $padSeqStore [ $padSeqSecond ] ) ) {

      $padSeqDone [] = $padSeqSecond; 
      $pull = $padSeqSecond;
    
    } elseif ( $padSeqSecond and isset ( $padSeqStore [ $padSeqPrefix ] ) ) {

      $pull = $padSeqPrefix;
    
    } elseif ( isset ( $padSeqStore [ $padSeqTag ] ) ) {

      $pull = $padSeqTag;
    
    } elseif ( $padSeqPullName ) {

      $pull           = $padSeqPullName;
      $padSeqPullName = '';    

    } elseif ( $padLastPush ) {

      $pull = $padLastPush;

    } 

  }


  function padSeqStore ( $check ) {

    return in_array ( $check, ['start','store','pull','fixed'] );

  }
  
  
  function padSeqPlay ( $check ) {

    return in_array ( $check, ['make','keep','remove','flag'] );

  }


  function padSeqRandom ( $array, $count=1, $order=0, $dups=0 ) {

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
        shuffle ( $keys );

    }

    foreach ( $keys as $k )
      $out [] = $array [ $k ];
    
    return $out;

  }


  function padSeqCorrectParms (&$padSeqPrm1, &$padSeqPrm2, &$padSeqPrm3) {

    if ( str_contains ( $padSeqPrm1, '|' ) and ! $padSeqPrm2 ) {

      $padTmp = padExplode ( $padSeqPrm1, '|', 2 );

      $padSeqPrm1 = $padTmp [0];
      $padSeqPrm2 = $padTmp [1];

    }

    if ( str_contains ( $padSeqPrm1, '|' ) and ! $padSeqPrm3 ) {

      $padTmp = padExplode ( $padSeqPrm1, '|', 2 );

      $padSeqPrm1 = $padTmp [0];
      $padSeqPrm3 = $padTmp [1];

    }

    if ( str_contains ( $padSeqPrm2, '|' ) and ! $padSeqPrm3 ) {
      $padTmp = padExplode ( $padSeqPrm2, '|', 2 );
      $padSeqPrm2 = $padTmp [0];
      $padSeqPrm3 = $padTmp [1];

    }    

  }


  function padSeqRandomLy ( $for, $start, $end, $inc ) {

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


  function padSeqBuild ( $check, $for='' ) {

    if ( $check == 'pull' )
      return 'fixed';

    if ( $for == 'keep' or $for == 'remove' or $for == 'flag' )
      return 'check';

    if ( file_exists ( "sequence/types/$check/$for.php" ) ) 
      return $for;
    
    if     ( file_exists ( "sequence/types/$check/loop.php")     ) return 'loop';
    elseif ( file_exists ( "sequence/types/$check/make.php")     ) return 'make';
    elseif ( file_exists ( "sequence/types/$check/function.php") ) return 'function';
    elseif ( file_exists ( "sequence/types/$check/bool.php")     ) return 'bool';
    elseif ( file_exists ( "sequence/types/$check/jump.php")     ) return 'jump';
    elseif ( file_exists ( "sequence/types/$check/order.php")    ) return 'order';
    elseif ( file_exists ( "sequence/types/$check/fixed.php")    ) return 'fixed';
    elseif ( file_exists ( "sequence/types/$check/check.php")    ) return 'check';
    else                                                           return '';

  }


  function padSeqEvalAction ( $seq1, $action, $seq2 ) {

    $padSeqResult          = $seq1;
    $padSeqActionList [0]  = $action;
    $padSeqActionName      = $action;
    $padSeqStore [$action] = $seq2;

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


  function padSeqTruncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }


  function padHandTruncate ( $array, $side, $count ) {

    return padSeqTruncate ( $array, $side, $count );

  }


?>