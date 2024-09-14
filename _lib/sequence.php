<?php


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

    if ( $for == 'keep' or $for == 'remove' )
      return 'check';

    $check = "/pad/sequence/types/$check";

    if ( $for == 'make' ) if ( file_exists ( "$check/make.php" ) ) return 'make';
    if ( $for == 'loop' ) if ( file_exists ( "$check/loop.php" ) ) return 'loop';
    
    if     ( file_exists ( "$check/loop.php")     ) return 'loop';
    elseif ( file_exists ( "$check/make.php")     ) return 'make';
    elseif ( file_exists ( "$check/function.php") ) return 'function';
    elseif ( file_exists ( "$check/bool.php")     ) return 'bool';
    elseif ( file_exists ( "$check/jump.php")     ) return 'jump';
    elseif ( file_exists ( "$check/order.php")    ) return 'order';
    elseif ( file_exists ( "$check/fixed.php")    ) return 'fixed';
    else                                            return '';

  }


  function padSeqAction ( $seq1, $action, $seq2 ) {

    $padSeqResult      = $seq1;
    $padSeqActionList [0] = $action;
    $padSeqActionName  = $action;
    
    $padSeqStore [$action] = $seq2;

    return include "/pad/sequence/actions/types/$action.php";  

  }


  function padSeqReverse ( $x ) {

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


?>