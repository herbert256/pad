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


  function padSeqMakeType ( $check ) {

    if     ( file_exists ( "$check/function.php") ) return 'function';
    elseif ( file_exists ( "$check/make.php")     ) return 'make';
    elseif ( file_exists ( "$check/loop.php")     ) return 'loop';
    else                                            return '';

  }


  function padSeqFilterType ( $check ) {

    if     ( file_exists ( "$check/bool.php")     ) return 'bool';
    elseif ( file_exists ( "$check/order.php")    ) return 'order';
    elseif ( file_exists ( "$check/fixed.php")    ) return 'fixed';
    elseif ( file_exists ( "$check/jump.php")     ) return 'jump';
    elseif ( file_exists ( "$check/function.php") ) return 'function';
    elseif ( file_exists ( "$check/loop.php")     ) return 'loop';
    elseif ( file_exists ( "$check/make.php")     ) return 'make';
    elseif ( file_exists ( "$check/filter.php")   ) return 'filter';
    else                                            return 'none';

  }


  function padSeqAction ( $sequence1, $action, $sequence2 ) {

    $padSeqResult      = $sequence1;
    $padSeqCnt         = 0;
    $padSeqActionValue = $action;
    $padSeqActionName  = $action;
    
    $padSeqStore [$action] = $sequence2;

    return include pad . "sequence/actions/$action.php";  

  }


  function padSeqReverse ( $x ) {

   $rev = 0;
    while ($x > 0) {
      $rev = ($rev  * 10) + $x % 10;
      $x = (int)($x / 10);
    }
    return $rev;

  }

  function padSeqSet ( $name, $value ) {

    $name = ucfirst ($name);

    $GLOBALS ["padSeqParm"]  = $value;
    $GLOBALS ["padSeq$name"] = $value;

    padDone ( $name );

  }


  function padSeqArrayAction ($action) {

    $array  = $GLOBALS ['padSeqActionValue'];
    $arrays = padExplode ($array, '|');

    $parms [] = $GLOBALS ['padSeqResult'];

    foreach ($arrays as $store)
      if ( $store !== TRUE )
        if ( isset($GLOBALS ['padSeqStore'] [$store]) )
          $parms [] = $GLOBALS ['padSeqStore'] [$store];
        else
          $parms [] = $store;

    return call_user_func_array ($action, $parms);

  }

  function padSeqTruncate ( $array, $side, $count ) {

    if ( $side == 'left' )
      return array_slice ( $array, $count );
    else
      return array_slice ( $array, 0, $count * -1 );

  }

  function padSeqGetCnt ( $first, $second, $third ) {

    global $pad, $padPrm, $padSeqParm;

    if ( isset($padPrm [$pad][$first])      and $padPrm [$pad][$first]  !== TRUE and is_numeric($padPrm [$pad][$first]) )
 
      return $padPrm [$pad][$first];
 
    elseif ( isset($padPrm [$pad][$second]) and $padPrm [$pad][$second] !== TRUE and is_numeric($padPrm [$pad][$second]) )
 
      return $padPrm [$pad][$second];
 
    elseif ( isset($padPrm [$pad][$third])  and $padPrm [$pad][$third]  !== TRUE and is_numeric($padPrm [$pad][$third]) )
 
      return $padPrm [$pad][$third];
 
    elseif (                                     $padSeqParm !== TRUE            and is_numeric($padSeqParm) )
 
      return $padSeqParm;
 
    else
 
      return 1;

  }

?>