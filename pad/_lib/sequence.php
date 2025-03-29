<?php


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


  function padSeqRandom ( $for, $start, $end, $inc ) {

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

    if ( $for == 'keep' or $for == 'remove' )
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