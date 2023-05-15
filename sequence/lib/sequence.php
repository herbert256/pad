<?php


  function padSeqAction ( $sequence1, $action, $sequence2 ) {

    $padSeqResult      = $sequence1;
    $padSeqCnt         = 0;
    $padSeqActionValue = $action;
    $padSeqActionName  = $action;
    
    $padSeqStore [$action] = $sequence2;

    return include pad . "sequence/actions/$action.php";  

  }


  function padSeqRandom ( $min, $max ) {

    $rand = rand ( $min, $max ); 

    if ( $rand < $min )
      $rand = $min;

    if ( $rand > $max )
      $rand = $max;

    return $rand;

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

    $GLOBALS ["padSeqParm"] = $value;
    $GLOBALS ["padSeq_$name"] = $value;

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
      return array_slice ($array, $count);
    else
      return array_slice ($array, 0, $count*-1);

  }

  function padSeqGetCnt ( $first, $second, $third ) {

    global $pad, $padPrm, $padSeqParm;

    if ( isset($padPrm [$pad][$first])      and $padPrm [$pad][$first]  !== TRUE and is_numeric($padPrm [$pad][$first]) )
 
      return $padPrm [$pad][$first];
 
    elseif ( isset($padPrm [$pad][$second]) and $padPrm [$pad][$second] !== TRUE and is_numeric($padPrm [$pad][$second]) )
 
      return $padPrm [$pad][$second];
 
    elseif ( isset($padPrm [$pad][$third])  and $padPrm [$pad][$third]  !== TRUE and is_numeric($padPrm [$pad][$third]) )
 
      return $padPrm [$pad][$third];
 
    elseif (                                    $padSeqParm !== TRUE           and is_numeric($padSeqParm) )
 
      return $padSeqParm;
 
    else
 
      return 1;

  }

?>